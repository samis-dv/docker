<?php

return [
    "debug" => true,
    "secret" => "somesecret",
    "projects" => [
        "user/repo" => [
            "builder" => [
                "name" => "someproject",
                "branch" => "somebranch",
            ],
            "travis" => [
                "url" => "https://api.travis-ci.org/repo/user%2Frepo/requests",
                "token" => "sometoken",
                "script" => [
                    "chmod +x ./build.sh",
                    "./build.sh",
                ]
            ],
        ],
    ],
];
