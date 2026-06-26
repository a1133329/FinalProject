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

if(!$group){
    die("Group not found.");
}

$is_creator = ($group["created_by"] == $user_id);

/* send note */
if(isset($_POST["send_note"])){

    $message = mysqli_real_escape_string($link,$_POST["message"]);

    mysqli_query($link,
    "INSERT INTO group_notes(group_id,user_id,message)
    VALUES('$group_id','$user_id','$message')");

    header("Location: groupdetail.php?group_id=$group_id");
    exit();
}

/* expenses */
$expenses = mysqli_query($link,
"SELECT * FROM expenses
WHERE group_id='$group_id'
ORDER BY expense_id DESC");

/* members */
$members = mysqli_query($link,
"SELECT users.username
FROM group_members
JOIN users
ON group_members.user_id = users.user_id
WHERE group_members.group_id='$group_id'");

/* notes */
$notes = mysqli_query($link,
"SELECT group_notes.*, users.username
FROM group_notes
JOIN users
ON group_notes.user_id = users.user_id
WHERE group_notes.group_id='$group_id'
ORDER BY note_id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Group Detail - SharePay</title>


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

        .nav a{
            display:block;
            text-decoration:none;
            padding:12px 14px;
            border-radius:12px;
            margin-bottom:10px;
            color:#4a3b2a;
            background:#f7f0e6;
        }   

        .nav a:hover,
        .nav .active{
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

        /* HEADER */
        .header{
            background:#fffaf3;
            border:1px solid #e7ddcf;
            padding:25px;
            border-radius:20px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:25px;
        }

        .header h1{
            font-size:24px;
            color:#4a3b2a;
            margin-bottom:8px;
        }

        .code{
            display:inline-block;
            background:#efe4d1;
            color:#8b6f47;
            padding:6px 12px;
            border-radius:12px;
            font-size:12px;
        }

        /* BUTTONS */
        .btn-area{
            display:flex;
            gap:10px;
        }

        .add-btn{
            padding:12px 18px;
            background:#8b6f47;
            color:white;
            text-decoration:none;
            border-radius:12px;
            font-weight:bold;
        }

        .add-btn:hover{
            background:#755c3a;
        }

        .delete-btn{
            padding:12px 18px;
            background:#c97b63;
            color:white;
            text-decoration:none;
            border-radius:12px;
            font-weight:bold;
        }

        /* LAYOUT */
        .content{
            display:flex;
            gap:20px;
        }

        .left{
            flex:2;
        }

        .right{
            flex:1;
        }

        /* BOX */
        .box{
            background:#fffaf3;
            border:1px solid #e7ddcf;
            padding:20px;
            border-radius:18px;
            margin-bottom:20px;
        }

        .box h3{
            margin-bottom:15px;
            color:#4a3b2a;
        }

        /* EXPENSE */
        .expense-card{
            background:#ffffff;
            border:1px solid #e7ddcf;
            padding:15px;
            border-radius:15px;
            margin-bottom:12px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }

        .expense-title{
            font-weight:bold;
            color:#4a3b2a;
            margin-bottom:5px;
        }

        .category{
            display:inline-block;
            background:#efe4d1;
            color:#8b6f47;
            padding:4px 10px;
            border-radius:10px;
            font-size:12px;
        }

        .amount{
            color:#8b6f47;
            font-weight:bold;
            text-align:right;
        }

        .view-btn{
            font-size:12px;
            color:#8b6f47;
            text-decoration:none;
        }

        /* MEMBERS */
        .member-list p{
            background:#f7f0e6;
            padding:10px;
            border-radius:12px;
            margin-bottom:8px;
            font-size:14px;
        }

        /* NOTES */
        textarea{
            width:100%;
            height:80px;
            padding:10px;
            border-radius:12px;
            border:1px solid #e7ddcf;
            resize:none;
            outline:none;
        }

        .send-btn{
            width:100%;
            padding:12px;
            margin-top:10px;
            background:#8b6f47;
            color:white;
            border:none;
            border-radius:12px;
            font-weight:bold;
            cursor:pointer;
        }

        .note-item{
            background:#ffffff;
            border:1px solid #e7ddcf;
            padding:12px;
            border-radius:12px;
            margin-bottom:10px;
        }

        .note-item b{
            color:#8b6f47;
        }

        .note-item small{
            color:#999;
        }

        .empty{
            background:#fffaf3;
            border:1px dashed #d8c9b6;
            padding:20px;
            border-radius:15px;
            text-align:center;
            color:#888;
        }

    </style>
<!-- -----------------------------------------------------DESIGN----------------------------------------------- -->
 
</head>

<body>

<div class="sidebar">

    <div class="brand">SharePay</div>

    <div class="profile">
        <small>Welcome back</small>
        <b><?php echo $_SESSION["username"]; ?></b>
    </div>

    <div class="nav">
        <a href="dashboard.php" class="active">Workspace</a>
        <a href="creategroup.php">Create Group</a>
        <a href="joingroup.php">Join Group</a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

</div>

<div class="main">

    <div class="header">

        <div>
            <h1><?php echo $group["group_name"]; ?></h1>

            <div class="code">
                Invite Code:
                <?php echo $group["invite_code"]; ?>
            </div>
        </div>

        <?php if($is_creator){ ?>

            <div class="btn-area">

                <a href="addexpense.php?group_id=<?php echo $group_id; ?>" class="add-btn">
                    Add Expense
                </a>

                <a href="deletegroup.php?group_id=<?php echo $group_id; ?>"
                   class="delete-btn"
                   onclick="return confirm('Delete this group?')">
                    Delete Group
                </a>

            </div>

        <?php } ?>

    </div>

    <div class="content">

        <div class="left">

            <div class="box">

                <h3>Group Expenses</h3>

                <?php if(mysqli_num_rows($expenses) > 0){ ?>

                    <?php while($e = mysqli_fetch_assoc($expenses)){ ?>

                        <div class="expense-card">

                            <div>

                                <div class="expense-title">
                                    <?php echo $e["title"]; ?>
                                </div>

                                <div class="category">
                                    <?php echo $e["category"]; ?>
                                </div>

                            </div>

                            <div>

                                <div class="amount">
                                    NT$ <?php echo number_format($e["amount"],0); ?>
                                </div>

                                <a href="paymentstatus.php?expense_id=<?php echo $e['expense_id']; ?>" class="view-btn">
                                    View Payments
                                </a>

                            </div>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="empty">
                        No expenses yet
                    </div>

                <?php } ?>

            </div>

            <div class="box">

                <h3>Group Notes</h3>

                <form method="POST">

                    <textarea name="message"
                    required
                    placeholder="Send reminder or note..."></textarea>

                    <button type="submit"
                    name="send_note"
                    class="send-btn">
                        Send Note
                    </button>

                </form>

                <br>

                <?php if(mysqli_num_rows($notes) > 0){ ?>

                    <?php while($n = mysqli_fetch_assoc($notes)){ ?>

                        <div class="note-item">

                            <b><?php echo $n["username"]; ?></b>

                            <p><?php echo $n["message"]; ?></p>

                            <small><?php echo $n["created_at"]; ?></small>

                        </div>

                    <?php } ?>

                <?php } else { ?>

                    <div class="empty">
                        No notes yet
                    </div>

                <?php } ?>

            </div>

        </div>

        <div class="right">

            <div class="box">

                <h3>Group Members</h3>

                <div class="member-list">

                    <?php while($m = mysqli_fetch_assoc($members)){ ?>

                        <p>
                            <?php echo $m["username"]; ?>
                        </p>

                    <?php } ?>

                </div>

            </div>

            <div class="box">

                <h3>Tips</h3>

                <p style="font-size:13px; color:#666; line-height:1.7;">
                    Please make sure all members already joined before creating expense.
                    The system will split the bill equally based on current group members.
                </p>

            </div>

        </div>

    </div>

</div>

</body>
</html>