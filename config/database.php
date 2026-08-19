<?php

$DB_HOST     = "localhost";
$DB_NAME     = "worker";
$DB_USER     = "root";
$DB_PASSWORD = "";

if (!defined('DB_HOST'))     define('DB_HOST', $DB_HOST);
if (!defined('DB_NAME'))     define('DB_NAME', $DB_NAME);
if (!defined('DB_USER'))     define('DB_USER', $DB_USER);
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', $DB_PASSWORD);

$servername = $DB_HOST;
$username   = $DB_USER;
$password   = $DB_PASSWORD;
$dbname     = $DB_NAME;

?>