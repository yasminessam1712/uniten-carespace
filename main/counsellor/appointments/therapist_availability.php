<?php
session_start();

if (!isset($_SESSION['therapist_id'])) {
    header("Location: ../credentials/counsellor_login.php");
    exit();
}

$therapist_id = $_SESSION['therapist_id'];
$conn = mysqli_connect("localhost", "root", "root", "therapist");
date_default_timezone_set("Asia/Kuala_Lumpur");

$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");

$message="";

$checkQuery = "
    SELECT id, date, end_time FROM availability 
    WHERE status NOT IN ('disabled', 'passed') 
    AND therapist_id = ?
";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->bind_param("i", $therapist_id);
$checkStmt->execute();
$result = $checkStmt->get_result();

$currentDate = date("Y-m-d");
$currentTime = date("H:i:s");

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $date = $row['date'];
    $end_time = $row['end_time'];

    if ($date < $currentDate) {
        // Date is in the past
        mysqli_query($conn, "UPDATE availability SET status = 'passed' WHERE id = $id");
    } elseif ($date == $currentDate && $end_time < $currentTime) {
        // Same day, and already ended
        mysqli_query($conn, "UPDATE availability SET status = 'passed' WHERE id = $id");
    }
    // Future dates are ignored — correct
}


$availabilityQuery = "SELECT * FROM availability WHERE therapist_id = ? ORDER BY date ASC, start_time ASC";
$availabilityStmt = mysqli_prepare($conn, $availabilityQuery);
mysqli_stmt_bind_param($availabilityStmt, "i", $therapist_id);
mysqli_stmt_execute($availabilityStmt);
$result = mysqli_stmt_get_result($availabilityStmt);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $t = (int)$_POST['therapist_id'];
    $d = $_POST['date'];
    $s = $_POST['time_slot'];
    $start_time = $s;
    $end_time = date("H:i:s", strtotime($s) + 3600);

    $checkQuery = "SELECT id, status FROM availability WHERE therapist_id = ? AND date = ? AND start_time = ? AND end_time = ? AND status != 'disabled'";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, "isss", $t, $d, $start_time, $end_time);
    mysqli_stmt_execute($stmt);
    $checkResult = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['status' => 'error', 'message' => 'The time slot already exists!']);
    } else {
        $insertQuery = "INSERT INTO availability (therapist_id, date, start_time, end_time, status) VALUES (?, ?, ?, ?, 'available')";
        $insertStmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($insertStmt, "isss", $t, $d, $start_time, $end_time);
        $executeInsert = mysqli_stmt_execute($insertStmt);

        if ($executeInsert) {
            echo json_encode(['status' => 'success', 'message' => 'Time slot created successfully!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error in creating time slot!']);
        }
    }
    exit();
}

echo $message;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Set Availability - UNITEN CARESPACE</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.18/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

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
        .main {
            padding: 40px;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        #status_filter {
            font-family: 'Poppins', sans-serif;     
            font-weight: bold;     
            padding: 6px 12px;     
            font-size: 14px;     
            border-radius: 6px;     
            border: 1.5px solid #ccc;     
            background-color: #f9f9f9;     
            color: #333;     
            margin-top: 10px;     
            width: 180px;     
            box-sizing: border-box;     
            transition: border-color 0.3s ease, 
            background-color 0.3s ease;     
            margin-bottom: 20px;
        }

        #status_filter:focus {
            outline: none;     
            border-color: #5e5c51;     
            background-color: #fff; }

        #status_filter:hover {
            border-color: #5e5c51; }

        .form-container label {
            font-weight: bold;     
            margin-right: 10px; }

        .form-container select,
        .form-container input[type="date"],
        .form-container input[type="time"] {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            padding: 6px 12px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            width: 180px;     
            margin-top: 10px;
        }

        .form-container select:focus,
        .form-container input[type="date"]:focus,
        .form-container input[type="time"]:focus {
            outline: none;
            border-color: #5e5c51;
        }


        form input[type="date"],
        form input[type="time"],
        form select {
            padding: 6px 10px;
            font-size: 14px;
            margin-right: 20px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            width: 200px;
        }
        form button {
            background-color: #5e5c51;
            color: white;
            font-weight: 600;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        button, .view-btn {
            padding: 6px 12px;     
            font-size: 14px;     
            font-weight: 600;     
            border-radius: 4px;     
            border: none;     
            cursor: pointer;     
            transition: background-color 0.3s ease, color 0.3s ease;     
            text-decoration-line: none;
        }

        .view-btn {
            background-color: #3498db;     
            color: white; }

        .view-btn:hover {
            background-color: #2980b9; }

        .disable-btn {
            background-color: #e74c3c;     
            color: white; }

        .disable-btn:hover {
            background-color: #c0392b; }

        #availabilityTable {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;  }
        #availabilityTable th, #availabilityTable td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        #availabilityTable th {
            background-color: #5e5c51;
            padding: 14px 18px !important;
            vertical-align: middle;
            font-size: 14px;
        }
        #availabilityTable td {
            
            padding: 14px 18px !important;
            vertical-align: middle;
            font-size: 14px;
        }
        #availabilityTable th:nth-child(5),
        #availabilityTable td:nth-child(5) {
            width: 120px;     
            text-align: center; 
        }
        #availabilityTable th:nth-child(1),
        #availabilityTable td:nth-child(1) {
            width: 50px;     
            text-align: left; 
        }
        .available-status {
            color: #000000;    
            font-weight: bold;
        }

        .booked-status {
            color: #28a745;     
            font-weight: bold;
        }

        .passed-status {
            color: #6c757d;     
            font-weight: bold;
        }

        .disabled-status {
            color: #dc3545;     
            font-weight: bold;
        }
        #availabilityTable_wrapper .dataTables_filter {
            margin-bottom: 20px; }

        #availabilityTable_wrapper .dataTables_paginate {
            margin-top: 20px; }

        th {
            background-color: #5e5c4f;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
        }
        #availabilityTable tbody tr.odd {
            background-color:rgb(238, 238, 238);        
        }

        #availabilityTable tbody tr.even {
            background-color: #ffffff;         
        }

        #availabilityTable tbody tr:hover {
            background-color:rgb(239, 239, 239);         
        }
        #availabilityTable tbody tr:hover td:first-child {
            background-color:rgb(233, 233, 233);        
        }

        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.18/dist/sweetalert2.all.min.js"></script>
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

