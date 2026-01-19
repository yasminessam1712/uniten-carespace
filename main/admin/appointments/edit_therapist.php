<?php
session_start();

$conn = mysqli_connect("localhost", "root", "root", "therapist");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['admin'])) {
    header("Location: admin/credentials/adminlogin.php");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Invalid therapist ID.";
    exit;
}
$therapist_id = (int)$_GET['id'];

$query = "SELECT * FROM therapists WHERE id = $therapist_id";
$result = mysqli_query($conn, $query);
$therapist = mysqli_fetch_assoc($result);

if (!$therapist) {
    echo "Therapist not found!";
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $specialty = mysqli_real_escape_string($conn, $_POST['specialty']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $password = $_POST['password'];

    $photo = $therapist['photo'];

    if (!empty($_FILES['photo']['name'])) {
        $photo_filename = time() . '_' . basename($_FILES['photo']['name']);
        $upload_dir = __DIR__ . '/../../counsellor/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $target_file = $upload_dir . $photo_filename;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $photo = $photo_filename;
        } else {
            $message = "Photo upload failed.";
            $messageType = 'error';
        }
    }

    if (empty($message)) {
        $update_query = "UPDATE therapists SET  
            email = '$email', 
            specialty = '$specialty', 
            bio = '$bio', 
            photo = '$photo'";

        if (!empty($password)) {
            $update_query .= ", password = '" . mysqli_real_escape_string($conn, $password) . "'";
        }

        $update_query .= " WHERE id = $therapist_id";

        if (mysqli_query($conn, $update_query)) {
            $message = "Therapist updated successfully!";
            $messageType = 'success';
            $result = mysqli_query($conn, $query);
            $therapist = mysqli_fetch_assoc($result);
        } else {
            $message = "Error updating therapist: " . mysqli_error($conn);
            $messageType = 'error';
        }
    }
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Therapist</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f1ec;
            margin: 0;
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
        .container {
            width: 50%;
            margin: 40px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        input[type="text"], input[type="email"], textarea, input[type="file"], input[type="password"] {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
            font-size: 14px;
            box-sizing: border-box;
        }
        .button-group {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-top: 20px;
        }
        button, .back-button {
            width: 140px;
            padding: 8px 0;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            border-radius: 4px;
            cursor: pointer;
            border: none;
            color: white;
            background-color: #5e5c51;
            transition: background-color 0.3s ease;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            user-select: none;
        }
       
        .back-button {
            text-decoration: none;
        }
        .success {
            color: green;
            font-size: 16px;
            text-align: center;
        }
        .error {
            color: red;
            font-size: 16px;
            text-align: center;
        }
                .current-photo {
            max-width: 150px;
            max-height: 150px;
            border-radius: 8px;
            margin-bottom: 10px;
            display: block;
        }
        .current-photo-label {
            margin-top: 0;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 14px;
        }
    </style>
</head>
<body>

<nav>
    <div class="brand">
        <img src="../../uploads/logo_official.png" alt="Logo" />
        UNITEN CARESPACE (Admin)
    </div>
    <div class="nav-right">
        <a href="../../admin_page.php">Dashboard</a>
        <a href="view_therapist.php">Counsellors</a>
        <div class="dropdown">
            <button class="dropbtn" style="margin-left: -30px;">Articles ▾</button>
            <div class="dropdown-content">
                <a href="../articles/publish_article.php">Publish Articles</a>
                <a href="../articles/manage_articles.php">Manage Articles</a>
            </div>
        </div>
        <div class="dropdown"> 
            <button class="dropbtn"style="margin-left: -40px;">Appointments ▾</button>
            <div class="dropdown-content">
                <a href="view_appointments.php">View Appointments</a>
                <a href="sumary_therapist.php">Summary</a>
                <a href="review_cancellations.php">Cancellations</a>
                
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

<div class="container">
    <h2>Edit Counsellor Details</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Name</label>
                <div style="padding: 8px 0; font-weight: 600;"><?= htmlspecialchars($therapist['name'])  ?></div>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($therapist['email']) ?>" required />

        <label for="specialty">Specialty:</label>
        <input type="text" id="specialty" name="specialty" value="<?= htmlspecialchars($therapist['specialty']) ?>" required />

        <label for="bio">Bio:</label>
        <textarea id="bio" name="bio" rows="6" style="font-family: 'Poppins', sans-serif;"><?= htmlspecialchars($therapist['bio']) ?></textarea>

        <label for="current_password">Current Password:</label>
<div style="position: relative;">
    <input type="password" id="current_password" name="current_password" value="<?= htmlspecialchars($therapist['password']) ?>" readonly>
    <button type="button" onclick="togglePassword()" style="position: absolute; right: 10px; top: 10px; background: none; border: none; cursor: pointer;">👁️</button>
</div>

<label for="password">New Password:</label>
<input type="password" id="password" name="password" placeholder="Leave empty to keep current password" />


        <label for="photo">Update Photo (optional):</label>
        <input type="file" id="photo" name="photo" />

        <?php if (!empty($therapist['photo'])): ?>
            <p class="current-photo-label">Current Photo:</p>
            <img
                src="../../counsellor/uploads/<?= htmlspecialchars($therapist['photo']) ?>"
                alt="Current Therapist Photo"
                class="current-photo"
            />
        <?php else: ?>
            <p>No photo uploaded yet.</p>
        <?php endif; ?>

        <div class="button-group">
            <button type="submit">Save Changes</button>
            <a href="view_therapist.php" class="back-button">Back</a>
        </div>
    </form>
</div>

<?php if (!empty($message) && !empty($messageType)): ?>
<script>
    Swal.fire({
        icon: <?= json_encode($messageType) ?>,
        title: <?= json_encode($messageType === 'success' ? 'Success' : 'Error') ?>,
        text: <?= json_encode($message) ?>,
        confirmButtonColor: '#5e5c51',
        timer: 3000,
        timerProgressBar: true,
    });
</script>
<?php endif; ?>

<script>
function togglePassword() {
    const pwd = document.getElementById('current_password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
}
</script>
<script>
function togglePassword() {
    const pwd = document.getElementById('current_password');
    pwd.type = pwd.type === 'password' ? 'text' : 'password';
}

document.querySelector('form').addEventListener('submit', function(event) {
    const newPassword = document.getElementById('password').value;
    if (newPassword) {
        const strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[!@#$%^&*]).{8,}$/;
        if (!strongRegex.test(newPassword)) {
            event.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Weak Password',
                text: 'Password must include uppercase, lowercase, digit, special char (!@#$%^&*), and be at least 8 characters long.'
            });
        }
    }
});
</script>


</body>
</html>