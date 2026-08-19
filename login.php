<?php
session_start();
$role = isset($_GET['role']) && in_array($_GET['role'], ['customer', 'worker', 'admin']) ? $_GET['role'] : 'customer';
header("Location: index.php?view=login&role=" . urlencode($role));
exit();
?>
