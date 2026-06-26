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

if(isset($_POST["create"])){

    $group_name = mysqli_real_escape_string($link, $_POST["group_name"]);
    $created_by = $_SESSION["user_id"];

    $invite_code = "SP-" . strtoupper(substr(md5(uniqid()),0,6));

    if(empty(trim($group_name))){
    die("Group name cannot be empty.");
    }

    $sql = "INSERT INTO groups_tb(group_name,invite_code,created_by)
            VALUES('$group_name','$invite_code','$created_by')";

    if(mysqli_query($link,$sql)){

        $group_id = mysqli_insert_id($link);

        mysqli_query($link,
        "INSERT INTO group_members(group_id,user_id)
        VALUES('$group_id','$created_by')");

        header("Location: groupdetail.php?group_id=$group_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Create Group - SharePay</title>
<!-- -----------------------------------------------------DESIGN----------------------------------------------- -->
    <style>
        *{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial, sans-serif;
}

body{
    min-height:100vh;
    background:#f6f1e8;
    color:#333;
    line-height:1.5;
}

/* SIDEBAR */
.sidebar{
    width:240px;
    background:#fffaf3;
    border-right:1px solid #e7ddcf;
    position:fixed;
    top:0;
    bottom:0;
    left:0;
    padding:28px 20px;
}

.brand{
    font-size:22px;
    font-weight:bold;
    color:#8b6f47;
    margin-bottom:22px;
}

.profile{
    background:#f3eadf;
    padding:14px;
    border-radius:14px;
    margin-bottom:22px;
}

.profile small{
    display:block;
    color:#777;
    margin-bottom:4px;
}

.profile b{
    color:#4a3b2a;
}

.nav a{
    display:block;
    text-decoration:none;
    padding:11px 14px;
    border-radius:12px;
    margin-bottom:10px;
    color:#4a3b2a;
    background:#f7f0e6;
}

.nav a:hover,
.nav .active{
    background:#e7ddcf;
}

.logout{
    position:absolute;
    bottom:22px;
    left:20px;
    right:20px;
}

.logout a{
    display:block;
    text-align:center;
    padding:11px;
    border-radius:12px;
    background:#c97b63;
    color:white;
    text-decoration:none;
    font-weight:bold;
}

.main{
    margin-left:240px;
    padding:32px;
}

.page-header{
    margin-bottom:22px;
}

.page-header h1{
    font-size:22px;
    color:#4a3b2a;
    margin-bottom:4px;
}

.page-header p{
    color:#777;
    font-size:13px;
}

.form-card{
    background:#fffaf3;
    border:1px solid #e7ddcf;
    padding:24px;
    border-radius:16px;
    max-width:520px;
}

label{
    display:block;
    font-size:13px;
    font-weight:bold;
    margin-bottom:6px;
    color:#555;
}

input{
    width:100%;
    padding:11px 12px;
    border-radius:12px;
    border:1px solid #e7ddcf;
    margin-bottom:16px;
    outline:none;
    font-size:14px;
}

input:focus{
    border-color:#8b6f47;
}

.info-box{
    background:#f7f0e6;
    color:#8b6f47;
    border:1px dashed #d8c9b6;
    padding:12px;
    border-radius:12px;
    font-size:13px;
    margin-bottom:16px;
}

.btn-row{
    display:flex;
    gap:10px;
}

.cancel-btn{
    flex:1;
    padding:11px;
    text-align:center;
    background:#f7f0e6;
    color:#4a3b2a;
    text-decoration:none;
    border-radius:12px;
    font-weight:bold;
}

.submit-btn{
    flex:2;
    padding:11px;
    background:#8b6f47;
    border:none;
    color:white;
    border-radius:12px;
    font-size:14px;
    font-weight:bold;
    cursor:pointer;
}

.submit-btn:hover{
    background:#755c3a;
}
    </style>
<!-- -----------------------------------------------------DESIGN----------------------------------------------- -->


</head>
<body>

<div class="sidebar">

    <div class="brand">SharePay</div>

    <div class="profile">
        <small>Welcome</small>
        <b><?php echo $_SESSION["username"]; ?></b>
    </div>

    <div class="nav">
        <a href="dashboard.php">Workspace</a>
        <a href="creategroup.php" class="active">Create Group</a>
        <a href="joingroup.php">Join Group</a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

</div>

<div class="main">

    <div class="page-header">
        <h1>Create New Group</h1>
        <p>Create a new SharePay group for managing shared expenses</p>
    </div>

    <div class="form-card">

        <form method="POST">

            <label>Group Name</label>
            <input type="text" name="group_name" required placeholder="Example: Thailand Trip">

            <div class="info-box">
                Invitation code will be generated automatically by the system.
            </div>

            <div class="btn-row">
                <a href="dashboard.php" class="cancel-btn">Cancel</a>
                <button type="submit" name="create" class="submit-btn">
                    Create Group
                </button>
            </div>

        </form>

    </div>

</div>

</body>
</html>