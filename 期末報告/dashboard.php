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

$user_id = $_SESSION["user_id"];

$query_groups = "SELECT g.* FROM groups_tb g 
                 JOIN group_members gm ON g.group_id = gm.group_id 
                 WHERE gm.user_id = '$user_id'";

$my_groups = mysqli_query($link, $query_groups);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Dashboard - SharePay</title>

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
            padding:30px 20px;
        }

        .brand{
            font-size:22px;
            font-weight:bold;
            color:#8b6f47;
            margin-bottom:25px;
        }

        .profile{
            background:#f3eadf;
            padding:15px;
            border-radius:15px;
            margin-bottom:25px;
        }

        .profile small{
            display:block;
            color:#777;
            margin-bottom:5px;
        }

        .profile b{
            color:#4a3b2a;
        }

        /* NAV */
        .nav a{
            display:block;
            text-decoration:none;
            padding:12px 14px;
            border-radius:12px;
            margin-bottom:10px;
            color:#4a3b2a;
            background:#f7f0e6;
            transition:0.2s;
        }

        .nav a:hover{
            background:#e7ddcf;
        }

        /* LOGOUT */
        .logout{
            position:absolute;
            bottom:25px;
            left:20px;
            right:20px;
        }

        .logout a{
            display:block;
            text-align:center;
            padding:12px;
            border-radius:12px;
            background:#c97b63;
            color:white;
            text-decoration:none;
            font-weight:bold;
        }

        /* MAIN */
        .main{
            margin-left:240px;
            padding:40px;
        }

        .header h1{
            font-size:26px;
            color:#4a3b2a;
            margin-bottom:6px;
        }

       .header p{
            color:#777;
            font-size:14px;
        }

        /* SECTION */
        .section-title{
            margin:25px 0 15px;
            font-size:18px;
            color:#4a3b2a;
        }

        /* GROUP CARDS */
        .groups{
            display:grid;
            grid-template-columns:repeat(auto-fill, minmax(240px, 1fr));
            gap:18px;
        }

        .group-card{
            background:#fffaf3;
            border:1px solid #e7ddcf;
            padding:20px;
            border-radius:18px;
            text-decoration:none;
            color:#333;
            transition:0.2s;
        }

        .group-card:hover{
            transform:translateY(-3px);
            border-color:#8b6f47;
        }

        .code{
            display:inline-block;
            background:#efe4d1;
            color:#8b6f47;
            padding:5px 10px;
            border-radius:10px;
            font-size:12px;
            margin-bottom:12px;
        }

        .group-card h3{
            color:#4a3b2a;
            margin-bottom:8px;
        }

        .group-card p{
            font-size:13px;
            color:#777;
        }

        /* EMPTY */
        .empty{
            background:#fffaf3;
            border:1px dashed #d8c9b6;
            padding:30px;
            border-radius:18px;
            text-align:center;
            color:#888;
        }   
    </style>
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
        <a href="creategroup.php">Create Group</a>
        <a href="joingroup.php">Join Group</a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

</div>

<div class="main">

    <div class="header">
        <h1>Hello, <?php echo $_SESSION["username"]; ?></h1>
        <p>Manage your SharePay groups here</p>
    </div>

    <h2 class="section-title">My Active Groups</h2>

    <div class="groups">

        <?php if(mysqli_num_rows($my_groups) > 0){ ?>

            <?php while($g = mysqli_fetch_assoc($my_groups)){ ?>

                <a href="groupdetail.php?group_id=<?php echo $g['group_id']; ?>" class="group-card">

                    <div class="code">
                        <?php echo $g["invite_code"]; ?>
                    </div>

                    <h3>
                        <?php echo $g["group_name"]; ?>
                    </h3>

                    <p>View group bills and payment status</p>

                </a>

            <?php } ?>

        <?php } else { ?>

            <div class="empty">
                You do not have any group yet.<br>
                Create or join a group from the sidebar
            </div>

        <?php } ?>

    </div>

</div>

</body>
</html>