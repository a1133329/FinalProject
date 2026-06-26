<?php
session_start();

$link = mysqli_connect('localhost', 'root', '', 'final_project');

if (!$link) {
    die("Fail to connect!");
}

$message = "";
$message_type = "";

if(isset($_POST["login"])){

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($link,$sql);

    if(mysqli_num_rows($result)==1){

        $row = mysqli_fetch_assoc($result);

        if(password_verify($password,$row["password"])){

            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["role"] = $row["role"];

            if($row["role"]=="admin"){
                header("Location: admin_dashboard.php");
            }
            else{
                header("Location: dashboard.php");
            }
            exit();
        }
        else{
            $message = "Wrong Password";
            $message_type = "danger";
        }
    }
    else{
        $message = "Email Not Found";
        $message_type = "danger";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Login - SharePay</title>

<!-- -----------------------------------------------------DESIGN----------------------------------------------- -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

body {
    min-height: 100vh;
    background: #f6f1e8;
    display: flex;
    justify-content: center;
    align-items: center;
}

.container {
    width: 400px;
    padding: 40px;
    background: #fffdf9;
    border-radius: 18px;
    border: 1px solid #ebe3d5;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

        h2 {
            text-align: center;
            color:#8b6f47;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            font-size: 13px;
            color: #777;
            margin-bottom: 24px;
        }

        .input-group {
            margin-bottom: 18px;
        }

        .input-group input {
            width: 100%;
            padding: 13px 18px;
            background: #f9fff9;
            border: 2px solid #e8f7ee;
            border-radius: 22px;
            outline: none;
            font-size: 14px;
            color: #4a4a4a;
        }

        .input-group input:focus {
            border-color: #2ecc71;
            background: white;
        }

        .btn-submit {
            width: 100%;
            padding: 13px;
            background:#8b6f47;
            border: none;
            border-radius: 22px;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-submit:hover {
            background:#755c3a;
        }

        .footer-link {
            margin-top: 18px;
            text-align: center;
            font-size: 13px;
        }

        .footer-link a {
            color:#8b6f47;
            text-decoration: none;
            font-weight: bold;
        }

        .alert {
            padding: 10px;
            border-radius: 12px;
            margin-bottom: 18px;
            font-size: 13px;
            text-align: center;
        }

        .alert-danger {
            background: #fff0f0;
            border: 1px solid #ff8080;
            color: #e74c3c;
        }
    </style>
    <!-- -----------------------------------------------------DESIGN----------------------------------------------- -->
     
</head>

<body>


<div class="container">
    <h2>SharePay</h2>
    <div class="subtitle">Login to your account</div>

    <?php if(!empty($message)){ ?>
        <div class="alert alert-<?php echo $message_type; ?>">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST">
        <div class="input-group">
            <input type="email" name="email" required placeholder="Email Address">
        </div>

        <div class="input-group">
            <input type="password" name="password" required placeholder="Password">
        </div>

        <button type="submit" name="login" class="btn-submit">Login</button>
    </form>

    <div class="footer-link">
        Don't have an account? <a href="register.php">Create one</a>
    </div>
</div>

</body>
</html>