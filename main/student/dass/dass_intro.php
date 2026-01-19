<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>DASS Test Intro - UNITEN Carespace</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            font-family: 'Poppins', sans-serif;
            background: #f0f1ec;
            color: #333;
            overflow-x: hidden; 
            width: 100%;
        }

        .navbar {
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
            margin-right: 12px;
        }

        .navbar-left span {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
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

        .container {
            max-width: 800px;
            margin: 60px auto;
            background: white;
            padding: 40px 24px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            text-align: center;
        }

        h1 {
            font-size: 28px;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
        }

        .start-btn {
            margin-top: 30px;
            display: inline-block;
            padding: 12px 24px;
            background-color: #5e5c51;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
        }

        .start-btn:hover {
            background-color: #4b4a41;
        }

        @media screen and (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar-right {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                margin-top: 10px;
            }

            .navbar-left span {
                font-size: 1.1rem;
            }

            .container {
                margin: 40px 20px;
                padding: 30px 20px;
            }
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
            <a href="../credentials/logout.php" id="logout-link">Logout</a>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('logout-link').addEventListener('click', function(e) {
  e.preventDefault(); 
  Swal.fire({
    title: 'Are you sure?',
    text: "You will be logged out.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#5e5c51',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, logout!'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = this.href; 
    }
  });
});
</script>
        <?php else: ?>
            <a href="../credentials/userlogin.php">Login</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <h1>Welcome to the DASS-21 Test</h1>
    <p>The DASS test is a psychological assessment tool to evaluate your levels of depression, anxiety, and stress. This short test consists of 21 questions based on how you've felt over the past week.</p>
    <p>To proceed and save your results in our system, please log in first.</p>
    
    <?php if (!isset($_SESSION['userid'])): ?>
        <a class="start-btn" href="../credentials/userlogin.php?redirect=dass/dass_test.php">Login to Begin</a>
    <?php else: ?>
        <a class="start-btn" href="dass_test.php">Start Test</a>
    <?php endif; ?>
</div>

</body>
</html>

