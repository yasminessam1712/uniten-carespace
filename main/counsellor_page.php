<?php 
session_start(); 
include_once("config.php");
if (!isset($_SESSION['therapist_id'])) {
    header("Location: counsellor/credentials/counsellor_login.php");
    exit();
}

$counsellor_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'counsellors.jpeg' LIMIT 1");
$counsellor_picture = mysqli_fetch_assoc($counsellor_result);

$logo_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'logo_official.png' LIMIT 1");
$logo_picture = mysqli_fetch_assoc($logo_result);

$articles_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'articles.jpg' LIMIT 1");
$articles_picture = mysqli_fetch_assoc($articles_result);

$nowDate = date('Y-m-d');
$nowTime = date('H:i:s');
?>

<?php
$conn = mysqli_connect("localhost", "root", "root", "therapist");

$therapist_id = $_SESSION['therapist_id']; 

date_default_timezone_set('Asia/Kuala_Lumpur'); // ✅ Make sure you're using the correct timezone
$nowDate = date('Y-m-d');
$nowTime = date('H:i:s');

$nextAppointmentsQuery = 
    "
    SELECT 
        a.id, 
        a.student_name, 
        v.date, 
        v.start_time, 
        v.end_time, 
        a.status
    FROM appointments a
    JOIN availability v ON a.availability_id = v.id
    WHERE a.therapist_id = ?
    AND a.status != 'cancelled'
    AND (
        v.date > ? OR
        (v.date = ? AND v.start_time >= ?)
    )
    ORDER BY v.date ASC, v.start_time ASC
    LIMIT 3
    ";

