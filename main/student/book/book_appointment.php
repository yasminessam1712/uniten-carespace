<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['userid'])) {
    header("Location: ../credentials/userlogin.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "therapist");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
$result = mysqli_query($conn, "SELECT * FROM therapists");
if (!$result) {
    die("Query failed: " . mysqli_error($conn));

}

?>
<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Book a Counselling Session</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }

        body {
            background-color: #f0f1ec;
            color: #333;
            min-height: 100vh;
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
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
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
            padding: 40px 20px;
        }

        .book-now-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 14px;
            max-width: 900px;
            margin: auto;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }

        .info-box {
            text-align: center;
            margin-bottom: 2rem;
        }

        .info-box h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .info-box .description {
            font-size: 0.95rem;
            color: #555;
            line-height: 1.6;
        }

        .therapist-container {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            justify-content: center;
            max-width: 1200px; 
            margin: 0 auto; 
        }

         .therapist-card {
            background: #fefefe;     
            border-radius: 16px;
            width: 280px;
            padding: 1.5rem;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .therapist-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 22px rgba(0,0,0,0.1);
        }
        .therapist-card img {
            width: 180px;
            height: 180px;
            object-fit: cover;
            border-radius: 50%;
            margin: 0 auto 1rem auto;
            background-color: #f0f1ec;
            display: block;
        }


        .therapist-card h3 {
            font-size: 1.2rem;
            color: #222;
            margin-bottom: 0.4rem;
        }

        .therapist-card p {
            color: #555;
            font-size: 0.95rem;
            margin-bottom: 1rem;
        }

        .book-now-button {
            background: #5e5c4f;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            font-size: 14px;
            transition: background 0.3s;
            margin-top: auto;
        }

        .book-now-button:hover {
            background: #4a4a3f;
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

    <div class="main">
        <div class="book-now-container">
            <div class="info-box">
                <h1>Book a Counselling Session</h1>
                <p class="description">
                    Get professional support for your academic and personal challenges.<br>
                    Select a therapist and take the first step toward achieving balance and well-being.
                </p>
            </div>
            <div class="therapist-container">
            <?php while ($t = mysqli_fetch_assoc($result)) { 
                $photoPath = "../../counsellor/uploads/" . $t['photo'];?>
                <?php 
                $photoPath = __DIR__ . "/../counsellor/uploads/" . $t['photo'];
                $photoUrl = "/test/App/main/counsellor/uploads/" . $t['photo'];
                ?>

            <div class="therapist-card">
            <img src="../../counsellor/uploads/<?= htmlspecialchars($t['photo']) ?>" 
            onerror="this.onerror=null; this.src='../../uploads/empty_profile_pic.png';"
            alt="Photo" width="50" height="50" style="border-radius: 50%;">

                <h3><?= htmlspecialchars($t['name']) ?></h3>
                <p><?= htmlspecialchars($t['specialty']) ?></p>
                <button class="book-now-button" onclick="window.location.href='book_counsellor.php?id=<?= $t['id'] ?>'">Book Now</button>
            </div>
                <?php } ?>
        </div>
    </div>
</body>
</html> 

<?php $conn->close(); ?>
