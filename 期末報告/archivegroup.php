<?php
session_start();

$link = mysqli_connect('localhost', 'root', '', 'final_project');

if (!$link) {
    die("Fail to connect!");
}

if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
    die("Admin Only");
}

if(!isset($_GET["group_id"])){
    die("Group ID not found.");
}

$group_id = $_GET["group_id"];

mysqli_query($link,
"UPDATE groups_tb
SET status='archived'
WHERE group_id='$group_id'");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Archiving Group</title>

    <meta http-equiv="refresh" content="1.5;url=admin_dashboard.php">

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:Arial, sans-serif;
        }

        body{
            height:100vh;
            background:#f8f9fd;
            display:flex;
            justify-content:center;
            align-items:center;
        }

        .box{
            background:white;
            padding:40px;
            border-radius:25px;
            box-shadow:0 10px 30px rgba(0,0,0,0.1);
            text-align:center;
            width:360px;
        }

        .archive-icon{
            width:80px;
            height:80px;
            border-radius:50%;
            background:#fff3cd;
            color:#f39c12;
            display:flex;
            justify-content:center;
            align-items:center;
            margin:0 auto 20px;
            font-size:36px;
            font-weight:bold;
            animation:bounce 1s infinite;
        }

        h2{
            color:#2c3e50;
            margin-bottom:10px;
        }

        p{
            color:#777;
            font-size:14px;
            line-height:1.6;
        }

        .loading{
            margin-top:18px;
            height:8px;
            background:#eee;
            border-radius:20px;
            overflow:hidden;
        }

        .loading span{
            display:block;
            height:100%;
            width:60%;
            background:#f39c12;
            border-radius:20px;
            animation:load 1.5s infinite;
        }

        @keyframes bounce{
            0%,100%{ transform:translateY(0); }
            50%{ transform:translateY(-8px); }
        }

        @keyframes load{
            0%{ margin-left:-60%; }
            100%{ margin-left:100%; }
        }
    </style>
</head>

<body>

<div class="box">

    <div class="archive-icon">
        ↓
    </div>

    <h2>Archiving Group...</h2>

    <p>
        The selected group is being archived.<br>
        Please wait a moment.
    </p>

    <div class="loading">
        <span></span>
    </div>

</div>

</body>
</html>