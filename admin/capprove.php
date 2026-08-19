<?php	
include("dbcon.php");
$bid = $_GET['id'];
$sql = "update cregistration set cstatus=2where  crid=".$bid;

$conn->query($sql);

 header('location:viewcustomers.php');



?>