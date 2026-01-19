<?php
session_start();

if (!isset($_SESSION['userid'])) {
    die("You must be logged in.");
}
$username = $_SESSION['userid']; 

$user_conn = new mysqli("localhost", "root", "root", "user");
if ($user_conn->connect_error) {
    die("User DB Connection failed: " . $user_conn->connect_error);
}

$user_query = $user_conn->prepare("SELECT first_name, last_name, email FROM login WHERE username = ?");
$user_query->bind_param("s", $username);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $student_name = $user_data['first_name'] . ' ' . $user_data['last_name'];  
    $email = $user_data['email'];  
} else {
    die("User data not found.");
}

$user_query->close();
$user_conn->close();

$conn = new mysqli("localhost", "root", "root", "therapist");
if ($conn->connect_error) {
    die("Booking DB Connection failed: " . $conn->connect_error);
}

$student_id = $username;  
$therapist_id = $_POST['therapist_id'];
$availability_id = $_POST['availability_id'];
$status = "confirmed";

$availability_query = $conn->prepare("SELECT date, start_time, end_time FROM availability WHERE id = ?");
$availability_query->bind_param("i", $availability_id);
$availability_query->execute();
$availability_result = $availability_query->get_result();

if ($availability_result->num_rows > 0) {
    $availability_data = $availability_result->fetch_assoc();
    $slot_date = $availability_data['date'];
    $slot_start_time = $availability_data['start_time'];
    $slot_end_time = $availability_data['end_time'];
} else {
    die("Invalid availability slot.");
}

$availability_query->close();

// Check if the user already has a conflicting appointment with another therapist
$check_booking_query = $conn->prepare("
    SELECT * FROM appointments 
    INNER JOIN availability ON appointments.availability_id = availability.id 
    WHERE appointments.student_id = ? 
    AND availability.date = ? 
    AND availability.start_time = ? 
    AND availability.end_time = ? 
    AND appointments.status = 'confirmed' 
    AND appointments.therapist_id != ?");
    
$check_booking_query->bind_param("ssssi", $student_id, $slot_date, $slot_start_time, $slot_end_time, $therapist_id);
$check_booking_query->execute();
$check_booking_result = $check_booking_query->get_result();

if ($check_booking_result->num_rows > 0) {
    $_SESSION['error_message'] = "You already have an appointment with a different therapist during this time slot. Please choose a different slot.";
    header("Location: error_booking.php");
    exit();
} else {
    // Proceed with booking if the slot is available and no conflicts
    $stmt = $conn->prepare("INSERT INTO appointments (student_name, student_id, therapist_id, availability_id, status, email) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiss", $student_name, $student_id, $therapist_id, $availability_id, $status, $email);

    if ($stmt->execute()) {
        // Update the availability status to 'booked'
        $conn->query("UPDATE availability SET status = 'booked' WHERE id = $availability_id");
        echo '
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Booking in Progress</title>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
            <style>
                body {
                    background:#f0f1ec;
                    font-family: "Inter", sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                    margin: 0;
                }
                .loader-container {
                    text-align: center;
                }
                .spinner {
                    border: 6px solid #d0d8c7;
                    border-top: 6px solid #4a7854;
                    border-radius: 50%;
                    width: 60px;
                    height: 60px;
                    animation: spin 1s linear infinite;
                    margin: 0 auto 20px;
                }
                .text {
                    font-size: 18px;
                    color: #333;
                    font-weight: 500;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
            </style>
        </head>
        <body>
            <div class="loader-container">
                <div class="spinner"></div>
                <div class="text">Booking your session...</div>
            </div>
            <script>
                setTimeout(() => {
                    window.location.href = "success_booking.php";
                }, 2500);
            </script>
        </body>
        </html>';
    } else {
        $_SESSION['error_message'] = "Booking failed. Please try again.";
        header("Location: errorll_booking.php");
        exit();
    }
}

$stmt->close();
$conn->close();
?>
