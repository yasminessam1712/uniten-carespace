<?php
session_start();  

if (!isset($_SESSION['therapist_id'])) {
    header("Location: ../credentials/counsellor_login.php");  
    exit();
}

$therapist_id = $_SESSION['therapist_id'];

$conn = mysqli_connect("localhost", "root", "root", "therapist") or die("Cannot connect to the database");

$file_upload_message = ''; 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["appointment_file"])) {
    $upload_dir = "uploads/";  

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);  
    }

    $file_name = basename($_FILES["appointment_file"]["name"]);  
    $file_path = $upload_dir . $file_name;  
    $file_tmp_name = $_FILES["appointment_file"]["tmp_name"];  
    $file_error = $_FILES["appointment_file"]["error"];  

    if ($file_error === UPLOAD_ERR_OK) {

        if (move_uploaded_file($file_tmp_name, $file_path)) {

            $appointment_id = $_POST['appointment_id'];  
            $update_query = "UPDATE appointments SET report_file = '$file_name' WHERE id = $appointment_id AND therapist_id = $therapist_id";

            if (mysqli_query($conn, $update_query)) {
                $file_upload_message = 'success'; 
            } else {
                $file_upload_message = 'error'; 
            }
        } else {
            $file_upload_message = 'failed_move'; 
        }
    } else {
        $file_upload_message = 'error_uploading'; 
    }
}

$query = "
    SELECT a.*, t.name AS therapist_name, t.id AS therapist_id, v.date, v.start_time, v.end_time
    FROM appointments a
    JOIN therapists t ON a.therapist_id = t.id
    JOIN availability v ON a.availability_id = v.id
    WHERE a.therapist_id = $therapist_id
    ORDER BY v.date ASC, v.start_time ASC
";

$appointments_query = mysqli_query($conn, $query);

$all_appointments = [];
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

mysqli_data_seek($appointments_query, 0);
while ($row = mysqli_fetch_assoc($appointments_query)) {
    $row['formatted_date'] = date('d-m-y', strtotime($row['date']));
    $row['formatted_time'] = date('gA', strtotime($row['start_time'])) . " - " . date('gA', strtotime($row['end_time']));
    $row['file'] = $row['report_file'] ?? '';

    if ($row['status'] === 'confirmed' && ($row['date'] < $current_date || ($row['date'] == $current_date && $row['end_time'] < $current_time))) {
        $row['status'] = 'completed';
    }

    switch ($row['status']) {
        case 'confirmed': 
            $row['status_color'] = '#008000'; 
            $row['status_style'] = 'font-weight: bold; color: #008000;';
            break;
        case 'cancelled': 
            $row['status_color'] = '#e74c3c'; 
            $row['status_style'] = 'font-weight: bold; color: #e74c3c;';
            break;
        case 'completed': 
            $row['status_color'] = 'black'; 
            $row['status_style'] = 'font-weight: bold; color:rgb(0, 0, 0);';
            break;
        default: 
            $row['status_color'] = '#333'; 
            $row['status_style'] = 'font-weight: bold; color: #333;';
            break;
    }
    

    $all_appointments[] = $row;
}

$total = count($all_appointments);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments - UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        #appointmentsTable thead th,
        #appointmentsTable tbody td {
            padding: 14px 18px !important;
            vertical-align: middle;
            font-size: 14px;
        }
        .btn {
            padding: 8px 15px;
            font-size: 14px;
            font-weight: bold;  
            text-align: center;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: inline-block;
            background-color: #3498db; 
            color: white;
            transition: background-color 0.2s ease; 
        }

        
        .btn:hover {
            background-color: #2980b9; 
        }

        .btn:focus {
            outline: none; 
        }

        .upload-btn {
            padding: 6px 12px;
            background-color: #3498db;
            color: white;
            font-size: 14px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .upload-btn:hover {
            background-color: #2c80b4;
        }

        .file-input {
            width: 200px;
            padding: 6px;
            font-size: 14px;
        }

        .upload-form {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        th {
            background-color: #5e5c4f;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
        }
    </style>
</head>
<body>

<nav>
    <div class="brand">
        <img src="../../uploads/logo_official.png" alt="Logo">
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
        <h2>My Appointments Overview</h2>
        <div style="margin-top: 20px; margin-bottom: 20px;">
    <a href="../../uploads/report_sample.docx" class="btn btn-primary" download>
        <button class="btn">Download Sample Report</button>
    </a>
</div>
<label for="statusFilter"><strong>Filter by Status:</strong></label>
<select id="statusFilter" style="margin-bottom: 20px; padding: 6px;">
    <option value="">All</option>
    <option value="confirmed">Confirmed</option>
    <option value="cancelled">Cancelled</option>
    <option value="completed">Completed</option>
</select>

        <table id="appointmentsTable" class="display nowrap" style="width:100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Student Name</th>
                    <th>Student ID</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Status</th>
                    <th>Rating</th>
                    <th>File</th>
                    <th>Upload File</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_appointments as $index => $appointment): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($appointment['student_name']) ?></td>
                    <td><?= htmlspecialchars($appointment['student_id']) ?></td>
                    <td><?= $appointment['formatted_date'] ?></td>
                    <td><?= $appointment['formatted_time'] ?></td>
                    <td><span style="color:<?= $appointment['status_color'] ?>; font-weight: bold;"><?= $appointment['status'] ?></span></td>
                    <td><?php 

            echo $appointment['rating'] 
                ? str_repeat("★", $appointment['rating']) . str_repeat("☆", 5 - $appointment['rating']) 
                : 'No Rating'; 
        ?></td>
                    <td><?= $appointment['file'] ? "<a href='uploads/{$appointment['file']}' target='_blank'>Download</a>" : 'No File' ?></td>
                    <td>
                        <form method="POST" enctype="multipart/form-data" class="upload-form">
                            <input type="hidden" name="appointment_id" value="<?= $appointment['id'] ?>" />
                            <input type="file" name="appointment_file" class="file-input" required />
                            <button type="submit" class="upload-btn">Upload</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <p><strong>Total Appointments:</strong> <?= $total ?></p>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.colVis.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
 $(document).ready(function () {
    var table = $('#appointmentsTable').DataTable({
        dom: 'Bfrtip',
        buttons: [
            'copy', 'excel', 'pdf', 'print',
            {
                extend: 'colvis',
                text: 'Toggle Columns'
            }
        ]
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
        table.column(5).search(selectedStatus).draw(); // Column 5 = Status
    });
});

</script>

<script>
    <?php if ($file_upload_message === 'success'): ?>
        Swal.fire({
            icon: 'success',
            title: 'File Uploaded',
            text: 'Your file has been uploaded successfully!',
        });
    <?php elseif ($file_upload_message === 'error'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'There was an issue updating the appointment with the file.',
        });
    <?php elseif ($file_upload_message === 'failed_move'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to move the uploaded file.',
        });
    <?php elseif ($file_upload_message === 'error_uploading'): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error uploading file.',
        });
    <?php endif; ?>
</script>

</body>
</html>
