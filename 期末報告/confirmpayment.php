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

$expense = mysqli_fetch_assoc(mysqli_query($link,
"SELECT * FROM expenses WHERE expense_id=$expense_id"));

if(!$expense){
    die("Expense not found");
}

$group_id = $expense["group_id"];

$group = mysqli_fetch_assoc(mysqli_query($link,
"SELECT * FROM groups_tb WHERE group_id=$group_id"));

if(!$group){
    die("Group not found");
}

if($group["created_by"] != $user_id){
    die("Only creator can confirm");
}

$check = mysqli_query($link,
"SELECT payment_id 
 FROM payments 
 WHERE payment_id=$payment_id 
 AND status='pending'");

if(mysqli_num_rows($check) == 0){
    die("Invalid payment");
}

mysqli_query($link,
"UPDATE payments
 SET status='confirmed'
 WHERE payment_id=$payment_id
 AND status='pending'");

header("Location: paymentstatus.php?expense_id=$expense_id");
exit();
?>