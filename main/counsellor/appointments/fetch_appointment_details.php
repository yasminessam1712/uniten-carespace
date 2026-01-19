<?php
session_start();

if (!isset($_SESSION['therapist_id'])) {
    header("Location: ../credentials/counsellor_login.php");
    exit();
}

$appointment_id = $_GET['id']; 

error_log("Appointment ID: " . $appointment_id);

$conn = mysqli_connect("localhost", "root", "root", "therapist");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "
    SELECT a.student_name, a.student_id, a.status AS appointment_status, a.feedback, 
           b.date AS availability_date, b.start_time, b.end_time, b.status AS availability_status
    FROM appointments a
    LEFT JOIN availability b ON a.availability_id = b.id
    WHERE a.availability_id = ? AND a.status IN ('confirmed', 'completed')
    ORDER BY a.id DESC LIMIT 1
";



$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $appointment_id); 
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if ($row = mysqli_fetch_assoc($result)) {

    $student_name = !empty($row['student_name']) ? $row['student_name'] : 'No booking';
    $student_id = !empty($row['student_id']) ? $row['student_id'] : 'No booking';
    $appointment_status = !empty($row['appointment_status']) ? ucfirst($row['appointment_status']) : 'No booking';  


    $availability_date = !empty($row['availability_date']) ? date("d-m-y", strtotime($row['availability_date'])) : 'No booking';
    $start_time = !empty($row['start_time']) ? date("g:ia", strtotime($row['start_time'])) : 'No booking';
    $end_time = !empty($row['end_time']) ? date("g:ia", strtotime($row['end_time'])) : 'No booking';
    $availability_status = !empty($row['availability_status']) ? ucfirst($row['availability_status']) : 'No booking';
} else {
    $no_booking = true; 
}

error_log("Fetched appointment details for ID: " . $appointment_id);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Details - UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
   
     body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        background-color: #f0f1ec;
        color: #333;
        min-height: 100vh;
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
        .dropdown:hover .dropdown-content {
            display: block;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
            width: 80%;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
            text-align: left;
        }

        .container h2 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .details-table td {
            padding: 12px 15px;
            font-size: 14px;
            vertical-align: middle;
        }

        .details-table .label {
            font-weight: 600;
            color: #333;
            width: 200px; }

        .details-table tr:nth-child(odd) {
            background-color:rgb(234, 233, 233); }
        .back-link {
            font-size: 16px;
            color: white;
            background-color:#5e5c51;     
            padding: 10px 20px;     
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            margin-left: 300px;
            transition: background-color 0.3s ease;     
            text-align: center;
            font-weight: bold;
        }

        .back-link:hover {
            background-color:#5e5c52;    
            text-decoration: none;
        }

        @media (max-width: 768px) {
            .container {
                width: 95%;    
             }

            .label {
                width: auto;     
            }
        }
        .no-booking-message {
            font-size: 18px;
            font-weight: bold;
            color: #e74c3c;             
            text-align: center;
            padding: 20px;
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
                <a href="view_appointments.php">View Appointments</a>
              
                <a href="therapist_availability.php">Set Availability</a>
            </div>
        </div>
        <a href="../profile/counsellor_profile.php">Profile</a>
        <a href="../credentials/logout.php" id="logout-link">Logout</a>
    </div>
</nav>

<div class="container">
    <?php if (isset($no_booking) && $no_booking): ?>
        <div class="no-booking-message">
            No booking was made with this time slot.
        </div>
    <?php else: ?>
        <h2>Appointment Details</h2>
        <table class="details-table">
            <tr>
                <td class="label">Student Name:</td>
                <td><?= $student_name ?></td>
            </tr>
            <tr>
                <td class="label">Student ID:</td>
                <td><?= $student_id ?></td>
            </tr>
            <tr>
                <td class="label">Status:</td>
                <td><?= $appointment_status ?></td>
            </tr>
            <tr>
                <td class="label">Date:</td>
                <td><?= $availability_date ?></td>
            </tr>
            <tr>
                <td class="label">Time Slot:</td>
                <td><?= $start_time . ' - ' . $end_time ?></td>
            </tr>
            <tr>
                <td class="label">Status:</td>
                <td><?= $availability_status ?></td>
            </tr>
        </table>
    <?php endif; ?>

    <a href="therapist_availability.php" class="back-link">Back to Availability</a>
</div>

</body>
</html>

<?php

mysqli_close($conn);
?>