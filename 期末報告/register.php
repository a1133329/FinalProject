<?php
$link = mysqli_connect('localhost', 'root', '', 'final_project');

if (!$link) {
    die("Fail to connect!");
}

$message = "";
$message_type = "";

if(isset($_POST["register"])){

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $check = mysqli_query($link,
    "SELECT *
    FROM users
    WHERE email='$email'
    OR username='$username'");

    if(mysqli_num_rows($check) > 0){

        $message = "Username or Email already exists.";
        $message_type = "danger";

    }
    else{

        $sql = "INSERT INTO users(username,email,password,role)
                VALUES('$username','$email','$password','user')";

        if(mysqli_query($link,$sql)){

            header("Location: login.php?registered=1");
            exit();

        }
        else{

            $message = "Error: " . mysqli_error($link);
            $message_type = "danger";

        }
    }
}
?>

<html>
<head>
<meta charset="UTF-8">
<title>Register - SharePay</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Arial,sans-serif;
}

body{
    min-height:100vh;
    background:#f6f1e8;
    display:flex;
    justify-content:center;
    align-items:center;
}

.container{
    width:400px;
    padding:40px;
    background:#fffaf3;
    border:1px solid #e7ddcf;
    border-radius:20px;
    box-shadow:0 10px 25px rgba(0,0,0,0.08);
}

h2{
    text-align:center;
    color:#8b6f47;
    margin-bottom:6px;
}

.subtitle{
    text-align:center;
    color:#777;
    font-size:13px;
    margin-bottom:20px;
}

.input-group{
    margin-bottom:15px;
}

.input-group input{
    width:100%;
    padding:12px 15px;
    border-radius:12px;
    border:1px solid #e6dccd;
    background:#fff;
    outline:none;
}

.input-group input:focus{
    border-color:#8b6f47;
}

.btn-submit{
    width:100%;
    padding:12px;
    background:#8b6f47;
    color:white;
    border:none;
    border-radius:12px;
    cursor:pointer;
    font-weight:bold;
}

.btn-submit:hover{
    background:#755c3a;
}

.footer-link{
    margin-top:15px;
    text-align:center;
    font-size:13px;
}

.footer-link a{
    color:#8b6f47;
    text-decoration:none;
    font-weight:bold;
}

.alert{
    padding:10px;
    border-radius:10px;
    margin-bottom:15px;
    text-align:center;
    font-size:13px;
    background:#fdecea;
    color:#c0392b;
    border:1px solid #f5c6cb;
}
</style>
</head>

<body>

<div class="container">
    <h2>SharePay</h2>
    <div class="subtitle">Create your account</div>

    <?php if(!empty($message)){ ?>
        <div class="alert">
            <?php echo $message; ?>
        </div>
    <?php } ?>

    <form method="POST">
        <div class="input-group">
            <input type="text" name="username" placeholder="Username" required>
        </div>

        <div class="input-group">
            <input type="email" name="email" placeholder="Email Address" required>
        </div>

        <div class="input-group">
            <input type="password" name="password" placeholder="Password" required>
        </div>

        <button class="btn-submit" name="register" type="submit">
            Create Account
        </button>
    </form>

    <div class="footer-link">
        Already have an account? <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>