<div class="main">
    <div class="container">
        <h2>My availability</h2>
    <div class="form-container">
        <form method="POST" id="availabilityForm">
            <input type="hidden" name="therapist_id" value="<?= $therapist_id ?>">

            <div>
                <label for="date">Date:</label>
                <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" required />
            
                <label for="time_slot">Time Slot:</label>
                <select id="time_slot" name="time_slot" required>
                    <option value="08:00:00">8am-9am</option>
                    <option value="10:00:00">10am-11am</option>
                    <option value="14:00:00">2pm-3pm</option>
                    <option value="16:00:00">4pm-5pm</option>
                </select>
                <button type="submit">Add Slot</button>
            </div>

        <div style="margin-top: 20px;margin-bottom: 20px;"></div>
        </form>
        
        <label for="statusFilter"><strong>Filter by Status:</strong></label>
        <select id="statusFilter" style="margin-bottom: 20px; padding: 6px;">
            <option value="">All</option>
            <option value="Available">Available</option>
            <option value="Booked">Booked</option>
            <option value="Passed">Passed</option>
            <option value="Disabled">Disabled</option>
        </select>

    </div>
<table id="availabilityTable" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
                <th>Time Slot</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if (mysqli_num_rows($result) > 0) {
            $count = 0;

            while ($row = mysqli_fetch_assoc($result)) {
                $start_time = date("g:ia", strtotime($row['start_time']));
                $end_time = date("g:ia", strtotime($row['end_time']));
            

                $statusClass = '';
                $statusText = ucfirst($row['status']); 
            
                switch ($row['status']) {
                    case 'available':
                        $statusClass = 'available-status';
                        break;
                    case 'booked':
                        $statusClass = 'booked-status';
                        break;
                    case 'passed':
                        $statusClass = 'passed-status';
                        break;
                    case 'disabled':
                        $statusClass = 'disabled-status';
                        break;
                    default:
                        $statusClass = 'default-status'; 
                        break;
                }
            

                echo "<tr>";
                echo "<td> <?= ++$count ?></td>";
                echo "<td>" . date('d-m-y', strtotime($row['date'])) . "</td>";
                echo "<td>" . $start_time . "-" . $end_time . "</td>";
                echo "<td class='" . $statusClass . "'>" . $statusText . "</td>";


                if ($row['status'] == 'available') {
                    echo "<td><button class='disable-btn' data-id='" . $row['id'] . "'>Disable</button></td>";
                } elseif ($row['status'] == 'booked'|| $row['status'] == 'passed') {

                    echo "<td><a href='fetch_appointment_details.php?id=" . $row['id'] . "' class='view-btn'>View</a></td>";
                } elseif ($row['status'] == 'disabled') {
                    echo "<td>No action can be made</td>";  

                } else {

                    echo "<td></td>";
                }
           
                echo "</tr>";
            }
            
        } else {
            echo "<tr><td colspan='5'>No availability slots found.</td></tr>";
        }
        ?>
        </tbody>
    </table>
</div>

<script>
$(document).ready(function () {

    var table = $('#availabilityTable').DataTable({
        "searching": true,  
        "paging": true,     
        "info": true,      
        "bLengthChange": true  
        
    });
 
    function resetRowNumbers() {
        var rowNum = 1;
        table.rows({ search: 'applied' }).every(function () {
            var row = this.node();
            $(row).find('td:first').text(rowNum);
            rowNum++;
        });
    }

    table.on('search.dt', function () {
        resetRowNumbers();
    });

    resetRowNumbers();

    // ✅ Status filter
    $('#statusFilter').on('change', function () {
        var selectedStatus = this.value.toLowerCase(); // e.g. 'confirmed'
        table.column(3).search(selectedStatus).draw(); // Column 5 = Status
    });
});

$('#availabilityForm').submit(function (e) {
    e.preventDefault();

    var formData = $(this).serialize();

    $.ajax({
        type: 'POST',
        url: '', 
        data: formData,
        success: function (response) {

            var res = JSON.parse(response);


            Swal.fire({
                title: res.status === 'success' ? 'Success' : 'Error',
                text: res.message,
                icon: res.status === 'success' ? 'success' : 'error',
                confirmButtonText: 'OK'
            }).then(() => {

                if (res.status === 'success') {
                    location.reload(); 
                }
            });
        },
        error: function () {
            Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
        }
    });
});

$('.disable-btn').on('click', function () {
    const slotId = $(this).data('id');

    Swal.fire({
        title: 'Are you sure?',
        text: "You are about to disable this slot.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#5e5c51',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, disable it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: 'disable_slot.php',
                method: 'POST',
                data: { slot_id: slotId },
                success: function (response) {
                    if (response === 'success') {

                        Swal.fire({
                            title: 'Disabled!',
                            text: 'The slot has been disabled.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {

                            location.reload(); 
                        });
                    } else {

                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    }
                }
            });
        }
    });
});

</script>

</body>
</html>  