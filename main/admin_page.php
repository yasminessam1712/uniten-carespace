<?php 
session_start(); 
include_once("config.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin/credentials/adminlogin.php");
    exit();
}

$counsellor_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'counsellors.jpeg' LIMIT 1");
$counsellor_picture = mysqli_fetch_assoc($counsellor_result);

$logo_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'logo_official.png' LIMIT 1");
$logo_picture = mysqli_fetch_assoc($logo_result);

$articles_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'articles.jpg' LIMIT 1");
$articles_picture = mysqli_fetch_assoc($articles_result);

$dass_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'dass.png' LIMIT 1");
$dass_picture = mysqli_fetch_assoc($dass_result);


$conn_therapist = new mysqli("localhost", "root", "root", "therapist");
if ($conn_therapist->connect_error) die("Connection failed: " . $conn_therapist->connect_error);


$today = date("Y-m-d");

$completedAppointments = mysqli_fetch_assoc(mysqli_query(
    $conn_therapist,
    "
    SELECT COUNT(*) as count 
    FROM appointments a
    JOIN availability v ON a.availability_id = v.id
    WHERE a.status = 'completed'
    AND v.date = '$today'
    "
))['count'];




$bookingData = [];
$bookingLabels = [];
$startOfWeek = strtotime("monday this week");
for ($i = 0; $i < 7; $i++) {
    $day = date("Y-m-d", strtotime("+$i day", $startOfWeek));
    $dayLabel = date("D, d M", strtotime($day));
    $bookingLabels[] = $dayLabel;

    $stmt = $conn_therapist->prepare("SELECT COUNT(*) as count FROM appointments WHERE DATE(created_at) = ?");
    $stmt->bind_param("s", $day);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $bookingData[] = $row['count'];
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container {
            width: 100%; 
            height: 400px;
            max-width: 800px;
            margin: 2rem auto;
            background: #fff;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            position: relative;
        }
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

        #splash h1, #splash p {
            color: white;
            opacity: 0;
        }

        #splash h1 {
            font-size: 28px;
            font-weight: 700;
            animation: fadeInText 1s ease 1s forwards;
        }

        #splash p {
            font-size: 16px;
            font-weight: 400;
            margin-top: 8px;
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

        .dropdown:hover .dropdown-content {
            display: block;
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
        .today-heading {
            text-align: center;
            margin-top: 1rem;
            font-size: 1.6rem;
            font-weight: 700;
            color: #333;
        }

        

        .stats-cards {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 3rem auto;
            max-width: 1000px;
            flex-wrap: wrap;
        }

        .stat-box {
            background: #ffffff;
            padding: 1.0rem 1.5rem;            
            border-radius: 8px;              
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);  
            text-align: center;
            min-width: 80px;               
            max-width: 250px;              
            flex: 1;
            font-size: 14px;                 
        }


        .stat-box h2 {
            font-size: 2.2rem;
            color: #5e5c51;
            margin-bottom: 0.5rem;
        }

        .stat-box p {
            font-size: 1rem;
            color: #555;
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
            width: 300px;
            height: 340px;
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
            height: 180px;
            object-fit: cover;
            border-radius: 8px;
            width: 100%;
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
    </style>
</head>
<body>


<div id="splash">
    <img src="<?= $logo_picture['filepath']; ?>" alt="Logo">
    <h1>UNITEN Admin Panel</h1>
    <p>Loading administrative interface...</p>
</div>

<div class="page-content">
<nav>
    <div class="brand">
        <img src="<?= $logo_picture['filepath']; ?>" alt="Logo">
        UNITEN CARESPACE (Admin)
    </div>
    <div class="nav-right">
        <a href="admin_page.php">Dashboard</a>
        <a href="admin/appointments/view_therapist.php">Counsellors</a>
        <div class="dropdown">
            <button class="dropbtn">Articles ▾</button>
            <div class="dropdown-content">
                <a href="admin/articles/publish_article.php">Publish Articles</a>
                <a href="admin//articles/manage_articles.php">Manage Articles</a>
            </div>
        </div>
        <div class="dropdown">
            <button class="dropbtn">Appointments ▾</button>
            <div class="dropdown-content">
                <a href="admin/appointments/view_appointments.php">View Appointments</a>
                <a href="admin/appointments/sumary_therapist.php">Summary</a>
                <a href="admin/appointments/review_cancellations.php">Cancellation</a>
                
            </div>
        </div>
        <a href="admin/credentials/logout.php" id="logout-link">Logout</a>
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
      window.location.href = this.href; t
    }
  });
});
</script>
    </div>
</nav>

    <div class="hero">
        <h1>Welcome Back Admin!</h1>
        <p class="desc">Welcome to the administrative interface of UNITEN Carespace.</p>
        <p class="desc">Manage student sessions, therapist schedules, published articles, and mental health insights across the platform.</p>
    </div>

    <div class="today-heading">Today's Statistics (<?= date("d M Y") ?>)</div>

    <div class="stats-cards">
        <div class="stat-box">
            <h2><?= $completedAppointments ?></h2>
            <p>Completed Sessions</p>
        </div>
    </div>
    <div class="chart-container">
    <canvas id="lineChart"></canvas>
</div>

    <div class="features">
        <a href="admin/articles/manage_articles.php" style="text-decoration: none; color: inherit;">
            <div class="feature-card">
                <img src="<?= $articles_picture['filepath']; ?>" alt="Articles">
                <h3>Manage Articles</h3>
                <p>Create, edit, or remove mental health articles and content for students.</p>
            </div>
        </a>
        <a href="admin/appointments/view_appointments.php" style="text-decoration: none; color: inherit;">
            <div class="feature-card">
                <img src="<?= $counsellor_picture['filepath']; ?>" alt="Counseling">
                <h3>Appointments</h3>
                <p>Track and manage student bookings and therapist schedules.</p>
            </div>
        </a>
     
    </div>

    <div class="about-section">
        <div class="about-container">
            <h2>Admin Support</h2>
            <p>Need help managing Carespace? Reach out to the system administrator or technical support team below.</p>
            <div class="about-info">
                <div>
                    <p><strong>Email</strong></p>
                    <p>
                    <a href="mailto:yasminessam1712@gmail.com" style="text-decoration:none; color:#3b5998;">
                        Technical Support
                    </a>
                    </p>
                </div>
                <div>
                    <p>Location</p>
                    <p>UNITEN, Jalan Kajang-Puchong,<br>43000 Kajang, Selangor</p>
                </div>
                <div>
                    <p>Phone</p>
                    <p>+60 19 669 2702</p>
                </div>
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

<script>
 const ctx = document.getElementById('lineChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($bookingLabels) ?>,
        datasets: [{
            label: 'Bookings per Day (<?= date("d M", $startOfWeek) ?> - <?= date("d M Y", strtotime("+6 days", $startOfWeek)) ?>)',
            data: <?= json_encode($bookingData) ?>,
            borderColor: '#5e5c51',
            backgroundColor: 'rgba(149, 190, 111, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointBackgroundColor: '#5e5c51'
        }]
    },
    options: {
        responsive: true,
        aspectRatio: 2, 
        maintainAspectRatio: true, 
        plugins: {
            title: {
                display: true,
                text: 'Weekly Appointment Trend',
                font: { size: 18 }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        }
    }
});

</script>
</body>
</html>
