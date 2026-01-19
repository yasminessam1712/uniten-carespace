<?php
include 'db_conn_therapist.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = $_POST['appointment_id'];
    $rating = $_POST['rating'];

    if (!empty($appointment_id) && !empty($rating)) {
        $update = mysqli_query($conn_therapist, "UPDATE appointments SET rating = $rating WHERE id = $appointment_id");

        if ($update) {
            echo "<script>alert('Thank you for your rating!'); window.location.href='userinformation.php';</script>";
        } else {
            echo "<script>alert('Failed to submit rating.'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Missing rating or appointment ID.'); window.history.back();</script>";
    }
}
?>
