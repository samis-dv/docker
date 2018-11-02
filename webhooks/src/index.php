<?php

/**
 * Configuration
 */

$config = require("./config.php");

/**
 * Helper
 */

function logs($str) {
    file_put_contents("php://stderr", $str . "\n");
}

function respond($status, $payload) {
    header("Content-Type: application/json", true);;
    echo json_encode($payload);
    die;
}

/**
 * Webhook
 */

if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    respond(400, ["error" => "invalid_request"]);
};

// Read data
$input = file_get_contents("php://input");
$json = json_decode($input, true);
$token = false;

// Check signature presence
if (!isset($_SERVER["HTTP_X_HUB_SIGNATURE"])) {
    respond(400, ["error" => "missing_signature"]);
} else {
    list($algorithm, $token) = explode("=", $_SERVER["HTTP_X_HUB_SIGNATURE"], 2) + array("", "");
}

// Validate signature
if ($token !== hash_hmac($algorithm, $input, $config["secret"]) && !$config["debug"]) {
    respond(400, ["error" => "invalid_signature"]);
}

// Unknown project
$repository = strtolower($json["repository"]["full_name"]);
if (!isset($config["projects"][$repository])) {
    respond(400, ["error" => "unknown_project"]);
}

// We don't build drafts
if ($json["release"]["draft"]) {
    respond(200, ["status" => "draft_skipped"]);
}

// Version information
$tag = $json["release"]["tag_name"];
if (substr($tag, 0, 1) === "v") {
    $tag = substr($tag, 1);
}

// Project configs
$project = $config["projects"][$repository];
$builder = $project["builder"];
$travis = $project["travis"];

// Trigger payload
$travis_payload = json_encode([
    "request" => [
        "message" => "Release '" . $tag . "' created for project '" . $builder["name"] . "'",
        "branch" => isset($builder["branch"]) ? $builder["branch"] : "master",
        "config" => [
            "env" => [
                "PROJECT_NAME=" . $builder["name"],
                "PROJECT_TAG=" . $tag,
                "TARGET_TAG_SUFFIX=latest"
            ],
            "script" => isset($travis["script"]) ? $travis["script"] : [
                "chmod +x ./build.sh",
                "./build.sh",
            ],
        ],
    ],
]);

// Trigger post
$travis_curl = curl_init($travis["url"]);
curl_setopt($travis_curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($travis_curl, CURLOPT_POST, 1);
curl_setopt($travis_curl, CURLOPT_POSTFIELDS, $travis_payload);
curl_setopt($travis_curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($travis_curl, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Content-Length: " . strlen($travis_payload),
    "Accept: application/json",
    "Travis-API-Version: 3",
    "Authorization: token ". $travis["token"],
]);
$travis_result = curl_exec($travis_curl);
$travis_status = intval(curl_getinfo($travis_curl, CURLINFO_HTTP_CODE));
curl_close($travis_curl);

// Result
if ($travis_status < 200 || $travis_status >= 300) {
    logs("Trigger failed with status " . $travis_status . ", response:");
    logs($travis_result);
    respond(500, [
        "error" => "trigger_failed",
        "response" => $travis_result,
    ]);
}

respond(200, ["status" => "success"]);
