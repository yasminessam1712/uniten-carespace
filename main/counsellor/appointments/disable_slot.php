<?php
session_start();
if (!isset($_SESSION['therapist_id'])) {
    header("Location: ../credentials/counsellor_login.php");
    exit();
}

if (isset($_POST['slot_id'])) {
    $slot_id = $_POST['slot_id'];
    $conn = mysqli_connect("localhost", "root", "root", "therapist");

    $updateQuery = "UPDATE availability SET status = 'disabled' WHERE id = ?";
    $stmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmt, "i", $slot_id);

    if (mysqli_stmt_execute($stmt)) {
        echo 'success';
    } else {
        echo 'error';
    }
}
?>
