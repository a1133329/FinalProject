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

$expense_id = intval($_GET["expense_id"]);
$user_id = $_SESSION["user_id"];

$expense = mysqli_fetch_assoc(mysqli_query($link,
"SELECT * FROM expenses WHERE expense_id='$expense_id'"));

if(!$expense){
    die("Expense not found");
}

$group_id = $expense["group_id"];

$group = mysqli_fetch_assoc(mysqli_query($link,
"SELECT * FROM groups_tb WHERE group_id='$group_id'"));

$is_creator = ($group["created_by"] == $user_id);

$sql = "SELECT payments.*, users.username
        FROM payments
        JOIN users ON payments.user_id = users.user_id
        WHERE payments.expense_id='$expense_id'";

$result = mysqli_query($link,$sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payment Status</title>

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

.nav a{
    display:block;
    text-decoration:none;
    padding:12px;
    border-radius:12px;
    margin-bottom:10px;
    background:#f7f0e6;
    color:#4a3b2a;
}

.main{
    margin-left:240px;
    padding:40px;
}

.summary{
    background:#fffaf3;
    border:1px solid #e7ddcf;
    padding:20px;
    border-radius:16px;
    display:flex;
    justify-content:space-between;
    margin-bottom:20px;
}

.total{
    background:#f7f0e6;
    padding:10px 15px;
    border-radius:12px;
}

.content{
    display:flex;
    gap:20px;
}

.status-box{
    flex:2;
    background:#fffaf3;
    border:1px solid #e7ddcf;
    padding:20px;
    border-radius:16px;
}

.right{
    flex:1;
}

.member-row{
    background:#f7f0e6;
    border:1px solid #e7ddcf;
    padding:14px;
    border-radius:14px;
    margin-bottom:12px;
    display:flex;
    justify-content:space-between;
}

.member-name{
    font-weight:bold;
}

.member-amount{
    font-weight:bold;
    margin-bottom:6px;
}

.status-badge{
    padding:4px 10px;
    border-radius:999px;
    font-size:12px;
    font-weight:bold;
}

.unpaid{ background:#fcebea; color:#c0392b; }
.pending{ background:#fff3e0; color:#d35400; }
.confirmed, .paid{ background:#e8f8f5; color:#16a085; }

.action-btn{
    display:inline-block;
    padding:6px 10px;
    border-radius:10px;
    font-size:12px;
    text-decoration:none;
    color:white;
}

.submit{ background:#8b6f47; }
.confirm{ background:#27ae60; }

.box{
    background:#fffaf3;
    border:1px solid #e7ddcf;
    padding:18px;
    border-radius:16px;
    margin-bottom:15px;
}

.box h3{
    margin-bottom:10px;
    color:#4a3b2a;
}

.back-btn{
    display:block;
    margin-top:10px;
    text-align:center;
    padding:10px;
    background:#f7f0e6;
    border-radius:12px;
    text-decoration:none;
    color:#4a3b2a;
}

.sidebar-bottom {
    position: absolute;
    bottom: 25px;
    width: calc(100% - 40px);
}
</style>

</head>

<body>

<div class="sidebar">

    <div class="brand">SharePay</div>

    <div class="profile">
        Welcome<br>
        <b><?php echo $_SESSION["username"]; ?></b>
    </div>

    <div class="nav">
        <a href="dashboard.php">Workspace</a>
        <a href="creategroup.php">Create Group</a>
        <a href="joingroup.php">Join Group</a>
    </div>

    <div class="sidebar-bottom">
    <a href="groupdetail.php?group_id=<?php echo $group_id; ?>" class="back-btn">
        Back to Group
    </a>
</div>

</div>

<div class="main">

    <div class="summary">
        <div>
            <h2><?php echo htmlspecialchars($expense["title"]); ?></h2>
            <small><?php echo htmlspecialchars($group["group_name"]); ?></small>
        </div>

        <div class="total">
            NT$ <?php echo number_format($expense["amount"],0); ?>
        </div>
    </div>

    <div class="content">

        <div class="status-box">

            <h3>Payment Status</h3>

            <?php while($row = mysqli_fetch_assoc($result)){

                $status = strtolower($row["status"] ?? "unpaid");
            ?>

            <div class="member-row">

                <div>
                    <div class="member-name">
                        <?php echo $row["username"]; ?>
                        <?php if($row["user_id"] == $user_id) echo " (You)"; ?>
                    </div>

                    <span class="status-badge <?php echo $status; ?>">
                        <?php echo strtoupper($status); ?>
                    </span>
                </div>

                <div style="text-align:right;">
                    <div class="member-amount">
                        NT$ <?php echo number_format($row["amount"],0); ?>
                    </div>

                    <?php if($row["user_id"] == $user_id){ ?>

                        <?php if($status == "unpaid"){ ?>
                            <a class="action-btn submit"
                               href="submitpayment.php?payment_id=<?php echo $row['payment_id']; ?>&expense_id=<?php echo $expense_id; ?>">
                                Submit Payment
                            </a>
                        <?php } ?>

                        <?php if($status == "pending"){ ?>
                            <small>Waiting Confirmation</small>
                        <?php } ?>

                        <?php if($status == "confirmed"){ ?>
                            <b style="color:#16a085;">Paid</b>
                        <?php } ?>

                    <?php } ?>

                    <?php if($is_creator && $status == "pending"){ ?>
                        <div style="margin-top:5px;">
                            <a class="action-btn confirm"
                               href="confirmpayment.php?payment_id=<?php echo $row['payment_id']; ?>&expense_id=<?php echo $expense_id; ?>">
                                Confirm Payment
                            </a>
                        </div>
                    <?php } ?>

                </div>

            </div>

            <?php } ?>

        </div>

        <div class="right">

            <div class="box">
                <h4 style="color:#4a3b2a; margin-bottom: 8px;">How it works:</h4>
            <p>1. Member clicks <b>SUBMIT PAYMENT</b> after transferring money.</p>
            <p style="margin-top:5px;">2. Status becomes <b style="color:#d35400;">WAITING CONFIRMATION</b>.</p>
            <p style="margin-top:5px;">3. Creator checks payment.</p>
            <p style="margin-top:5px;">4. The creator clicks <b>CONFIRM PAYMENT</b>, and the status changes to <b style="color:#16a085;">PAID</b> after verifying that the member has completed the payment.</p>

            </div>

        </div>

    </div>

</div>

</body>
</html>