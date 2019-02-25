<?php

if (!function_exists("env")) {
  function env($name, $default = false) {
      $value = getenv($name);
      if ($value === false) {
          return $default;
      } else {
          return $value;
      }
  }
}

return [
  "debug" => env("WEBHOOK_DEBUG") !== false,
  "github" => [
    "secret" => env("WEBHOOK_GITHUB_SECRET", false)
  ],
  "travis" => [
    "token" => env("WEBHOOK_TRAVIS_TOKEN", false)
  ],
  "slack" => [
    "url" => env("WEBHOOK_SLACK_URL", false)
  ]
];
