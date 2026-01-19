<?php 
session_start(); 
include_once("config.php");

$counsellor_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'counsellors.jpeg' LIMIT 1");
$counsellor_picture = mysqli_fetch_assoc($counsellor_result);

$logo_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'logo_official.png' LIMIT 1");
$logo_picture = mysqli_fetch_assoc($logo_result);

$articles_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'articles.jpg' LIMIT 1");
$articles_picture = mysqli_fetch_assoc($articles_result);

$dass_result = mysqli_query($conn, "SELECT * FROM pictures WHERE filename = 'dass.png' LIMIT 1");
$dass_picture = mysqli_fetch_assoc($dass_result);


$conn_user = new mysqli("localhost", "root", "root", "therapist");
if ($conn_user->connect_error) die("Connection failed: " . $conn_user->connect_error);

$upcomingAppointment = null;
if (isset($_SESSION['userid'])) {
    $userid = $_SESSION['userid'];
    $now = date('Y-m-d H:i:s');


    $user_conn = new mysqli("localhost", "root", "root", "user");
    if ($user_conn->connect_error) {
        die("User DB Connection failed: " . $user_conn->connect_error);
    }

    $user_query = $user_conn->prepare("SELECT email FROM login WHERE username = ?");
    $user_query->bind_param("s", $userid);
    $user_query->execute();
    $user_result = $user_query->get_result();

    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $email = $user_data['email'];  // Retrieve the email from the login table
    } else {
        die("User email not found.");
    }

    $user_query->close();
    $user_conn->close();


    $stmt = $conn_user->prepare("
        SELECT v.date, v.start_time, v.end_time, t.name AS therapist_name
        FROM appointments a
        INNER JOIN availability v ON a.availability_id = v.id
        INNER JOIN therapists t ON a.therapist_id = t.id
        WHERE a.email = ? 
          AND a.status = 'confirmed' 
          AND v.date >= CURDATE()
        ORDER BY v.date ASC, v.start_time ASC
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);  // Bind the email parameter
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $row = $result->fetch_assoc()) {
        $upcomingAppointment = $row;
    }
    $stmt->close();
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

        
        .login-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 20px;
            background-color: #333;
            color: white;
            font-size: 14px;
            font-weight: 500;
            position: relative;
            z-index: 10000;
        }

        .login-left, .login-right {
            display: inline-block;
        }

        .login-left a, .login-right a {
            color: #f0db4f;
            text-decoration: underline;
            margin-left: 6px;
            font-weight: 600;
        }

        .login-left a:hover, .login-right a:hover {
            color: #ffffff;
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

        .reminder-box {
            margin: 1rem auto 0 auto;
            max-width: 400px;
            background: #d4edda;
            color: #155724;
            padding: 20px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            line-height: 1.6;       
            text-align: center;     
            letter-spacing: 0.02em; 
            box-shadow: 0 2px 6px rgba(21, 87, 36, 0.2); 
        }

        .reminder-box br {
            content: "";
            margin-bottom: 8px;     
            display: block;
        }


        .no-appointment-msg {
            margin: 1rem auto 0 auto;
            max-width: 400px;
            color: #555;
            font-size: 14px;
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
    </style>
</head>
<body>

<?php if (!isset($_SESSION['userid'])): ?>

<div class="login-bar">
    <div class="login-left">
        Admin? <a href="admin/credentials/adminlogin.php">Login here</a>
    </div>
    <div class="login-right">
        Counsellor? <a href="counsellor/credentials/counsellor_login.php">Login here</a>
    </div>
</div>
<?php endif; ?>


<div id="splash">
    <img src="<?= $logo_picture['filepath']; ?>" alt="Logo">
    <h1>UNITEN Carespace</h1>
    <p>Your mind matters most.</p>
</div>

<div class="page-content">
    <nav>
        <div class="brand">
            <img src="<?= $logo_picture['filepath']; ?>" alt="Logo">
            UNITEN CARESPACE
        </div>
        <div class="nav-right">
            <a href="mainpage.php">Home</a>
            <a href="student/articles/view_articles.php">Articles</a>
            <a href="student/dass/dass_intro.php">DASS Test</a>
            <div class="dropdown">
                <button class="dropbtn">Counsellor ▾</button>
                <div class="dropdown-content">
                    <a href="student/book/therapist_page.php">View Counsellors</a>
                    <a href="student/book/book_appointment.php">Book Appointment</a>
                </div>
            </div>
            <?php if (isset($_SESSION['userid'])): ?>
                <a href="student/profile/userinformation.php">Profile</a>
                <a href="student/credentials/logout.php" id="logout-link">Logout</a>
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
                <a href="student/credentials/userlogin.php">Login</a>
            <?php endif; ?>
        </div>
    </nav>

    <div class="hero">
        <h1>Your mind matters.</h1>
        <p class="desc">Confidential & compassionate support starts here.</p>
        <p class="desc">UNITEN Carespace is a safe platform to help students with mental health needs.<br> Whether you're facing academic stress, anxiety, or need someone to talk to. <br> We're here to help.</p>
        <?php if (isset($_SESSION['userid'])): ?>
            <a href="student/book/book_appointment.php" class="button">Book a Session</a><br><br><br><br>

            <?php if ($upcomingAppointment): ?>
                <div class="reminder-box">
                    Reminder: You have an upcoming appointment with <?= htmlspecialchars($upcomingAppointment['therapist_name']) ?><br>
                    Date: <?= date('d M Y', strtotime($upcomingAppointment['date'])) ?> | Time: <?= date('g:i A', strtotime($upcomingAppointment['start_time'])) ?> - <?= date('g:i A', strtotime($upcomingAppointment['end_time'])) ?>
                    Location: Building TA, Level 3
                </div>
            <?php else: ?>
                <div class="no-appointment-msg">
                    You currently have no upcoming appointments.
                </div>
            <?php endif; ?>

        <?php else: ?>
            <a href="student/credentials/userlogin.php?redirect=book/book_appointment.php" class="button">Book a Session</a>
        <?php endif; ?>
    </div>

    <div class="features">
        <a href="student/book/therapist_page.php" style="text-decoration: none; color: inherit;">
            <div class="feature-card">
                <img src="<?= $counsellor_picture['filepath']; ?>" alt="Counseling">
                <h3>Our Counsellors</h3>
                <p>Talk to trained therapists from your campus in a confidential setting.</p>
            </div>
        </a>
        <a href="student/articles/view_articles.php" style="text-decoration: none; color: inherit;">
            <div class="feature-card">
                <img src="<?= $articles_picture['filepath']; ?>" alt="Articles">
                <h3>Mental Health Articles</h3>
                <p>Explore useful tips and stories to manage stress, anxiety, and more.</p>
            </div>
        </a>
        <a href="student/dass/dass_intro.php" style="text-decoration: none; color: inherit;">
            <div class="feature-card">
                <img src="<?= $dass_picture['filepath']; ?>" alt="DASS">
                <h3>DASS test</h3>
                <p>Take a DASS-21 test to know your current mental health state.</p>
            </div>
        </a>
    </div>
    <div class="about-section">
    <div class="about-container">
        <h2>About UNITEN Student Guidance Unit (SGU)</h2>
        <p>
            The Student Guidance Unit (SGU) at UNITEN provides professional counseling and advisory services to support students' academic and personal well-being. They offer confidential support to help students face challenges and develop resilience.
        </p>
        <p>
            SGU runs workshops, individual counseling, and peer support programs to promote a healthy and balanced student life.
        </p>

        <div class="about-info" style="display: flex; flex-wrap: wrap; gap: 2rem; margin-top: 2rem;">
            <div style="flex: 1; min-width: 220px;">
                <p><strong>Email</strong></p>
                <p>
                  <a href="mailto:student.guidance@uniten.edu.my" style="text-decoration:none; color:#3b5998;">
                    SGU
                  </a>
                </p>
            </div>
            <div style="flex: 1; min-width: 220px;">
                <p><strong>Address</strong></p>
                <p>
                    Universiti Tenaga Nasional (UNITEN)<br />
                    Jalan IKRAM-UNITEN, 43000 Kajang,<br />
                    Selangor, Malaysia
                </p>
            </div>
            <div style="flex: 1; min-width: 220px;">
                <p><strong>Phone</strong></p>
                <p>+603-8921 5126</p>
            </div>
            <div style="flex: 1; min-width: 220px;">
                <p><strong>Social Media</strong></p>
                <p>
                    <a href="https://www.facebook.com/UNITEN.StudentGuidance" target="_blank" 
                       style="text-decoration:none; color:#3b5998; font-size: 1rem;">
                       Facebook
                    </a>
                </p>
                <p>
                    <a href="https://www.instagram.com/sguuniten/" target="_blank" 
                       style="text-decoration:none; color:#3b5998; font-size: 1rem;">
                       Instagram
                    </a>
                </p>
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