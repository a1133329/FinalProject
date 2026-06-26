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

$error_message = "";

if(isset($_POST["join"])){

    $invite_code = trim($_POST["invite_code"]);
    $user_id = $_SESSION["user_id"];
    $username = $_SESSION["username"];

    $sql = "SELECT * FROM groups_tb WHERE invite_code='$invite_code'";
    $result = mysqli_query($link,$sql);

    if(mysqli_num_rows($result) == 1){

        $group = mysqli_fetch_assoc($result);
        $group_id = $group["group_id"];

        $check_member = mysqli_query($link,
        "SELECT * FROM group_members
        WHERE group_id='$group_id'
        AND user_id='$user_id'");

        if(mysqli_num_rows($check_member) == 0) {

            mysqli_query($link,
            "INSERT INTO group_members(group_id,user_id)
            VALUES('$group_id','$user_id')");

            $message = $username . " joined this group.";

            mysqli_query($link,
            "INSERT INTO group_notes(group_id,user_id,message)
            VALUES('$group_id','$user_id','$message')");
        }

        header("Location: groupdetail.php?group_id=$group_id");
        exit();
    }
    else{
        $error_message = "Invite code not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Join Group - SharePay</title>


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

/* LOGOUT */
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

/* MAIN */
.main{
    margin-left:240px;
    padding:32px;
}

/* HEADER TEXT */
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

/* LAYOUT */
.workspace{
    display:flex;
    gap:18px;
    align-items:flex-start;
}

/* FORM */
.form-card{
    flex:2;
    background:#fffaf3;
    border:1px solid #e7ddcf;
    padding:24px;
    border-radius:16px;
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
}

input:focus{
    border-color:#8b6f47;
}

/* BUTTONS */
.btn-row{
    display:flex;
    gap:10px;
    margin-top:6px;
}

.cancel-btn{
    flex:1;
    padding:11px;
    text-align:center;
    text-decoration:none;
    border-radius:12px;
    background:#f7f0e6;
    color:#4a3b2a;
    font-weight:bold;
}

.submit-btn{
    flex:2;
    padding:11px;
    background:#8b6f47;
    border:none;
    color:white;
    border-radius:12px;
    font-weight:bold;
    cursor:pointer;
}

.submit-btn:hover{
    background:#755c3a;
}

/* ERROR */
.error-box{
    background:#fdecea;
    color:#c0392b;
    padding:11px;
    border-radius:12px;
    margin-bottom:14px;
    font-size:13px;
}

/* INFO */
.info-box{
    flex:1;
    background:#fffaf3;
    border:1px dashed #d8c9b6;
    padding:18px;
    border-radius:16px;
    font-size:13px;
    color:#555;
    line-height:1.6;
}

.info-box h4{
    color:#8b6f47;
    margin-bottom:8px;
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
        <a href="creategroup.php">Create Group</a>
        <a href="joingroup.php" class="active">Join Group</a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

</div>

<div class="main">

    <div class="page-header">
        <h1>Join Group</h1>
        <p>Enter invitation code to join a group</p>
    </div>

    <div class="workspace">

        <div class="form-card">

            <?php if(!empty($error_message)){ ?>
                <div class="error-box">
                    <?php echo $error_message; ?>
                </div>
            <?php } ?>

            <form method="POST">

                <label>Invitation Code</label>

                <input type="text"
                name="invite_code"
                required
                placeholder="Example: SP-A1B2C">

                <div class="btn-row">

                    <a href="dashboard.php" class="cancel-btn">
                        Cancel
                    </a>

                    <button type="submit" name="join" class="submit-btn">
                        Join Group
                    </button>

                </div>

            </form>

        </div>

        <div class="info-box">

            <h4>How It Works</h4>

            <p>
                Ask your friend for the group invitation code.
            </p>

            <p style="margin-top:10px;">
                Once you join the group, your name will automatically appear in the member list and group notes.
            </p>

        </div>

    </div>

</div>

</body>
</html>