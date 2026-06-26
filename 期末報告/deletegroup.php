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

$group_id = $_GET["group_id"];
$user_id = $_SESSION["user_id"];

$group = mysqli_fetch_assoc(mysqli_query($link,
"SELECT * FROM groups_tb WHERE group_id='$group_id'"));

if($group["created_by"] != $user_id){
    die("Only creator can delete this group.");
}

/* ambil semua expense di group */
$expenses = mysqli_query($link,
"SELECT * FROM expenses WHERE group_id='$group_id'");

while($e = mysqli_fetch_assoc($expenses)){
    $expense_id = $e["expense_id"];

    mysqli_query($link,
    "DELETE FROM payments WHERE expense_id='$expense_id'");
}

/* delete data terkait */
mysqli_query($link, "DELETE FROM expenses WHERE group_id='$group_id'");
mysqli_query($link, "DELETE FROM group_notes WHERE group_id='$group_id'");
mysqli_query($link, "DELETE FROM group_members WHERE group_id='$group_id'");
mysqli_query($link, "DELETE FROM groups_tb WHERE group_id='$group_id'");

header("Location: dashboard.php");
exit();
?>