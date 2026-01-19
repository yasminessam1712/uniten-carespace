<?php
$connection = mysqli_connect("localhost", "root", "root", "user") or die("Can't connect to server");

$username = $email = $first_name = $last_name = "";
$errors = [];
$register_success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim(mysqli_real_escape_string($connection, $_POST["username"]));
    $password = mysqli_real_escape_string($connection, $_POST["password"]);
    $confirm_password = mysqli_real_escape_string($connection, $_POST["confirm_password"]);
    $email = trim(mysqli_real_escape_string($connection, $_POST["email"]));
    $first_name = trim(mysqli_real_escape_string($connection, $_POST["first_name"]));
    $last_name = trim(mysqli_real_escape_string($connection, $_POST["last_name"]));
    $first_name = ucwords(strtolower($first_name)); 
    $last_name = ucwords(strtolower($last_name));   

    if (empty($first_name)) {
        $errors['first_name'] = "First Name cannot be empty.";
    }

    if (empty($last_name)) {
        $errors['last_name'] = "Last Name cannot be empty.";
    }


    if (empty($username)) {
        $errors['username'] = "Student ID cannot be empty.";
    } elseif (!preg_match('/^[A-Z]{2}\d{6,8}$/', strtoupper($username))) {
        $errors['username'] = "Must be a valid UNITEN ID (e.g., CB230123, IS01082525).";
    } else {
        $safe_username = mysqli_real_escape_string($connection, strtoupper($username));
        $check_username = mysqli_query($connection, "SELECT * FROM login WHERE username = '$safe_username'");
        if (mysqli_num_rows($check_username) > 0) {
            $errors['username'] = "Student ID is already taken.";
        }
    }

    if (strlen($password) < 8 || 
    !preg_match('/[A-Z]/', $password) ||         
    !preg_match('/[\W_]/', $password)) {         
    $errors['password'] = "Password must be at least 8 characters long and contain at least one uppercase letter and one special character.";
}


    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format.";
    } else {
        $check_email = mysqli_query($connection, "SELECT * FROM login WHERE email = '$email'");
        if (mysqli_num_rows($check_email) > 0) {
            $errors['email'] = "Email is already used.";
        }
    }


    if (empty($errors)) {
        $sql = "INSERT INTO login (username, password, email, first_name, last_name) 
                VALUES ('$username', '$password', '$email', '$first_name', '$last_name')";
        if (mysqli_query($connection, $sql)) {
            $register_success = "Registration successful! You may now login.";
            $username = $email = $first_name = $last_name = "";
        } else {
            $errors['general'] = "Something went wrong. Please try again.";
        }
    }

    mysqli_close($connection);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0; padding: 0;
            box-sizing: border-box;
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

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .back-button a {
            background-color: #5e5c51;
            color: #fff;
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
        }

        .container {
            display: flex;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            overflow: hidden;
            max-width: 700px;
            width: 100%;
        }

        .left-info {
            background-color: #5e5c51;
            color: white;
            flex: 1;
            padding: 25px 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 10px;
            line-height: 1.8;
        }

        .left-info img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
        }

        .left-info h1 {
            font-size: 16px;
        }

        .left-info p {
            font-size: 13px;
            text-align: center;
        }

        .right-register {
            flex: 1;
            padding: 25px 20px;
            background-color: #ffffff;
        }

        .right-register h2 {
            text-align: center;
            font-size: 18px;
            margin-bottom: 18px;
            color: #333;
        }

        .message {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 16px;
            color: #e74c3c;
            text-align: center;
        }

        .message.success {
            color: #2ecc71;
        }

        .input-group {
            margin-bottom: 12px;
        }

        .input-group label {
            display: block;
            font-weight: 600;
            color: #555;
            margin-bottom: 4px;
            font-size: 13px;
        }

        .input-group input {
            width: 100%;
            padding: 8px 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 13px;
        }

        .input-error {
            color: red;
            font-size: 12px;
            margin-top: 3px;
        }

        .phone-wrapper {
            display: flex;
        }

        .phone-wrapper span {
            background-color: #eee;
            padding: 8px 10px;
            border: 1px solid #ccc;
            border-right: none;
            border-radius: 6px 0 0 6px;
            font-size: 13px;
        }

        .phone-wrapper input {
            border-radius: 0 6px 6px 0;
            border: 1px solid #ccc;
            border-left: none;
            font-size: 13px;
            flex: 1;
            padding: 8px 10px;
        }

        .btn-container {
            margin-top: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }

        .btn {
            padding: 8px;
            background-color: #5e5c51;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            width: 100%;
        }

        .btn:hover {
            background-color: #4a483c;
        }

        .text-center {
            font-size: 13px;
            color: #444;
            margin-top: 8px;
        }

        @media (max-width: 768px) {
            .container {
                flex-direction: column;
                max-width: 90%;
                margin-top: 20px;
            }

            .back-button {
                top: 10px;
                left: 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="left-info">
        <img src="../../uploads/logo_official.png" alt="Logo">
        <h1>Create Your Account</h1>
        <p>
            Join Carespace to access counseling services and wellness tools.<br>
            We’re here to help.
        </p>
    </div>

    <div class="right-register">
        <h2>Register</h2>

        <?php if (!empty($register_success)): ?>
            <div class="message success"><?= $register_success ?></div>
        <?php elseif (!empty($errors['general'])): ?>
            <div class="message"><?= $errors['general'] ?></div>
        <?php endif; ?>

<form method="post" action="">
    <div class="input-group">
        <label for="username">Student ID</label>
        <input type="text" name="username" value="<?= htmlspecialchars($username) ?>" required>
        <?php if (!empty($errors['username'])): ?>
            <div class="input-error"><?= $errors['username'] ?></div>
        <?php endif; ?>
        <p style="font-size: 12px; color: grey;">Make sure to enter your correct student ID, this cannot be changed later.</p>  <!-- Added text below the Student ID input -->
    </div>

    <div class="input-group">
        <label for="email">Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
        <?php if (!empty($errors['email'])): ?>
            <div class="input-error"><?= $errors['email'] ?></div>
        <?php endif; ?>
    </div>

    <div class="input-group">
        <label for="first_name">First Name</label>
        <input type="text" name="first_name" value="<?= htmlspecialchars($first_name) ?>" required>
        <?php if (!empty($errors['first_name'])): ?>
            <div class="input-error"><?= $errors['first_name'] ?></div>
        <?php endif; ?>
        <p style="font-size: 12px; color: grey;">This cannot be changed later.</p>  <!-- Added text below the Student ID input -->
    </div>

    <div class="input-group">
        <label for="last_name">Last Name</label>
        <input type="text" name="last_name" value="<?= htmlspecialchars($last_name) ?>" required>
        <?php if (!empty($errors['last_name'])): ?>
            <div class="input-error"><?= $errors['last_name'] ?></div>
        <?php endif; ?>
        <p style="font-size: 12px; color: grey;">This cannot be changed later.</p> 
    </div>

    <div class="input-group">
        <label for="password">Password</label>
        <input type="password" name="password" required>
        <?php if (!empty($errors['password'])): ?>
            <div class="input-error"><?= $errors['password'] ?></div>
        <?php endif; ?>
    </div>

    <div class="input-group">
        <label for="confirm_password">Confirm Password</label>
        <input type="password" name="confirm_password" required>
        <?php if (!empty($errors['confirm_password'])): ?>
            <div class="input-error"><?= $errors['confirm_password'] ?></div>
        <?php endif; ?>
    </div>

    <div class="btn-container">
        <button type="submit" class="btn">Register</button>
        <div class="text-center">Already have an account?</div>
        <a href="userlogin.php" class="btn">Login</a>
        <a class="btn" href="../../mainpage.php" style="background-color: #ccc; color: #333;">Back</a>
    </div>
</form>

    </div>
</div>


</body>
</html>

