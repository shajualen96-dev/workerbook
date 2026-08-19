<?php 

if (!defined('BASE_PATH')) {
    define("BASE_PATH", dirname(__DIR__) . '/');
}

if (!defined('BASE_URL')) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define("BASE_URL", "$protocol://$host/");
}

// set your timezone here
date_default_timezone_set('asia/kolkata');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(BASE_PATH . 'config/database.php'); 
require_once(BASE_PATH . 'classes/database.php'); 
require_once(BASE_PATH . 'classes/FormAssist.class.php'); 
require_once(BASE_PATH . 'classes/FormValidator.class.php'); 
require_once(BASE_PATH . 'classes/DataAccess.class.php'); 
?>