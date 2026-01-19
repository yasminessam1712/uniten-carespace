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

$article = ['topic' => '', 'article' => '', 'link' => '', 'image_path' => '', 'id' => ''];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM content WHERE id = $id");
    if ($result && $result->num_rows > 0) {
        $article = $result->fetch_assoc();
    } else {
        die("Article not found.");
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = intval($_POST['id']);
    $topic = trim($_POST['topic']);
    $content = trim($_POST['article']);
    $link = trim($_POST['link']);
    $image_path = $_POST['existing_image'];

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("UPDATE content SET topic = ?, article = ?, link = ?, image_path = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $topic, $content, $link, $image_path, $id);
    $stmt->execute();

    header("Location: edit_article.php?id=" . $id . "&updated=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Article - UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            max-width: 900px;
            background: white;
            margin: 3rem auto;
            padding: 2rem 3rem;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .main-container h2 {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 2rem;
        }

        label {
            font-weight: 600;
            display: block;
            margin-top: 1.3rem;
        }

        input[type="text"],
        textarea {
            width: 100%;
            padding: 14px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif; 
            
        }

        textarea {
            resize: vertical;
            min-height: 1000px; 
        }

        input[type="file"] {
            margin-top: 10px;
        }


        .btn {
            margin-top: 2rem;
            padding: 14px 30px; 
            background: #5e5c51;
            color: white;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            cursor: pointer;
            display: block; 
            margin-left: auto; 
            margin-right: auto; 
        }


        .btn:hover {
            background: #4e4d44;
        }

        .current-image {
            margin-top: 1rem;
        }

        .current-image img {
            max-width: 100%;
            border-radius: 8px;
            margin-top: 10px;
        }

        .back-link {
            display: block;
            margin-top: 1.5rem;
            text-align: center;
            text-decoration: none;
            color: white; /* Change the text color */
            background-color: #5e5c51; /* Set the background color */
            padding: 12px 24px; /* Add padding to make it look like a button */
            border-radius: 8px; /* Rounded corners for the button */
            font-weight: 600;
            width: fit-content;
            margin-left: auto;
            margin-right: auto;
            transition: background-color 0.3s ease;
        }

        .back-link:hover {
            background-color: #4e4d44; /* Change the background color on hover */
        }


        .char-count {
            font-size: 1rem;
            color: #888;
            margin-top: 5px;
            text-align: right;
        }

        .char-count-warning {
            color: red;
        }
    </style>
</head>
<body>

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
    <h2>Edit Published Article</h2>

    <form method="POST" enctype="multipart/form-data">
        <?php if (!empty($article['image_path'])): ?>
            <div class="current-image">
                <label>Current Image:</label>
                <img src="<?= htmlspecialchars($article['image_path']) ?>" alt="Article Image">
            </div>
        <?php endif; ?>

        <input type="hidden" name="id" value="<?= htmlspecialchars($article['id']) ?>">
        <input type="hidden" name="existing_image" value="<?= htmlspecialchars($article['image_path']) ?>">

        <label>Topic</label>
        <input type="text" name="topic" value="<?= htmlspecialchars($article['topic']) ?>" required>

        <label>Article</label>
        <textarea name="article" id="article" required><?= htmlspecialchars($article['article']) ?></textarea>
        <div class="char-count" id="char-count">Character count: 0/10000</div>
        <div class="char-count-warning" id="char-count-warning" style="display: none;">You have exceeded the 10,000-character limit!</div>

        <label>Link</label>
        <input type="text" name="link" value="<?= htmlspecialchars($article['link']) ?>">

        <label>Upload New Image (Optional)</label>
        <input type="file" name="image" accept="image/*">

        <button type="submit" class="btn" id="update-article-btn">Update Article</button>
        <script>
</script>
    </form>
    <a href="manage_articles.php" class="back-link">Back to Manage Articles</a>
</div>

<script>
document.getElementById('article').addEventListener('input', function() {
    const articleText = this.value.trim();
    const charCount = articleText.length;

    const charCountElement = document.getElementById('char-count');
    charCountElement.textContent = `Character count: ${charCount}/10000`;

    const charCountWarning = document.getElementById('char-count-warning');
    if (charCount > 10000) {
        charCountWarning.style.display = 'block';
    } else {
        charCountWarning.style.display = 'none';
    }
});
</script>
<?php if (isset($_GET['updated'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Article Updated',
                text: 'The article has been successfully updated.',
                confirmButtonText: 'OK'
            });
        </script>
    <?php endif; ?>
   
</body>
</html>
