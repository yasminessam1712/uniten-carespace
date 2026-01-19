<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "therapist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointment_id = $_POST['appointment_id'];
    $rating = $_POST['rating'];
    $feedback = trim($_POST['feedback']);


    $appointment_id = (int)$appointment_id;
    $rating = (int)$rating;
    $feedback = mysqli_real_escape_string($conn, $feedback);


    $update = "UPDATE appointments SET rating = '$rating', feedback = '$feedback' WHERE id = $appointment_id";
    if ($conn->query($update) === TRUE) {
        $_SESSION['rated'] = true;
    }

    header("Location: ../profile/userinformation.php");
    exit();
}
?>
