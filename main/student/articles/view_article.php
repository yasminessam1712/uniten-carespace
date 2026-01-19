<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$username = "root";
$password = "root";
$database_admin = "admin";

$conn = new mysqli($host, $username, $password, $database_admin);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$article_id = isset($_GET['id']) && is_numeric($_GET['id']) ? $_GET['id'] : 0;
if ($article_id == 0) {
    echo "Invalid article ID.";
    exit;
}

$sql = "SELECT topic, article, date, image_path, link FROM content WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $article_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $article = $result->fetch_assoc();
} else {
    echo "<div class='no-result'>No articles found.</div>";

    exit;
}

$related_sql = "SELECT id, topic FROM content WHERE id != ? ORDER BY date DESC LIMIT 3";
$related_stmt = $conn->prepare($related_sql);
$related_stmt->bind_param("i", $article_id);
$related_stmt->execute();
$related_result = $related_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($article['topic']) ?> - Article</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Poppins', sans-serif; }

        body { background: #f0f1ec;; color: #2a2a2a; }

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
            padding: 40px;
            max-width: 900px;
            margin: auto;
        }

        .article-box {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .article-box img {
            width: 100%;
            max-height: 300px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .article-box h3 {
            font-size: 26px;
            margin-bottom: 20px;
        }

        .article-box p {
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .article-date {
            font-size: 13px;
            color: #888;
            text-align: right;
        }

        .external-link {
            color: #2a5d4f;
            font-weight: 600;
            text-decoration: none;
            display: block;
            margin-top: 20px;
        }

        .external-link:hover {
            text-decoration: underline;
        }

        .related-articles {
            margin-top: 40px;
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }

        .related-articles h2 {
            margin-bottom: 15px;
            font-size: 18px;
        }

        .related-articles ul {
            list-style-type: none;
            padding: 0;
        }

        .related-articles li {
            margin-bottom: 10px;
        }

        .related-articles a {
            color: #2a5d4f;
            text-decoration: none;
            font-weight: 500;
        }

        .related-articles a:hover {
            text-decoration: underline;
        }
        .center-message {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            font-size: 20px;
            font-weight: 600;
            color: #333;    
        }
        .no-result {
            display: flex;
                justify-content: center;
                align-items: center;
                height: 50vh;                 font-size: 18px;
                color: #555;
                font-weight: 500;
                text-align: center;
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
    <div class="article-box">
        <?php if (!empty($article['image_path'])): ?>
            <img src="../../admin/articles/uploads/<?= htmlspecialchars(basename($article['image_path'])) ?>" alt="Article Image">
        <?php endif; ?>
        <h3><?= htmlspecialchars($article['topic']) ?></h3>
        <p><?= nl2br(htmlspecialchars($article['article'])) ?></p>
        <div class="article-date">Published on: <?= htmlspecialchars($article['date']) ?></div>
        <?php if (!empty($article['link'])): ?>
            <a href="<?= htmlspecialchars($article['link']) ?>" class="external-link" target="_blank">Visit External Link</a>
        <?php endif; ?>
    </div>

    <div class="related-articles">
        <h2>Related Articles</h2>
        <ul>
            <?php if ($related_result && $related_result->num_rows > 0): ?>
                <?php while ($related_article = $related_result->fetch_assoc()): ?>
                    <li><a href="view_article.php?id=<?= $related_article['id'] ?>"><?= htmlspecialchars($related_article['topic']) ?></a></li>
                <?php endwhile; ?>
            <?php else: ?>
                <li>No related articles found.</li>
            <?php endif; ?>
        </ul>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
