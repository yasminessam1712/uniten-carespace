<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['userid'])) {
    header("Location: ../credentials/userlogin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Successful</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background:#f0f1ec;
            color: #2a2a2a;
        }

        .navbar {
            width: 100%;
            background-color: #5e5c4f;
            padding: 14px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-left {
            display: flex;
            align-items: center;
        }

        .navbar-left img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            margin-right: 14px;
        }

        .navbar-left span {
            font-size: 1.3rem;
            font-weight: 700;
            color: #fff;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar-right a {
            color: white;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .navbar-right a:hover {
            opacity: 0.85;
        }

        .dropdown {
            position: relative;
        }

        .dropbtn {
            background: none;
            border: none;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 6px;
            overflow: hidden;
        }

        .dropdown-content a {
            color: #333;
            padding: 10px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            font-weight: bold;
        }

        .dropdown-content a:hover {
            background-color: #f3f3f3;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .main {
            padding: 40px;
            max-width: 720px;
            margin: auto;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .success-box {
            background-color: #fff;
            padding: 50px 40px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }

        .success-box h2 {
            font-size: 26px;
            font-weight: 700;
            color: #21875c;
            margin-bottom: 14px;
        }

        .success-box p {
            font-size: 15px;
            color: #555;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .success-box a {
            display: inline-block;
            background-color: #5e5c4f;
            color: white;
            padding: 10px 22px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }

        .success-box a:hover {
            background-color: #49483a;
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
                <button class="dropbtn">Counsellor ▾</button>
                <div class="dropdown-content">
                    <a href="../book/therapist_page.php">View Counsellors</a>
                    <a href="../book/book_appointment.php">Book Appointment</a>
                </div>
            </div>
            <?php if (isset($_SESSION['userid'])): ?>
                <a href="../profile/userinformation.php">Profile</a>
                <a href="../credentials/logout.php">Logout</a>
            <?php else: ?>
                <a href="../credentials/userlogin.php">Login</a>
            <?php endif; ?>
        </div>
    </div>

<div class="main">
<div class="success-box">
    <h2>Your Booking Is Confirmed!</h2>
    <p>Thank you for booking a counselling session.<br> Your appointment location is at TA Building, Level 3.<br> Our team will reach out to you shortly with the next steps.</p>
    <a href="book_appointment.php">Back to Booking Page</a>
</div>

</div>
</body>
</html>
