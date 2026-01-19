<?php 
session_start(); 

if (!isset($_SESSION['admin'])) {
    header("Location: adminlogin.php");
    exit();
}

$conn_admin = new mysqli("localhost", "root", "root", "admin");
if ($conn_admin->connect_error) {
    die("Connection failed: " . $conn_admin->connect_error);
}

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn_admin->query("DELETE FROM content WHERE id = $delete_id");
    header("Location: manage_articles.php");
    exit();
}

$result = $conn_admin->query("SELECT * FROM content ORDER BY date DESC");

$search_query = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search_query = trim($_GET['search']);
    $stmt = $conn_admin->prepare("SELECT * FROM content WHERE topic LIKE ? ORDER BY date DESC");
    $like = "%$search_query%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn_admin->query("SELECT * FROM content ORDER BY date DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Articles - UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            background-color: #f0f1ec;
            color: #333;
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

        .main-container {
            max-width: 1200px;
            margin: 3rem auto;
            padding: 2rem 3rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .main-container h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        .articles {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }

        .article-box {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 100%;
            max-width: 320px;
            min-height: 320px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
        }

        .article-box h3 {
            font-size: 18px;
            margin-bottom: 8px;
            color: #222;
        }

        .article-box .date {
            font-size: 12px;
            color: #888;
            margin-bottom: 10px;
        }

        .article-box p {
            font-size: 13px;
            color: #555;
            overflow: hidden;
            text-overflow: ellipsis;
            height: 60px;
        }

        .article-box img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .article-box a {
            display: inline-block;
            margin-top: auto;
            padding: 6px 12px;
            font-size: 12px;
            background-color: #5e5c51;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin-right: 6px;
        }

        .article-box a:hover {
            background-color: #4e4d44;
        }

        .search-bar {
            text-align: center;
            margin-bottom: 2rem;
        }

        .search-bar input[type="text"] {
            width: 300px;
            padding: 10px 14px;
            font-size: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-right: 10px;
        }

        .search-bar button {
            padding: 10px 16px;
            font-size: 15px;
            font-weight: 600;
            background-color: #5e5c51;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #4e4d44;
        }
    </style>
</head>
<body>
<?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Article Updated',
    text: 'The article has been successfully updated!',
    confirmButtonColor: '#5e5c51'
});
</script>
<?php endif; ?>


<nav>
    <div class="brand">
        <img src="../../uploads/logo_official.png" alt="Logo">
        UNITEN CARESPACE (Admin)
    </div>
    <div class="nav-right">
        <a href="../../admin_page.php">Dashboard</a>
        <a href="../appointments/view_therapist.php">Counsellors</a>
        <div class="dropdown">
            <button class="dropbtn">Articles ▾</button>
            <div class="dropdown-content">
                <a href="publish_article.php">Publish Articles</a>
                <a href="manage_articles.php">Manage Articles</a>
            </div>
        </div>
        <div class="dropdown">
            <button class="dropbtn">Appointments ▾</button>
            <div class="dropdown-content">
                <a href="../appointments/view_appointments.php">View Appointments</a>
                <a href="../appointments/sumary_therapist.php">Summary</a>
                <a href="../appointments/review_cancellations.php">Cancellations</a>
                
            </div>
        </div>
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

    </div>
</nav>

<div class="main-container">
    <h2>Manage Published Articles</h2>

    <div class="search-bar">
    <form method="GET" style="display: inline;">
        <input type="text" name="search" placeholder="Search by topic..." value="<?= htmlspecialchars($search_query) ?>">
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($search_query)): ?>
        <form method="GET" style="display: inline;">
            <button type="submit" style="background: #bbb; color: #222; margin-left: 10px;">Clear</button>
        </form>
    <?php endif; ?>
</div>

<div class="articles">
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="article-box">
            <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Article Image">

            <h3><?= htmlspecialchars($row['topic']) ?></h3>
            <div class="date">Published on: <?= htmlspecialchars($row['date']) ?></div>
            <p><?= htmlspecialchars(substr($row['article'], 0, 100)) ?>...</p>
            <div>
                <a href="edit_article.php?id=<?= $row['id'] ?>">Edit</a>
                <a href="javascript:void(0);" class="delete-link" data-id="<?= $row['id'] ?>">Delete</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Add event listener to all delete links
    document.querySelectorAll('.delete-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default action
            const articleId = this.getAttribute('data-id'); // Get article ID from the data-id attribute

            // Use SweetAlert to confirm deletion
            Swal.fire({
                title: 'Are you sure?',
                text: 'You will not be able to recover this article!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5e5c51',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, redirect to delete the article
                    window.location.href = 'manage_articles.php?delete_id=' + articleId;
                }
            });
        });
    });
</script>


</body>
</html>
