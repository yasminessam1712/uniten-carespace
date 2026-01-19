<?php
session_start();
$conn = new mysqli("localhost", "root", "root", "therapist");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $appointment_id = (int)$_POST['appointment_id'];
    $email = $conn->real_escape_string($_POST['email']);
    $reason = $conn->real_escape_string($_POST['reason']);
    $date = date("Y-m-d H:i:s");

    $getAvailability = $conn->prepare("SELECT availability_id FROM appointments WHERE id = ?");
    $getAvailability->bind_param("i", $appointment_id);
    $getAvailability->execute();
    $availabilityResult = $getAvailability->get_result();
    $availabilityRow = $availabilityResult->fetch_assoc();
    $availability_id = $availabilityRow['availability_id'] ?? null;
    $getAvailability->close();

    $insert = "
        INSERT INTO cancellation_requests (appointment_id, email, reason, status, request_date)
        VALUES ($appointment_id, '$email', '$reason', 'approved', '$date')
    ";

    $updateAppointment = "
        UPDATE appointments
        SET status = 'cancelled'
        WHERE id = $appointment_id
    ";

$updateAvailability = "
    UPDATE availability
    SET is_booked = 0, status = 'available'
    WHERE id = $availability_id
";

    if (
        $conn->query($insert) === TRUE &&
        $conn->query($updateAppointment) === TRUE &&
        $conn->query($updateAvailability) === TRUE
    ) {
        $_SESSION['cancel_requested'] = true;
    } else {
        $_SESSION['cancel_requested'] = false;
    }

    header("Location: ../profile/userinformation.php");
    exit();
}
?>
