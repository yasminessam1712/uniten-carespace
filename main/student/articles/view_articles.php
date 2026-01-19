<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "root";
$database_admin = "admin";

$admin_conn = new mysqli($host, $username, $password, $database_admin);
if ($admin_conn->connect_error) {
    die("Connection to admin DB failed: " . $admin_conn->connect_error);
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if (!empty($search)) {
    $sql = "SELECT id, topic, article, date, image_path FROM content WHERE topic LIKE ? ORDER BY date DESC";
    $stmt = $admin_conn->prepare($sql);
    $likeSearch = "%$search%";
    $stmt->bind_param("s", $likeSearch);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT id, topic, article, date, image_path FROM content ORDER BY date DESC";
    $result = $admin_conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mental Health Articles</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f0f1ec;
            color: #2a2a2a;
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

        .main {
            max-width: 1150px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .header h3 {
            text-align: center;
            font-size: 26px;
            font-weight: 600;
            margin-bottom: 30px;
        }

        .search-bar {
            text-align: center;
            margin-bottom: 30px;
        }

        .search-bar input[type="text"] {
            padding: 12px 18px;
            width: 60%;
            max-width: 500px;
            border: 1.5px solid #ccc;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
        }

        .search-bar button {
            padding: 12px 20px;
            background-color: #5e5c4f;
            color: white;
            border: none;
            border-radius: 10px;
            margin-left: 10px;
            font-weight: 600;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #4a483c;
        }

        .article-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 24px;
        }

        .article-card {
            background-color: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding-bottom: 16px;
            min-height: 380px;
        }

        .article-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .article-card h3 {
            font-size: 16px;
            margin: 16px 16px 6px;
            font-weight: 600;
            color: #2a2a2a;
        }

        .article-card p {
            font-size: 13px;
            color: #555;
            margin: 0 16px 12px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .view-full-button {
            background-color: #5e5c4f;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            margin-top: 6px;
            margin-bottom: 6px;
        }

        .view-full-button:hover {
            background-color: #49483a;
        }

        .article-date {
            font-size: 12px;
            color: #888;
            margin-top: 4px;
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
        <a href="view_articles.php">Articles</a>
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
  e.preventDefault(); /
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
    <div class="header">
        <h3>Mental Health Articles</h3>
    </div>

    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search articles..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div class="article-container">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="article-card">
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="../../admin/articles/uploads/<?= htmlspecialchars(basename($row['image_path'])) ?>" alt="Article Image">
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($row['topic']) ?></h3>
                    <p><?= htmlspecialchars(substr($row['article'], 0, 150)) ?>...</p>
                    <a href="view_article.php?id=<?= $row['id'] ?>" class="view-full-button">View Full Article</a>
                    <div class="article-date">Published on: <?= $row['date'] ?></div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align:center; color: #777;">No articles found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
