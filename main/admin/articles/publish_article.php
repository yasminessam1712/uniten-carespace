<?php 
session_start(); 
include_once("../../config.php");

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$conn_admin = new mysqli("localhost", "root", "root", "admin");
if ($conn_admin->connect_error) {
    die("Connection failed: " . $conn_admin->connect_error);
}

$success_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic = trim($_POST['topic']);
    $article = trim($_POST['article']);
    $link = trim($_POST['link']);
    $date = date("Y-m-d");
    $image_path = "";

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $image_name = time() . "_" . basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image_name;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $image_path = $target_file;
        } else {
            $success_message = "<p style='color: red;'>Image upload failed.</p>";
        }
    }

    if ($topic === "" || $article === "") {
        $success_message = "<p style='color: red;'>Topic and Article cannot be empty!</p>";
    } else {
        $stmt = $conn_admin->prepare("INSERT INTO content (topic, article, date, image_path, link) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $topic, $article, $date, $image_path, $link);

        if ($stmt->execute()) {
            $_SESSION['publish_success'] = true; // Flag success in session
        } else {
            $_SESSION['publish_success'] = false; // Flag failure in session
        }

        $stmt->close();
    }
}
$conn_admin->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Publish Article - UNITEN Carespace</title>
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
        max-width: 1500px; 
        background: white;
        margin: 3rem auto;
        padding: 2rem 3rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .main-container h3 {
        text-align: left;
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 2rem;
    }

    .description {
        font-size: 1.1rem;
        color: #555;
        margin-top: 10px;
        line-height: 1.5;
        text-align: justify;
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

    .success-message {
        text-align: center;
        font-size: 1.1rem;
        margin-bottom: 1.2rem;
    }

    .table-container {
        width: 100%;
        max-width: 800px;
        margin: 0 auto;
        background: #fff;
        padding: 1.5rem;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
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

<?php if (isset($_SESSION['publish_success']) && $_SESSION['publish_success'] === true): ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Article Published',
            text: 'The article has been successfully published!',
            confirmButtonColor: '#5e5c51'
        }).then(() => {
            window.location.href = 'manage_articles.php';  // Redirect after closing the alert
        });
    </script>
    <?php unset($_SESSION['publish_success']); ?>  <!-- Clear the session variable after showing the alert -->
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
    <h3>Publish Mental Health Article</h3>
    <p class="description">
    This section allows you to publish articles related to mental health. Whether you're sharing insights, personal experiences, research findings, or helpful resources, your articles will help raise awareness and provide valuable information. Please make sure to include well-researched content and provide a meaningful perspective that can benefit others.
    </p>

    <?php if (!empty($success_message)): ?>
        <div class="success-message"> <?= $success_message ?> </div>
        <a href="manage_articles.php"><button class="btn">Return to Dashboard</button></a>
    <?php else: ?>
        <form method="POST" enctype="multipart/form-data">
            <label>Topic</label>
            <input type="text" name="topic" placeholder="" required>

            <label>Article</label>
            <textarea name="article" placeholder="" id="article" required></textarea>

            <div class="char-count" id="char-count">Character count: 0/10000</div>
            <div class="char-count-warning" id="char-count-warning" style="display: none;">You have exceeded the 10,000-character limit!</div>

            <label>Optional Link</label>
            <input type="text" name="link" placeholder="https://example.com">

            <label>Upload Image (Optional)</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit" class="btn">Publish Article</button>
        </form>
    <?php endif; ?>
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

</body>
</html>