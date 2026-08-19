<?php

// Turn off reporting uncaught exception for mysqli to prevent raw stack trace crash on screen
mysqli_report(MYSQLI_REPORT_OFF);

$DB_HOST     = getenv('DB_HOST') ?: (isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : "localhost");
$DB_NAME     = getenv('DB_NAME') ?: (isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : "worker");
$DB_USER     = getenv('DB_USER') ?: (isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : "root");
$DB_PASSWORD = getenv('DB_PASSWORD') !== false ? getenv('DB_PASSWORD') : (isset($_ENV['DB_PASSWORD']) ? $_ENV['DB_PASSWORD'] : "");

if (!defined('DB_HOST'))     define('DB_HOST', $DB_HOST);
if (!defined('DB_NAME'))     define('DB_NAME', $DB_NAME);
if (!defined('DB_USER'))     define('DB_USER', $DB_USER);
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', $DB_PASSWORD);

$servername = $DB_HOST;
$username   = $DB_USER;
$password   = $DB_PASSWORD;
$dbname     = $DB_NAME;

?>