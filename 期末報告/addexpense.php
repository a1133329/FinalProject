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

/* GROUP */
$group = mysqli_fetch_assoc(mysqli_query($link,
"SELECT * FROM groups_tb WHERE group_id='$group_id'"));

if(!$group){
    die("Group not found.");
}

/* STATUS CHECK */
if($group["status"] == "archived"){
    die("This group has been archived.");
}

if($group["created_by"] != $user_id){
    die("Only creator can add expense.");
}

/* MEMBERS */
$members_check = mysqli_query($link,
"SELECT * FROM group_members WHERE group_id='$group_id'");

$member_count = mysqli_num_rows($members_check);

if(isset($_POST["add"])){

    if($member_count < 2){
        die("Need at least 2 members.");
    }

    $title = mysqli_real_escape_string($link, $_POST["title"]);
    $category = mysqli_real_escape_string($link, $_POST["category"]);
    $amount = (float) $_POST["amount"];
    $expense_date = $_POST["expense_date"];

    /* INSERT EXPENSE (FIXED COLUMN NAME) */
    $sql = "INSERT INTO expenses(group_id, title, category, amount, expense_date, created_by)
            VALUES('$group_id','$title','$category','$amount','$expense_date','$user_id')";

    if(mysqli_query($link,$sql)){

        $expense_id = mysqli_insert_id($link);

        $members = mysqli_query($link,
        "SELECT * FROM group_members WHERE group_id='$group_id'");

        $count = mysqli_num_rows($members);

        if($count > 0){
            $split = $amount / $count;
        } else {
            $split = 0;
        }

        while($m = mysqli_fetch_assoc($members)){
            $member_user_id = $m["user_id"];

            mysqli_query($link,
            "INSERT INTO payments(expense_id,user_id,amount,status)
            VALUES('$expense_id','$member_user_id','$split','unpaid')");
        }

        header("Location: groupdetail.php?group_id=$group_id");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Add Expense - SharePay</title>

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
    padding:35px;
}

.page-header{
    margin-bottom:22px;
}

.page-header h1{
    font-size:24px;
    color:#4a3b2a;
}

.page-header p{
    color:#777;
    font-size:13px;
}

.workspace{
    display:flex;
    gap:20px;
}

.form-card{
    flex:2;
    background:#fffaf3;
    border:1px solid #e7ddcf;
    padding:25px;
    border-radius:18px;
}

label{
    display:block;
    font-size:13px;
    font-weight:bold;
    margin-bottom:6px;
    color:#555;
}

input, select{
    width:100%;
    padding:12px;
    border-radius:12px;
    border:1px solid #e7ddcf;
    margin-bottom:16px;
    outline:none;
    font-size:14px;
    background:white;
}

input:focus, select:focus{
    border-color:#8b6f47;
}

.warning-box{
    background:#fff3cd;
    color:#856404;
    padding:12px;
    border-radius:12px;
    font-size:13px;
    margin-bottom:18px;
}

.btn-row{
    display:flex;
    gap:10px;
    margin-top:10px;
}

.back-btn{
    flex:1;
    text-align:center;
    padding:12px;
    background:#f7f0e6;
    border-radius:12px;
    text-decoration:none;
    color:#4a3b2a;
    font-weight:bold;
}

.submit-btn{
    flex:2;
    padding:12px;
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

.submit-btn:disabled{
    background:#bbb;
    cursor:not-allowed;
}


.info-box{
    flex:1;
    background:#f3eadf;
    border:1px dashed #c9b7a3;
    padding:20px;
    border-radius:18px;
    font-size:13px;
    color:#5a4a3a;
    line-height:1.6;
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
        <a href="dashboard.php" class="active">Workspace</a>
        <a href="creategroup.php">Create Group</a>
        <a href="joingroup.php">Join Group</a>
    </div>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

</div>

<div class="main">

    <div class="page-header">
        <h1>Add New Expense</h1>
        <p>Add a new expense for group: <b><?php echo $group["group_name"]; ?></b></p>
    </div>

    <div class="workspace">

        <div class="form-card">

            <div class="warning-box">
                Current members: <b><?php echo $member_count; ?></b> people.<br>
                Please make sure all members have joined before adding expense.
            </div>

            <form method="POST">

                <label>Category</label>
                <select name="category">
                    <option value="food">Food</option>
                    <option value="transportation">Transportation</option>
                    <option value="accomodation">Accommodation</option>
                    <option value="shopping">Shopping</option>
                    <option value="other">Other</option>
                </select>

                <label>Description</label>
                <input type="text" name="title" required placeholder="Example: McDonald Dinner">

                <label>Total Amount (NT$)</label>
                <input type="number" name="amount" min="1" required placeholder="Example: 1200">

                <label>Expense Date</label>
                <input type="date" name="expense_date" value="<?php echo date('Y-m-d'); ?>" required>

                <div class="btn-row">
                    <a href="groupdetail.php?group_id=<?php echo $group_id; ?>" class="back-btn">
                        Cancel
                    </a>

                    <button type="submit" name="add" class="submit-btn" <?php if($member_count < 2){ echo "disabled"; } ?>>
                        Split This Bill
                    </button>
                </div>

            </form>

        </div>

        <div class="info-box">
            <h4>Equal Split</h4>
            <p>
                After you add this expense, the system will automatically divide the amount equally among all current group members.
            </p>
            <p style="margin-top:10px;">
                If new members join after this expense is created, they will not be included in this old split.
            </p>
        </div>

    </div>

</div>

</body>
</html>