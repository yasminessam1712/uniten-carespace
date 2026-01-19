<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn_user = new mysqli("localhost", "root", "root", "user");
if ($conn_user->connect_error) die("Connection failed: " . $conn_user->connect_error);

if (!isset($_SESSION['userid'])) {
    header("Location: ../credentials/userlogin.php");
    exit();
}

$username = $_SESSION['userid'];
$user_result = mysqli_query($conn_user, "SELECT * FROM login WHERE username = '$username'");
$profile = mysqli_fetch_assoc($user_result);
$current_pass = $profile['password'];
$current_email = $profile['email'];

$success_password = false;
$success_email = false;
$error_password = '';
$error_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
    $old_password_input = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $re_enter_password = $_POST['re_enter_password'];

    if  ($old_password_input !== $current_pass){
        $error_password = "Incorrect old password.";
    
    } elseif (empty($new_password)) {
        $error_password = "New password cannot be empty.";
    } elseif ($new_password !== $re_enter_password) {
        $error_password = "New password and re-entered password do not match.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $new_password)) {
        $error_password = "Password must be at least 8 characters and include upper/lowercase letters, numbers, and a special character.";
    
    } else {

        $update = "UPDATE login SET password='$new_password' WHERE username='$username'";
        if (mysqli_query($conn_user, $update)) {
            $success_password = true;
        } else {
            $error_password = "Error updating password. Please try again.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_email'])) {
    $new_email = $_POST['new_email'];


    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL) || !preg_match('/\.(com|my|edu)$/i', $new_email)) {
        $error_email = "Invalid email format.";
    } elseif ($new_email === $current_email) {
        $error_email = "New email you entered is the same as the current one.";
    } else {

        $check_email_query = "SELECT * FROM login WHERE email = '$new_email' AND username != '$username'"; // Exclude current user's email
        $result = mysqli_query($conn_user, $check_email_query);
        
        if ($result === false) {

            $error_email = "There was an error checking the email. Please try again.";
        } elseif (mysqli_num_rows($result) > 0) {
            $error_email = "The email you entered is already in use. Please use a different one.";
        } else {

            $update_email = "UPDATE login SET email='$new_email' WHERE username='$username'";
            $update_appointments = "UPDATE therapist.appointments SET email='$new_email' WHERE email='$current_email'"; // therapist is the DB, appointments is the table

            if (mysqli_query($conn_user, $update_email) && mysqli_query($conn_user, $update_appointments)) {
                $success_email = true;

                $current_email = $new_email;
            } else {
                $error_email = "Error updating email. Please try again.";
            }
        }
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>User Information</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background: #f0f1ec; color: #2a2a2a; font-size: 14px; }

        .navbar {
            width: 100%; background-color: #5e5c4f; padding: 14px 32px;
            display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100;
        }

        .navbar-left { display: flex; align-items: center; }
        .navbar-left img { width: 42px; height: 42px; border-radius: 50%; margin-right: 14px; }
        .navbar-left span { font-size: 1.3rem; font-weight: 700; color: white; }

        .navbar-right { display: flex; align-items: center; gap: 20px; }
        .navbar-right a, .dropbtn {
            color: white; text-decoration: none; font-size: 15px; font-weight: 600; background: none; border: none; cursor: pointer;
        }
        .navbar-right a:hover, .dropbtn:hover { opacity: 0.85; }

        .dropdown { position: relative; }
        .dropdown-content {
            display: none; position: absolute; background-color: white; min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.1); border-radius: 6px; z-index: 1;
        }
        .dropdown-content a {
            color: #333; padding: 8px 14px; text-decoration: none; display: block;
            font-weight: 500; font-size: 13px;
        }
        .dropdown-content a:hover { background-color: #f0f0f0; }
        .dropdown:hover .dropdown-content { display: block; }

        .main {
            padding: 40px;
            display: flex;
            justify-content: center;
        }

        .profile-box {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            width: 100%;
            max-width: 600px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 20px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
        }

        .form-group input[type="password"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }
        .form-group input[type="email"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        button[name="update_password"] {
            background-color: #5e5c4f;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }
        button[name="update_email"] {
            background-color: #5e5c4f;
            color: white;
            border: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }

        .back-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 18px;
            text-decoration: none;
            background-color: #e0e0e0;
            color: #333;
            border-radius: 6px;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }

        .back-btn:hover {
            background-color: #d5d5d5;
            color: #000;
        }

        .error-message {
            background-color: #f8d7da; 
            color: #721c24; 
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: bold;
        }

        .success-message {
            background-color: #d4edda; 
            color: #155724; 
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: bold;
        }

    </style>
</head>
<body>

<div class="navbar">
    <div class="navbar-left">
        <img src="../../uploads/logo_official.png" alt="Logo">
        <span>UNITEN CARESPACE</span>
    </div>
    <div class="navbar-right">
        <a href="../../mainpage.php">Home</a>
        <a href="../articles/view_articles.php">Articles</a>
        <a href="../dass/dass_intro.php">DASS Test</a>
        <div class="dropdown">
            <button class="dropbtn">Counsellor <span>▾</span></button>
            <div class="dropdown-content">
                <a href="../book/therapist_page.php">View Counsellors</a>
                <a href="../book/book_appointment.php">Book Appointment</a>
            </div>
        </div>
        <?php if (isset($_SESSION['userid'])): ?>
            <a href="../profile/userinformation.php">Profile</a>
            <a href="../credentials/logout.php" id="logout-link">Logout</a>
        <?php else: ?>
            <a href="../credentials/userlogin.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="main">
    <div class="profile-box">
                <h2>Update Email</h2>

        <?php if ($error_email): ?>
            <div class="error-message"><?= $error_email ?></div>
        <?php endif; ?>

        <?php if ($success_email): ?>
            <div class="success-message">Your email has been updated successfully.</div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>New Email</label>
                <input type="email" name="new_email" value="<?= htmlspecialchars($current_email) ?>" required>
            </div>
            <button type="submit" name="update_email">Update Email</button>
        </form><br>
        
        <h2>Update Password</h2>
        
        <?php if ($error_password): ?>
            <div class="error-message"><?= $error_password ?></div>
        <?php endif; ?>

        <?php if ($success_password): ?>
            <div class="success-message">Your password has been updated successfully.</div>
        <?php endif; ?>

        <form method="POST">
            
            <div class="form-group">
                <label>Old Password</label>
                <input type="password" name="old_password" required>
            </div>
            <div class="form-group">
                <label>New Password</label>
                <input type="password" name="new_password" required>
            </div>
            <div class="form-group">
                <label>Re-enter New Password</label>
                <input type="password" name="re_enter_password" required>
            </div>
            <button type="submit" name="update_password">Update Password</button>
        </form>
        
        <a class="back-btn" href="userinformation.php">Back to Profile</a>
    </div>
</div>

</body>
</html>
 