$stmt = mysqli_prepare($conn, $nextAppointmentsQuery);
mysqli_stmt_bind_param($stmt, "ssss", $therapist_id, $nowDate, $nowDate, $nowTime);
mysqli_stmt_execute($stmt);
$resultNext = mysqli_stmt_get_result($stmt);
$nextAppointments = [];
while ($row = mysqli_fetch_assoc($resultNext)) {
    $nextAppointments[] = $row;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f0f1ec;
            color: #1d1d1f;
        }

        #splash {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: #5e5c51;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            animation: fadeOut 1s ease 2.8s forwards;
        }

        #splash img {
            width: 120px;
            height: 120px;
            animation: popUp 1s ease;
            border-radius: 50%;
            margin-bottom: 20px;
        }

        #splash h1 {
            color: white;
            font-size: 28px;
            font-weight: 700;
            opacity: 0;
            animation: fadeInText 1s ease 1s forwards;
        }

        #splash p {
            color: white;
            font-size: 16px;
            font-weight: 400;
            margin-top: 8px;
            opacity: 0;
            animation: fadeInText 1s ease 1.5s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

        @keyframes popUp {
            0% {
                transform: scale(0.7);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes fadeInText {
            to {
                opacity: 1;
            }
        }

        .page-content {
            opacity: 0;
            transform: translateX(-50px);
            animation: slideIn 1s ease-out 2.9s forwards;
        }

        @keyframes slideIn {
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 32px;
            background: #5e5c51;
        }

        nav .brand {
            display: flex;
            align-items: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: white;
        }

        nav .brand img {
            height: 48px;
            width: 48px;
            border-radius: 50%;
            margin-right: 12px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-right a,
        .dropbtn {
            color: white;
            font-size: 15px;
            font-weight: 600;
            text-decoration: none;
            background: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .nav-right a:hover,
        .dropdown-content a:hover,
        .dropbtn:hover {
            opacity: 0.85;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.2);
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
            font-weight: 600;
        }

        .dropdown-content a:hover {
            background-color: #f3f3f3;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .hero {
            text-align: center;
            padding: 4rem 1rem;
        }

        .hero h1 {
            font-size: 2.6rem;
            font-weight: 900;
            margin-bottom: 1rem;
        }

        .hero p.desc {
            font-size: 1.1rem;
            color: #555;
            margin-top: 10px;
            line-height: 1.5;
            text-align: center;
        }

        .hero a.button {
            margin-top: 1.5rem;
            display: inline-block;
            background: #5e5c51;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 700;
        }

        .features {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
            padding: 2rem 1rem;
        }

        .feature-card {
            background: white;
            width: 280px;
            padding: 1.2rem;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.07);
            text-align: center;
            transition: transform 0.2s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            color: #222;
        }

        .feature-card p {
            font-size: 0.95rem;
            color: #555;
        }

        .about-section {
            background: #f7f7f7;
            padding: 4rem 2rem;
            text-align: center;
            border-top: 2px solid #ddd;
        }

        .about-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-container h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #333;
        }

        .about-container p {
            font-size: 1.1rem;
            color: #555;
            line-height: 1.8;
            margin-bottom: 2rem;
        }

        .about-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
            margin-top: 2rem;
            padding: 2rem;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .about-info div {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            flex: 1;
            min-width: 250px;
            max-width: 300px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .about-info div:hover {
            transform: translateY(-8px);
        }

        .about-info p:first-child {
            font-weight: 600;
            font-size: 1.2rem;
            color: #333;
            margin-bottom: 1rem;
        }

        .about-info p:last-child {
            font-size: 1rem;
            color: #666;
        }
        .reminder-container {
            background-color: #d4edda; 
            border: 1.5px solid #155724; 
            color: #155724;
            padding: 20px 30px;
            margin: 20px auto 40px auto;
            max-width: 900px;
            border-radius: 10px;
            font-weight: 600;
        }

        .reminder-container h2 {
            margin-bottom: 15px;
        }

        .reminder-container ul {
            list-style: none;
            padding-left: 0;
        }

        .reminder-container li {
            margin-bottom: 10px;
            font-size: 1rem;
        }

    </style>
</head>
<body>


<div id="splash">
    <img src="<?= $logo_picture['filepath']; ?>" alt="Logo">
    <h1>UNITEN Carespace</h1>
    <p>Your mind matters most.</p>
</div>

<div class="page-content">
<nav>
    <div class="brand">
        <img src="<?= $logo_picture['filepath']; ?>" alt="Logo">
        UNITEN CARESPACE (Counsellor)
    </div>
    <div class="nav-right">
        <a href="counsellor_page.php">Dashboard</a>
        <div class="dropdown">
            <button class="dropbtn">Appointments ▾</button>
            <div class="dropdown-content">
                <a href="counsellor/appointments/view_appointments.php">View Appointments</a>
                <a href="counsellor/appointments/therapist_availability.php">Set Availability</a>
                
            </div>
        </div>
        <a href="counsellor/profile/counsellor_profile.php">Profile</a>
        <a href="counsellor/credentials/logout.php" id="logout-link">Logout</a>
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
    </div>
</nav>

    <div class="hero">
    <h1>Your role matters.</h1>
        <p class="desc">Support students with care and confidentiality.</p>
        <p class="desc">As a UNITEN Carespace counsellor, you play a vital role in guiding students through<br> their mental health journeys.From managing appointments to providing meaningful sessionn. <br>Your support makes a difference.</p>
        <a href="counsellor/appointments/view_appointments.php" class="button">View Appointments</a>

    </div>
    <div class="reminder-container">
    <h2>Upcoming Appointments</h2>
    <?php if (empty($nextAppointments)): ?>
        <p>No upcoming appointments.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($nextAppointments as $appt): ?>
                <li>
                    <strong><?= htmlspecialchars($appt['student_name']) ?></strong> —
                    <?= date('d-m-Y', strtotime($appt['date'])) ?>
                    <?= date('g:i A', strtotime($appt['start_time'])) ?> - <?= date('g:i A', strtotime($appt['end_time'])) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>


    <div class="features">
        <a href="counsellor/appointments/view_appointments.php" style="text-decoration: none; color: inherit;">
            <div class="feature-card">
                <img src="<?= $counsellor_picture['filepath']; ?>" alt="Counseling">
                <h3>Manage Your Appointments</h3>
                <p>View and manage your upcoming sessions with students easily and securely.</p>

            </div>
        </a>
        <a href="counsellor/appointments/therapist_availability.php" style="text-decoration: none; color: inherit;">
            <div class="feature-card">
                <img src="<?= $articles_picture['filepath']; ?>" alt="Articles">
                <h3>Manage Your Availability</h3>
                <p>Set your available time slots and view your schedule to better assist students in need.</p>

            </div>
        </a>
    </div>

    <div class="about-section">
    <div class="about-container">
        <h2>About Us</h2>
        <p>As a UNITEN Carespace counsellor, you are a vital part of our mission to support student mental health. We provide a safe, confidential platform where you can guide students through their challenges, promote resilience, and foster well-being.</p>
        <p>Through your dedication, workshops, and one-on-one sessions, you help shape a healthier campus community where students feel heard, supported, and empowered.</p>
        <div class="about-info">
            <div>
                <p>Email</p>
                <a href="mailto:yasminessam1712@gmail.com" style="text-decoration:none; color:#3b5998;">
                Counsellor Support
                </a>
            </div>
            <div>
                <p>Address</p>
                <p>UNITEN, Jalan Kajang-Puchong,<br>43000 Kajang, Selangor</p>
            </div>
            <div>
                <p>Phone</p>
                <p>+60 12 345 6789</p>
            </div>
        </div>
    </div>
</div>

<script>
    if (sessionStorage.getItem("splashShown")) {
        document.getElementById("splash").style.display = "none";
        document.querySelector(".page-content").style.opacity = "1";
        document.querySelector(".page-content").style.transform = "translateX(0)";
    } else {
        sessionStorage.setItem("splashShown", "true");
    }
</script>

</body>
</html>
