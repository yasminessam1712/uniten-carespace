<?php
session_start();
$conn_therapist = new mysqli("localhost", "root", "root", "therapist");
if ($conn_therapist->connect_error) die("Connection failed: " . $conn_therapist->connect_error);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['rating'])) {
    $appointment_id = intval($_POST['appointment_id']);
    $rating = intval($_POST['rating']);

    if ($rating >= 1 && $rating <= 5) {
        $update_rating = $conn_therapist->prepare("UPDATE appointments SET rating = ? WHERE id = ?");
        $update_rating->bind_param("ii", $rating, $appointment_id);
        $update_rating->execute();
        $update_rating->close();
        echo "<script>alert('✅ Thank you for your rating!'); window.location='userinformation.php';</script>";
    } else {
        echo "<script>alert('❌ Invalid rating value.'); window.location='userinformation.php';</script>";
    }
}
?>
