<?php
    session_start();

    $link = mysqli_connect('localhost', 'root', '', 'final_project');

    if (!$link) {
        die("Fail to connect!");
    }

    if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
        die("Admin Only");
    }

    $total_users = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS total FROM users"))["total"];
    $total_groups = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS total FROM groups_tb"))["total"];
    $total_archived = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS total FROM groups_tb WHERE status='archived'"))["total"];
    $total_expenses = mysqli_fetch_assoc(mysqli_query($link,"SELECT COUNT(*) AS total FROM expenses"))["total"];

    $total_money = mysqli_fetch_assoc(mysqli_query($link,"SELECT SUM(amount) AS total FROM expenses"))["total"];
    if($total_money == ""){
        $total_money = 0;
    }


/* ---------------- CATEGORY CHART ---------------- */

    $food_total = mysqli_fetch_assoc(mysqli_query($link,
    "SELECT SUM(amount) AS total
    FROM expenses
    WHERE category='food'"))["total"];

    $transportation_total = mysqli_fetch_assoc(mysqli_query($link,
    "SELECT SUM(amount) AS total
    FROM expenses
    WHERE category='transportation'"))["total"];

    $accomodation_total = mysqli_fetch_assoc(mysqli_query($link,
    "SELECT SUM(amount) AS total
    FROM expenses
    WHERE category='accomodation'"))["total"];

    $shopping_total = mysqli_fetch_assoc(mysqli_query($link,
    "SELECT SUM(amount) AS total
    FROM expenses
    WHERE category='shopping'"))["total"];

    $other_total = mysqli_fetch_assoc(mysqli_query($link,
    "SELECT SUM(amount) AS total
    FROM expenses
    WHERE category='other'"))["total"];

    if($food_total=="") $food_total=0;
    if($transportation_total=="") $transportation_total=0;
    if($accomodation_total=="") $accomodation_total=0;
    if($shopping_total=="") $shopping_total=0;
    if($other_total=="") $other_total=0;

/* ---------------- CATEGORY CHART ---------------- */


    $users = mysqli_query($link,"SELECT * FROM users ORDER BY user_id DESC");

    $groups = mysqli_query($link,
    "SELECT groups_tb.*, users.username AS creator_name
    FROM groups_tb
    JOIN users ON groups_tb.created_by = users.user_id
    ORDER BY groups_tb.group_id DESC");

    $bills = mysqli_query($link,
    "SELECT expenses.*, groups_tb.group_name, users.username AS creator_name
    FROM expenses
    JOIN groups_tb ON expenses.group_id = groups_tb.group_id
    JOIN users ON expenses.created_by = users.user_id
    ORDER BY expenses.expense_id DESC");
    ?>

<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>

<!-- -----------------------------------------------------DESIGN----------------------------------------------- -->
    <style>
        *{
            box-sizing:border-box;
            font-family:Arial, sans-serif;
        }

        body{
            margin:0;
            background:#f4f6f9;
            color:#2c3e50;
            font-family:Arial,sans-serif;
        }

        .top{
            background:#1f2937;
            color:white;
            padding:18px 32px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            box-shadow:0 2px 10px rgba(0,0,0,.08);
        }

        .top h2{
            margin:0;
            font-size:22px;
        }

        .logout{
            background:#ef4444;
            color:white;
            padding:10px 16px;
            border-radius:10px;
            text-decoration:none;
            font-size:13px;
            font-weight:bold;
        }

        .container{
            padding:30px;
        }

        .summary{
            display:grid;
            grid-template-columns:repeat(5,1fr);
            gap:18px;
            margin-bottom:25px;
        }

        .card{
           background:white;
            padding:22px;
            border-radius:16px;
            box-shadow:0 2px 12px rgba(0,0,0,.05);
        }

        .card small{
            color:#888;
            display:block;
            margin-bottom:8px;
        }

        .card h3{
            margin:0;
            font-size:28px;
            color:#2563eb;
        }

        .section{
            background:white;
            padding:22px;
            border-radius:16px;
            box-shadow:0 2px 12px rgba(0,0,0,.05);
            margin-bottom:25px;
        }

        .section h3{
            margin-top:0;
            margin-bottom:18px;
            color:#1f2937;
        }

        table{
            width:100%;
            border-collapse:collapse;
        }

        th{
            background:#f8fafc;
            color:#475569;
            font-size:13px;
        }

        th, td{
            padding:14px;
            border-bottom:1px solid #e5e7eb;
        }

        tr:hover{
            background:#fafafa;
        }

        .badge{
            padding:5px 10px;
            border-radius:999px;
            font-size:12px;
            font-weight:bold;
        }

        .admin{
            background:#dbeafe;
            color:#2563eb;
        }

        .user{
            background:#dcfce7;
            color:#16a34a;
        }

        .active{
            background:#e0f2fe;
            color:#0284c7;
        }

        .ready{
            background:#dcfce7;
            color:#16a34a;
        }

        .unfinished{
            background:#fee2e2;
            color:#dc2626;
        }

        .archived{
            background:#e5e7eb;
            color:#6b7280;
        }

        .archive-btn{
            background:#2563eb;
            color:white;
            padding:8px 12px;
            border-radius:8px;
            text-decoration:none;
            font-size:12px;
            font-weight:bold;
        }

        .archive-btn:hover{
            background:#1d4ed8;
        }

        #expenseChart{
            max-width:500px;
            margin:auto;
        }
    </style>
    <!-- -----------------------------------------------------DESIGN----------------------------------------------- -->

<!-- -----------------------------------------------------DESIGN----------------------------------------------- -->
</head>

<body>

<div class="top">
    <h2>Admin Dashboard</h2>
    <div>
        Admin: <?php echo $_SESSION["username"]; ?>
        &nbsp;
        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>

<div class="container">

    <div class="summary">
        <div class="card">
            <small>Total Users</small>
            <h3><?php echo $total_users; ?></h3>
        </div>

        <div class="card">
            <small>Total Groups</small>
            <h3><?php echo $total_groups; ?></h3>
        </div>

        <div class="card">
            <small>Archived</small>
            <h3><?php echo $total_archived; ?></h3>
        </div>

        <div class="card">
            <small>Total Expenses</small>
            <h3><?php echo $total_expenses; ?></h3>
        </div>

        <div class="card">
            <small>Total Money</small>
            <h3>NT$ <?php echo number_format($total_money,0); ?></h3>
        </div>
        
    </div>

    <div class="section">

    <h3>Expense Category Statistics</h3>

    <canvas id="expenseChart"></canvas>

</div>

    <div class="section">
        <h3>User Management</h3>

        <table>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
            </tr>

            <?php while($u = mysqli_fetch_assoc($users)){ ?>
                <tr>
                    <td><?php echo $u["username"]; ?></td>
                    <td><?php echo $u["email"]; ?></td>
                    <td>
                        <span class="badge <?php echo $u["role"]; ?>">
                            <?php echo $u["role"]; ?>
                        </span>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <div class="section">
        <h3>Group Management</h3>

        <table>
            <tr>
                <th>Group Name</th>
                <th>Creator</th>
                <th>Invite Code</th>
                <th>Status</th>
                <th>Action</th>
            </tr>

            <?php while($g = mysqli_fetch_assoc($groups)){ ?>

                <?php
                $group_id = $g["group_id"];

                $all_payments = mysqli_query($link,
                "SELECT payments.*
                 FROM payments
                 JOIN expenses ON payments.expense_id = expenses.expense_id
                 WHERE expenses.group_id='$group_id'");

                $all_payment_count = mysqli_num_rows($all_payments);

                $unfinished = mysqli_query($link,
                "SELECT payments.*
                 FROM payments
                 JOIN expenses ON payments.expense_id = expenses.expense_id
                 WHERE expenses.group_id='$group_id'
                 AND payments.status != 'confirmed'");

                $unfinished_count = mysqli_num_rows($unfinished);
                ?>

                <tr>
                    <td><?php echo $g["group_name"]; ?></td>
                    <td><?php echo $g["creator_name"]; ?></td>
                    <td><?php echo $g["invite_code"]; ?></td>

                    <td>
                        <?php if($g["status"] == "archived"){ ?>
                            <span class="badge archived">archived</span>
                        <?php } else if($all_payment_count == 0){ ?>
                            <span class="badge active">no bills</span>
                        <?php } else if($unfinished_count == 0){ ?>
                            <span class="badge ready">ready</span>
                        <?php } else { ?>
                            <span class="badge unfinished">unfinished</span>
                        <?php } ?>
                    </td>

                    <td>
                        <?php if($g["status"] != "archived" && $all_payment_count > 0 && $unfinished_count == 0){ ?>
                            <a href="archivegroup.php?group_id=<?php echo $g['group_id']; ?>"
                               class="archive-btn"
                               onclick="return confirm('Archive this group?');">
                                Archive
                            </a>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>

            <?php } ?>
        </table>
    </div>

    <div class="section">
        <h3>Bill Records</h3>

        <table>
            <tr>
                <th>Description</th>
                <th>Group</th>
                <th>Created By</th>
                <th>Date</th>
                <th>Amount</th>
            </tr>

            <?php while($b = mysqli_fetch_assoc($bills)){ ?>
                <tr>
                    <td><?php echo $b["title"]; ?></td>
                    <td><?php echo $b["group_name"]; ?></td>
                    <td><?php echo $b["creator_name"]; ?></td>
                    <td><?php echo $b["expense_date"]; ?></td>
                    <td>NT$ <?php echo number_format($b["amount"],0); ?></td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>
<script>

const ctx = document.getElementById('expenseChart');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: [
            'Food',
            'Transportation',
            'Accomodation',
            'Shopping',
            'Other'
        ],
        datasets: [{
            data: [
                <?php echo $food_total; ?>,
                <?php echo $transportation_total; ?>,
                <?php echo $accomodation_total; ?>,
                <?php echo $shopping_total; ?>,
                <?php echo $other_total; ?>
            ],
            backgroundColor: [
                '#3498db',
                '#2ecc71',
                '#f39c12',
                '#9b59b6',
                '#95a5a6'
            ]
        }]
    },
    options:{
        responsive:true
    }
});

</script>
</body>
</html>