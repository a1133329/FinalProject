<?php
session_start();

$link = mysqli_connect('localhost', 'root', '', 'final_project');

if (!$link) {
    die("Fail to connect!");
}

if(!isset($_SESSION["user_id"])){
    header("Location: login.php");
    exit();
}

$payment_id = ($_GET["payment_id"]);
$expense_id = ($_GET["expense_id"]);
$user_id = $_SESSION["user_id"];

$check = mysqli_query($link,
"SELECT payment_id 
 FROM payments 
 WHERE payment_id=$payment_id 
 AND user_id=$user_id 
 AND status='unpaid'");

if(mysqli_num_rows($check) == 0){
    die("Invalid request");
}

mysqli_query($link,
"UPDATE payments
 SET status='pending'
 WHERE payment_id=$payment_id
");

header("Location: paymentstatus.php?expense_id=$expense_id");
exit();
?>