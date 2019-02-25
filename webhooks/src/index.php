<?php

/**
<!--\r\n[app]\r\nversion=7.0.18\r\n[api]\r\nversion=2.0.18\r\n[docker]\r\ntags=latest,7.0.18-rc.1\r\n -->
 */

/**
 * Errors
 */
class WebhookException extends Exception {
  protected $details = null;

  public function __construct($code, $message, $details = null) {
    $this->details = is_null($details) ? "Webhook Error: " . $message : $details;
    parent::__construct($message, $code, null);
  }

  public function getDetails() {
    return $this->details;
  }
}

/**
 * Log a line to output
 */
function writeLog($str) {
  if (!is_string($str)) {
    $str = json_encode($str, JSON_PRETTY_PRINT);
  }
  file_put_contents("php://stderr", $str . "\n");
}

/**
 * Sends the server response and exits
 */
function sendResponse($status, $payload) {
  header("Content-Type: application/json", true);
  echo json_encode($payload);
  die;
}

/**
 * Sends a message to a slack channel
 */
function sendSlackMessage($message, $config) {
  if ($config["url"] === false) {
    return false;
  }

  $resource = curl_init($config["url"]);
  curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($resource, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($resource, CURLOPT_POSTFIELDS, [
    "payload" => json_encode([
      "text" => $message
    ])
  ]);

  $result = curl_exec($resource);
  curl_close($resource);

  return $result;
}

/**
 * Validates the request and return the payload
 */
function getPayload($config, $debug) {
  if ($_SERVER['REQUEST_METHOD'] !== "POST") {
    throw new WebhookException(400, "invalid_request");
  }

  // Read data
  $input = file_get_contents("php://input");
  $payload = json_decode($input, true);

  // Check signature and event presence
  if (!isset($_SERVER["HTTP_X_HUB_SIGNATURE"])) {
    throw new WebhookException(400, "missing_signature");
  }

  if (!isset($_SERVER["HTTP_X_GITHUB_EVENT"])) {
    throw new WebhookException(400, "missing_event");
  }

  // Validate signature
  list($algorithm, $token) = explode("=", $_SERVER["HTTP_X_HUB_SIGNATURE"], 2) + array("", "");
  if ($token !== hash_hmac($algorithm, $input, $config["secret"]) && !$debug) {
    throw new WebhookException(400, "invalid_signature");
  }

  // Validate event
  $event = $_SERVER["HTTP_X_GITHUB_EVENT"];
  if ($event !== "release") {
    throw new WebhookException(400, "invalid_event");
  }

  if ($payload["action"] !== "published") {
    throw new WebhookException(400, "invalid_action");
  }

  return $payload;
}

/**
 * Triggers a travis pipeline
 */
function triggerPipeline($repo, $branch, $config, $environment) {
  // Trigger payload
  $payload = json_encode([
    "request" => [
      "message" => "Release '" . $tag . "' created",
      "branch" => $branch,
      "config" => [
        "env" => $environment,
      ],
    ],
  ]);

  // Trigger post
  $resource = curl_init("https://api.travis-ci.org/repo/" . $repo . "/requests");
  curl_setopt($resource, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($resource, CURLOPT_POST, 1);
  curl_setopt($resource, CURLOPT_POSTFIELDS, $payload);
  curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($resource, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Content-Length: " . strlen($payload),
    "Accept: application/json",
    "Travis-API-Version: 3",
    "Authorization: token ". $config["token"],
  ]);

  $result = curl_exec($resource);
  $response = intval(curl_getinfo($resource, CURLINFO_HTTP_CODE));
  curl_close($resource);

  // Result
  if ($response < 200 || $response >= 300) {
    throw new WebhookException(500, "trigger_failed",
      ":x: Trigger failed with status " . $response .
      ", response:\n```" . $result . "```"
    );
  }
}

/**
 * Main function
 */
function main() {
  // Load config
  if (getenv("IN_DOCKER") === false) {
    $config = require("./config.php");
  } else {
    $config = require("./config.docker.php");
  }

  try {
    $payload = getPayload($config["github"], $config["debug"]);
    $release = $payload["release"];
    $releaseName = $release["name"];
    $releaseTag = $release["tag_name"];
    $releaseBody = $release["body"];
    $repository = $payload["repository"];

    // Received
    sendSlackMessage(
      ":white_check_mark: Release *${releaseName}* created",
      $config["slack"]
    );

    // Match metadata
    if (!preg_match("/<!--([.\S\s]*)-->/m", $releaseBody, $matches)) {
      throw new WebhookException(400, "missing_metadata",
        ":x: Error: no metadata present in release body."
      );
    }

    if ($release["draft"]) {
      throw new WebhookException(400, "missing_metadata",
        ":white_check_mark: Draft skipped."
      );
    }

    // Parse metadata
    $metadata = parse_ini_string(trim($matches[1]), true);
    if (!isset($metadata["api"]) || !isset($metadata["api"]["version"])) {
      throw new WebhookException(400, "missing_metadata",
        ":x: Metadata doesn't contain api version"
      );
    }

    if (!isset($metadata["app"]) || !isset($metadata["app"]["version"])) {
      throw new WebhookException(400, "missing_metadata",
        ":x: Metadata doesn't contain app version"
      );
    }

    if (!isset($metadata["docker"]) || !isset($metadata["docker"]["tags"])) {
      $tags = [];
      if (!$release["prerelease"]) {
        $tags[] = "latest";
      }
      $tags[] = $releaseTag;
      $metadata["docker"] = [
        "tags" => implode($tags, ",")
      ];
    }

    // Normalize versions
    $appVersion = $metadata["app"]["version"];
    if ($appVersion[0] === "v") {
      $appVersion = substr($appVersion, 1);
    }

    $apiVersion = $metadata["api"]["version"];
    if ($apiVersion[0] === "v") {
      $apiVersion = substr($apiVersion, 1);
    }

    $dockerTags = explode(",", $metadata["docker"]["tags"]);
    $dockerTags = array_map("trim", $dockerTags);

    triggerPipeline($repository["full_name"], $releaseTag, $config["travis"], [
      "API_VERSION" => $apiVersion,
      "APP_VERSION" => $appVersion,
      "TAGS" => $dockerTags
    ]);

    // Received
    sendSlackMessage(
      ":heavy_check_mark: Build triggered.\n" .
      "```APP: " . $appVersion . "\n" .
      "API: " . $apiVersion . "\n" .
      "Tags:\n - " . implode("\n - ", $dockerTags) . "\n" .
      "```",
      $config["slack"]
    );

    sendResponse(200, [ "success" => true ]);
  } catch (WebhookException $e) {
    sendSlackMessage($e->getDetails(), $config["slack"]);
    sendResponse($e->getCode(), [ "error" => $e->getMessage() ]);
  } catch (Exception $e) {
    sendSlackMessage(
      "Unknown Exception (" . $e->getCode() . "): " .
      $e->getMessage(), $config["slack"]
    );
    sendResponse(500, [ "error" => "unknown_error", "message" => $e->getMessage() ]);
  }
}

main();
die;

$release = $json["release"];


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

// Environment variables
$environment = [
  "PROJECT_NAME" => $builder["project"],
  "RELEASE" => $tag,
];

// Do not update latest tag if it's a prerelease/rc
if (!$json["release"]["prerelease"]) {
  $environment["PROJECT_TAG_ALIASES"] = "latest";
}


respond(200, ["status" => "success"]);
