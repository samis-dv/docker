<?php

ini_set("memory_limit", "4096M");
set_time_limit(0);

function write($line) {
    echo $line . "\n";
    ob_flush();
}

$config = require("/var/www/html/config/api.php");

if ($config["database"]["type"] !== "mysql") {
    write("Bootstrap script only supports MySQL backends.");
    exit(1);
}

write("Connecting to the database...");

$retries = getenv("DATABASE_RETRY");
if (!$retries) {
    $retries = 30;
} else {
    $retries = intval($retries);
}

while ($retries-- > 0) {
    try {
        $dsn = "mysql:" .
            "host=" . $config["database"]["host"] . ";" .
            "port=" . $config["database"]["port"] . ";";
        $connection = new PDO($dsn,
            $config["database"]["username"],
            $config["database"]["password"], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
    } catch (PDOException $e) {
        $connection = false;
        write("Database connection failed. If this is the first run, MySQL might be still initializing.");
        write("> ". $e->getMessage());
        sleep(5);
        continue;
    }
    break;
}

if (!$connection) {
    write("Cannot connect to the database server.");
    exit(1);
} else {
    write("Connected to the database instance.");
}

// Select database

try {
    $statement = $connection->prepare("USE " . $config["database"]["name"]);
    $statement->execute();
} catch (PDOException $e) {
    write("Failed to select database.");
    write($e->getMessage());
    exit(1);
}

// Initialize if necessary

$shouldInstall = false;

try {
    $statement = $connection->prepare("SHOW TABLES");
    $statement->execute();
    $tables = $statement->fetchAll(PDO::FETCH_CLASS);
    if (sizeof($tables) <= 0) {
        $shouldInstall = true;
    }
} catch (PDOException $e) {
    write("Failed to list database tables.");
    write($e->getMessage());
    exit(1);
}

// Initialize

if ($shouldInstall) {

    write("Installing database...");
    sleep(3);
    passthru("/var/www/html/bin/directus install:database");

    # Why not working?
    write("Installing data...");
    sleep(3);
    passthru("/var/www/html/bin/directus install:install -e \"admin@admin.com\" -p \"admin\" -t \"Directus\"");

}
