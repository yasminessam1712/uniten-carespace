<?php 
session_start(); 
if (!isset($_SESSION['therapist_id'])) {
    header("Location: ../credentials/counsellor_login.php");
    exit();
}
$therapist_id = $_SESSION['therapist_id'];

$conn = mysqli_connect("localhost", "root", "root", "therapist");
if (!$conn) {
    die("Database connection failed");
}

$sql = "SELECT * FROM therapists WHERE id = $therapist_id";
$result = mysqli_query($conn, $sql);
$therapist = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>My Profile - Counsellor</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
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
            gap: 30px;
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
            margin-top: 6px;
            padding: 0;
            line-height: 1;
            height: auto;
            vertical-align: middle;
            display: inline-block;
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

        .profile-container {
            max-width: 700px;
            margin: 20px auto 50px auto;
            background: white;
            padding: 25px 35px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .profile-container h1 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 700;
            color:Black;
        }

        .message {
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 18px;
            font-weight: 600;
            color: #5e5c51;
        }

        .profile-info {
            font-size: 15px;
            padding: 8px 0;
        }

        button {
            margin-top: 22px;
            background-color: #5e5c51;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: block;
            margin-left: auto;
            margin-right: auto;
            text-decoration: none; 
        }

        button:hover {
            background-color: #4b483f;
        }

        .photo-preview {
            display: block;
            margin: 0 auto 18px auto;
            width: 140px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
           
        }
    </style>
</head>
<body>

<nav>
    <div class="brand">
        <img src="../../uploads/logo_official.png" alt="Logo" />
        UNITEN CARESPACE (Counsellor)
    </div>
    <div class="nav-right">
        <a href="../../counsellor_page.php">Dashboard</a>
        <div class="dropdown">
            <button class="dropbtn">Appointments ▾</button>
            <div class="dropdown-content">
                <a href="../appointments/view_appointments.php">View Appointments</a>
                <a href="../appointments/therapist_availability.php">Set Availability</a>
            </div>
        </div>
        <a href="counsellor_profile.php">Profile</a>
        <a href="../credentials/logout.php" id="logout-link">Logout</a>
    </div>
</nav>

<div class="profile-container">
    <h1>My Profile</h1>

    <?php if (!empty($message)): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (!empty($therapist)): ?>
        <?php if (!empty($therapist['photo']) && file_exists('../uploads/' . $therapist['photo'])): ?>
            <img src="../uploads/<?= htmlspecialchars($therapist['photo']) ?>" alt="Profile Photo" class="photo-preview" />
        <?php else: ?>
            <img src="../../uploads/empty_profile_pic.png" alt="Profile Photo" class="photo-preview" />
        <?php endif; ?>

        <div class="profile-info">
            <strong>Name: <br></strong> <?= htmlspecialchars($therapist['name']) ?><br>
            <strong><br>Email: <br></strong> <?= htmlspecialchars($therapist['email']) ?><br>
            <strong><br>Specialty: <br></strong> <?= htmlspecialchars($therapist['specialty']) ?><br>
            <strong><br>Bio: <br></strong> <div style="font-family: 'Poppins', sans-serif; line-height: 1.8;  text-align: justify;"><?= nl2br(htmlspecialchars($therapist['bio'])) ?></div>
        </div>

        <a href="counsellor_profile_edit.php">
            <button>Edit Profile</button>
        </a>
    <?php else: ?>
        <p>Profile not found.</p>
    <?php endif; ?>
</div>

</body>
</html>

