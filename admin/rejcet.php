<?php
require('../config/autoload.php');

$dao = new DataAccess();

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $data = array('wstatus' => 3);   // 3 = Rejected

    $condition = "wid=" . $id;

    $redirect = isset($_GET['ref']) && !empty($_GET['ref']) ? $_GET['ref'] : 'viewworkers.php';
    
    // Add separator if ref already has query parameters
    $connector = (strpos($redirect, '?') !== false) ? '&' : '?';

    if ($dao->update($data, 'wregistration', $condition)) {
        header("Location: " . $redirect . $connector . "msg=rejected");
        exit();
    } else {
        echo "Failed to reject worker.";
    }
}
?>