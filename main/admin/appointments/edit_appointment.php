<?php
$conn = mysqli_connect("localhost", "root", "root", "therapist") or die("Connection failed");

$id = $_GET['id'] ?? null;
if (!$id) die("No appointment ID provided.");

$query = "
    SELECT a.*, t.name AS therapist_name, v.date, v.start_time, v.end_time
    FROM appointments a
    JOIN therapists t ON a.therapist_id = t.id
    JOIN availability v ON a.availability_id = v.id
    WHERE a.id = $id
";
$appointment = mysqli_fetch_assoc(mysqli_query($conn, $query));
if (!$appointment) die("Appointment not found.");


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = $_POST['status'];
    $valid = ['confirmed', 'cancelled'];
    if (in_array($new_status, $valid)) {
        $update = mysqli_query($conn, "UPDATE appointments SET status = '$new_status' WHERE id = $id");
        if ($update) {
            echo "<script>alert('✅ Status updated.'); window.location='view_appointments.php';</script>";
            exit();
        } else {
            echo "<script>alert('❌ Failed to update.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment - UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
        .main {
            padding: 40px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 700px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .container p {
            font-size: 15px;
            margin: 8px 0;
        }
        label {
            font-weight: 600;
            margin-right: 10px;
        }
        select {
            padding: 10px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 16px;
        }
        button {
            padding: 10px 18px;
            font-size: 14px;
            background-color: #3498db;
            border: none;
            color: white;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #2980b9;
        }
        .back {
            margin-top: 20px;
        }
        .back a {
            text-decoration: none;
            font-size: 14px;
            color: #555;
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
        <a href="view_therapist.php">Counsellors</a>
        <div class="dropdown">
            <button class="dropbtn">Articles ▾</button>
            <div class="dropdown-content">
                <a href="../articles/publish_article.php">Publish Articles</a>
                <a href="../articles/manage_articles.php">Manage Articles</a>
            </div>
        </div>
        <div class="dropdown">
            <button class="dropbtn">Appointments ▾</button>
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

<div class="main">
    <div class="container">
        <h2>Edit Appointment</h2>
        <p><strong>Therapist:</strong> <?= htmlspecialchars($appointment['therapist_name']) ?></p>
        <p><strong>Student:</strong> <?= htmlspecialchars($appointment['student_name']) ?></p>
        <p><strong>Student ID:</strong> <?= htmlspecialchars($appointment['student_id']) ?></p>
        <p><strong>Date:</strong> <?= date('d M Y', strtotime($appointment['date'])) ?></p>
        <p><strong>Time:</strong> <?= date('gA', strtotime($appointment['start_time'])) ?> - <?= date('gA', strtotime($appointment['end_time'])) ?></p>

        <form method="POST">
            <label>Change Status:</label>
            <select name="status">
                <option value="confirmed" <?= $appointment['status'] == 'confirmed' ? 'selected' : '' ?>>Confirm</option>
                <option value="cancelled" <?= $appointment['status'] == 'cancelled' ? 'selected' : '' ?>>Cancel</option>
            </select>
            <br>
            <button type="submit">Save</button>
        </form>

        <div class="back">
            <a href="view_appointments.php">Back to Appointments Overview</a>
        </div>
    </div>
</div>

</body>
</html>
