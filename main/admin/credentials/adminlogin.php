<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $connection = mysqli_connect("localhost", "root", "root", "admin") or die("Can't connect to server");

    $username = mysqli_real_escape_string($connection, $_POST["username"]);
    $password = mysqli_real_escape_string($connection, $_POST["password"]);

    $sql = "SELECT * FROM login WHERE username = '$username'";
    $result = mysqli_query($connection, $sql);

    if (!$result || mysqli_num_rows($result) == 0) {
        $login_error = "Username does not exist.";
    } else {
        $data = mysqli_fetch_array($result, MYSQLI_BOTH);
        if ($data['password'] == $password) {
            $_SESSION["userid"] = $username;
            $_SESSION["admin"] = true; // ✅ This is needed for admin access
            header("Location: ../../admin_page.php");
            exit();
        
        } else {
            $login_error = "Incorrect password.";
        }
    }

    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f0f1ec;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .container {
            display: flex;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            max-width: 750px;
            width: 100%;
        }

        .left-info {
            background-color: #5e5c51;
            color: white;
            flex: 1;
            padding: 30px 25px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .left-info img {
            width: 70px;
            height: 70px;
            object-fit: contain;
            border-radius: 50%;
            margin-bottom: 15px;
        }

        .left-info h1 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .left-info p {
            font-size: 13px;
            line-height: 1.6;
            text-align: center;
        }

        .right-login {
            flex: 1;
            padding: 30px 25px;
            background-color: #ffffff;
        }

        .right-login h2 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 18px;
            color: #333;
        }

        .error-msg {
            color: #e74c3c;
            font-size: 13px;
            text-align: center;
            margin-bottom: 12px;
        }

        .input-group {
            margin-bottom: 14px;
        }

        .input-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 14px;
        }

        .btn-container {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .btn {
            padding: 8px;
            background-color: #5e5c51;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            font-size: 13px;
        }

        .btn:hover {
            background-color: #4a483c;
        }

        .register-text {
            text-align: center;
            font-size: 13px;
            margin-top: 10px;
            color: #555;
        }

        a.btn {
            display: inline-block;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="left-info">
        <img src="../../uploads/logo_official.png" alt="Logo">
        <h1>Welcome Admin</h1>
        <p>Access the dashboard to manage users, articles, appointments and more.</p>
    </div>

    <div class="right-login">
        <h2>Login as Admin</h2>
        <?php if (!empty($login_error)): ?>
            <div class="error-msg"><?= $login_error ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn">Login</button>
                <a class="btn" href="../../mainpage.php" style="background-color: #ccc; color: #333;">Back</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
