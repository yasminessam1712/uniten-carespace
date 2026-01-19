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

$conn_pics = mysqli_connect("localhost", "root", "root", "pictures");
if (!$conn_pics) {
    die("Pictures DB connection failed");
}

$message = "";
$message_color = "green"; 

$sql = "SELECT * FROM therapists WHERE id = $therapist_id";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $therapist = mysqli_fetch_assoc($result);
} else {
    $message = "Therapist profile not found.";
    $message_color = "red";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $specialty = $_POST['specialty'] ?? '';
    $bio = $_POST['bio'] ?? '';
    
    $changes_made = false;

    if ($specialty !== $therapist['specialty'] || $bio !== $therapist['bio']) {
        $changes_made = true;
    }

    $photo_sql = "";
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {

        $uploadDir = '../uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $originalFilename = basename($_FILES['photo']['name']);
        $fileExt = pathinfo($originalFilename, PATHINFO_EXTENSION);
        $newFilename = uniqid('therapist_', true) . '.' . $fileExt;
        $targetFilePath = $uploadDir . $newFilename;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFilePath)) {
            $relativePath = '../uploads/' . $newFilename;
            $photo_sql = ", photo = '" . mysqli_real_escape_string($conn, $relativePath) . "'";

            $changes_made = true;
        } else {
            $_SESSION['message'] = "Failed to upload photo. Check folder permissions.";
            $_SESSION['message_color'] = "red";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

if ($changes_made) {

    $sql = "UPDATE therapists SET 
                specialty = '" . mysqli_real_escape_string($conn, $specialty) . "',
                bio = '" . mysqli_real_escape_string($conn, $bio) . "'"
                . $photo_sql . "
            WHERE id = $therapist_id";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['message'] = "Profile updated successfully!";
        $_SESSION['message_class'] = "success"; 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } 
    else {
        $_SESSION['message'] = "Error updating profile. Please try again.";
        $_SESSION['message_class'] = "error"; 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
} 
    else {
        $_SESSION['message'] = "Nothing changed! No updates made.";
        $_SESSION['message_class'] = "no-changes"; 
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
        }
     }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {

    $old_password = $_POST['old_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!empty($old_password) || !empty($new_password) || !empty($confirm_password)) {
        if (empty($new_password) || empty($confirm_password) || empty($old_password)) {
            $_SESSION['message'] = "No changes made. Please enter a new password to update.";
            $_SESSION['message_class'] = "no-changes";
        } elseif ($old_password !== $therapist['password']) {
            $_SESSION['message'] = "Old password is incorrect. Please enter the correct old password.";
            $_SESSION['message_class'] = "error";
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['message'] = "New password and confirm password do not match.";
            $_SESSION['message_class'] = "error";
        } else {
            $password_sql = ", password = '" . mysqli_real_escape_string($conn, $new_password) . "'";

            $sql = "UPDATE therapists SET password = '" . mysqli_real_escape_string($conn, $new_password) . "' WHERE id = $therapist_id";
            if (mysqli_query($conn, $sql)) {
                $_SESSION['message'] = "Password updated successfully!";
                $_SESSION['message_class'] = "success";
            } else {
                $_SESSION['message'] = "Error updating password. Please try again.";
                $_SESSION['message_class'] = "error";
            }
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
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
            color:black;
        }

        .message {
            color: black; 
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
        }

        .message.success {
            background-color: #d4edda; 
            border: 1px solid #c3e6cb; 
        }


        .message.no-changes {
            background-color: #fff3cd; 
            border: 1px solid #ffeeba;
        }


        .message.error {
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
        }


        label {
            display: block;
            margin-top: 12px;
            font-weight: 600;
            color: #5e5c51;
        }

        input[type="text"],
        input[type="email"],
        textarea,
        input[type="file"],
        button {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
        }

        input:disabled,
        textarea:disabled {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            color: #444;
            cursor: not-allowed;
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

        textarea {
            font-family: 'Poppins', sans-serif;
            line-height: 1.8;
            font-size: 16px;
            padding: 12px;
            min-height: 250px;
        }
        input[type="text"] {
            font-family: 'Poppins', sans-serif;
            line-height: 1.8; 
            font-size: 16px;
            padding: 10px; 
            min-height: 0px; 
            width: 150%; 
            max-width: 700px; 
            resize: none; 
        }
        input[type="password"] {
            font-family: 'Poppins', sans-serif;
            line-height: 1.8; 
            font-size: 16px;
            padding: 3px; 
            min-height: 0px; 
            width: 150%; 
            max-width: 700px; 
            resize: none; 
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
<div class="profile-right">
<h1>My Profile</h1>
    <div class="profile-left">
        <div class="profile-photo">
            <?php if (!empty($therapist['photo'])): ?>
                <img src="<?= htmlspecialchars($therapist['photo']); ?>" alt="Profile Photo" class="photo-preview" width="150" height="150" />
            <?php else: ?>
                <img src="../../uploads/empty_profile_pic.png" alt="Default Profile Photo" class="photo-preview" width="150" height="150" />
            <?php endif; ?>
        </div>
    </div>

        <?php

        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $message_class = $_SESSION['message_class']; 

            unset($_SESSION['message']);
            unset($_SESSION['message_class']);
        }
        ?>

        <?php if (!empty($message)): ?>
            <p class="message <?= $message_class; ?>"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" autocomplete="off">
        <label for="nameInput">Name</label>
            <p><?= htmlspecialchars($therapist['name']) ?></p> 

            <label for="emailInput">Email</label>
            <p><?= htmlspecialchars($therapist['email']) ?></p> 
            <label for="specialtyInput">Specialty</label>
            <input type="text" id="specialtyInput" name="specialty" placeholder="Please Fill in this Field"value="<?= htmlspecialchars($therapist['specialty']) ?>" />

            <label for="bioInput">Bio</label>
            <textarea id="bioInput" name="bio" rows="4"placeholder="Please Fill in this Field"><?= htmlspecialchars($therapist['bio']) ?></textarea>

            <label for="photo">Profile Photo</label>
            <input type="file" id="photo" name="photo" accept="image/*" />
            <button type="submit" name="update_profile">Update Profile</button>
        </form>

        <form method="POST" autocomplete="off">
            <h3>Change Password</h3>
            <label for="oldPassword">Old Password</label>
            <input type="password" id="oldPassword" name="old_password" />

            <label for="newPassword">New Password</label>
            <input type="password" id="newPassword" name="new_password" />

            <label for="confirmPassword">Confirm New Password</label>
            <input type="password" id="confirmPassword" name="confirm_password" />

            <button type="submit" name="change_password">Update Password</button>
           
        </form>
         <a href="counsellor_profile.php">
            <button>Return to Profile</button>
    </div>
</div>

</body>
</html>
