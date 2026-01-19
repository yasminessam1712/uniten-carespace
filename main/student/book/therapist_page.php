<?php
session_start();
$connection = new mysqli("localhost", "root", "root", "therapist");
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$sql = "SELECT name, specialty, photo, bio FROM therapists";
$result = $connection->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meet Our Counselors - UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    h1, h3, h4 {
        font-family: 'Poppins', sans-serif;
    }

    body {
        background: #f0f1ec;
        color: #1d1d1f;
        line-height: 1.6;
    }

    .navbar {
        width: 100%;
        background-color: #5e5c51;
        padding: 12px 24px;
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
        font-size: 1.4rem;
        font-weight: 700;
        color: white;
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
        font-weight: bold;
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
        font-weight: bold;
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
        max-width: 1600px;
        margin: 0 auto;
        padding: 3rem 2rem;
    }

    .container h1 {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 1rem;
        text-align: center;
        color: #222;
    }

    .container p {
        font-size: 1.1rem;
        color: #666;
        margin-bottom: 2.5rem;
        text-align: center;
        font-weight: 500;
    }

    .counselor-list {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }

    .counselor-item {
        display: flex;
        gap: 2rem;
        background: #fff;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: 0 10px 18px rgba(0,0,0,0.07);
        align-items: center;
    }

    .counselor-item img {
        width: 220px;
        height: 220px;
        object-fit: cover;
        border-radius: 14px;
        background-color: #f0f1ec;
    }

    .counselor-info {
        text-align: left;
    }

    .counselor-info h3 {
        font-size: 1.7rem;
        margin-bottom: 0.4rem;
        color: #1d1d1f;
    }

    .counselor-info h4 {
        font-size: 1.15rem;
        font-weight: 600;
        color: #666;
        margin-bottom: 1rem;
    }

    .counselor-info p {
        font-size: 1.02rem;
        color: #444;
        line-height: 1.7;
        text-align: justify;
    }

    .book-one-button {
        margin-top: 60px;
        text-align: center;
    }

    .book-one-button a {
        display: inline-block;
        background: #5e5c51;
        color: white;
        padding: 16px 32px;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        border-radius: 10px;
        transition: background-color 0.2s ease;
    }

    .book-one-button a:hover {
        background-color: #4b4a40;
    }
</style>

</head>
<body>

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
                    <a href="therapist_page.php">View Counsellors</a>
                    <a href="book_appointment.php">Book Appointment</a>
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
        <h1>Meet Our Counselors</h1>
        <p>Each counselor is here to support your journey with care and expertise.</p>

        <div class="counselor-list">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="counselor-item">
                        <img src="../../counsellor/uploads/<?= htmlspecialchars($row['photo']) ?>" 
                             onerror="this.onerror=null; this.src='../../uploads/empty_profile_pic.png';" 
                             alt="Photo">
                        <div class="counselor-info">
                            <h3><?= htmlspecialchars($row['name']) ?></h3>
                            <h4><?= htmlspecialchars($row['specialty']) ?></h4>
                            <p><?= htmlspecialchars($row['bio']) ?></p>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No counselors found.</p>
            <?php endif; ?>
        </div>

        <div class="book-one-button">
            <?php if (isset($_SESSION['userid'])): ?>
                <a href="book_appointment.php">Book a Session</a>
            <?php else: ?>
                <a href="../credentials/userlogin.php?redirect=book/book_appointment.php">Book a Session</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php $connection->close(); ?>

