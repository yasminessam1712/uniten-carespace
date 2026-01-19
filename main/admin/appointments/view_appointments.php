<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../credentials/adminlogin.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "root", "therapist") or die("Cannot connect");

if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM appointments WHERE id = $delete_id");
    header("Location: view_appointments.php?action=delete");
    exit();
}

if (isset($_GET['action'])) {
    $message = $_GET['action'] === 'edit' ? 'Appointment updated successfully!' :
               ($_GET['action'] === 'delete' ? 'Appointment deleted successfully!' : null);
    $messageType = $_GET['action'] === 'delete' ? 'error' : 'success';
}

$therapists = mysqli_query($conn, "SELECT * FROM therapists");
$selected_id = $_GET['id'] ?? 'all';
$selected_therapist = ($selected_id !== 'all') ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM therapists WHERE id = $selected_id")) : null;

mysqli_query($conn, "
    UPDATE appointments a
    JOIN availability v ON a.availability_id = v.id
    SET a.status = 'completed'
    WHERE a.status = 'confirmed'
    AND (v.date < CURDATE() OR (v.date = CURDATE() AND v.end_time < CURTIME()))
");

$query = "
    SELECT a.*, t.name AS therapist_name, t.id AS therapist_id, v.date, v.start_time, v.end_time
    FROM appointments a
    JOIN therapists t ON a.therapist_id = t.id
    JOIN availability v ON a.availability_id = v.id
";

if ($selected_id !== 'all') {
    $query .= " WHERE a.therapist_id = $selected_id";
}
$query .= " ORDER BY v.date ASC, v.start_time ASC";

$appointments_query = mysqli_query($conn, $query);
$all_appointments = [];
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

while ($row = mysqli_fetch_assoc($appointments_query)) {
    $row['formatted_date'] = date('d-m-y', strtotime($row['date']));
    $row['formatted_time'] = date('gA', strtotime($row['start_time'])) . " - " . date('gA', strtotime($row['end_time']));
    $row['report_file'] = $row['report_file'] ?? ''; // Correctly reference 'report_file'

    if ($row['status'] === 'confirmed' && ($row['date'] < $current_date || ($row['date'] == $current_date && $row['end_time'] < $current_time))) {
        $row['status'] = 'completed';
    }

    switch ($row['status']) {
        case 'confirmed': $row['status_color'] = '#008000'; break;
        case 'cancelled': $row['status_color'] = '#e74c3c'; break;
        case 'completed': $row['status_color'] = 'black'; break;
        default: $row['status_color'] = '#333'; break;
    }

    $all_appointments[] = $row;

}

$counts = [];
$total = 0;
$res = mysqli_query($conn, "SELECT therapist_id, COUNT(*) as total FROM appointments GROUP BY therapist_id");

if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        $tnameResult = mysqli_query($conn, "SELECT name FROM therapists WHERE id = {$row['therapist_id']}");
        $tname = mysqli_fetch_assoc($tnameResult)['name'] ?? "Unknown";
        $counts[$tname] = $row['total'];
        $total += $row['total'];
    }
}

$total = count($all_appointments);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments - UNITEN Carespace</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">


    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
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
        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 1.5rem;
            justify-content: center;
        }
        .filter-form select,
        .filter-form input,
        .filter-form button {
            padding: 10px;
            font-size: 14px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }
        .edit-btn {
            background-color: #3498db;
            color: white;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 13px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        .edit-btn:hover {
            background-color: #2c80b4;
        }
        #appointmentsTable thead th,
        #appointmentsTable tbody td {
            padding: 14px 18px !important;
            vertical-align: middle;
            font-size: 14px;
        }

        .custom-table th {
            background-color: #5e5c4f;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
        }

    </style>
</head>
<body>

<?php if (isset($message)): ?>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            icon: '<?= $messageType ?>',
            title: '<?= $messageType === 'success' ? 'Success' : 'Deleted' ?>',
            text: '<?= $message ?>',
            showConfirmButton: false,
            timer: 1800,
            timerProgressBar: true
        });
    });
</script>
<?php endif; ?>

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
        <h2>Appointments Overview</h2>

        <form method="GET" class="filter-form">
            <select name="id" onchange="this.form.submit()">
                <option value="all" <?= $selected_id === 'all' ? 'selected' : '' ?>>All Counsellors</option>
                <?php
                mysqli_data_seek($therapists, 0);
                while ($t = mysqli_fetch_assoc($therapists)) {
                    echo "<option value='{$t['id']}'" . ($selected_id == $t['id'] ? ' selected' : '') . ">{$t['name']} ({$t['specialty']})</option>";
                }
                ?>
            </select>
        </form>

        <div class="filter-form">
            <select id="statusFilter">
                <option value="all">All Status</option>
                <option value="confirmed">Confirmed</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <input type="text" id="dateRange" placeholder="Date range">
            <button onclick="resetFilters()">Reset Filters</button>
        </div>
        <table id="appointmentsTable" class="custom-table display nowrap" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Counsellor</th>
            <th>Student Name</th>
            <th>Student ID</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Rating</th>
            <th>File</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>


        <div class="totals">
    <p><strong>Total Appointments:</strong> <?= $total ?></p>
    <?php if (!empty($counts)): ?>
        <?php foreach ($counts as $name => $c): ?>
            <p><strong><?= htmlspecialchars($name) ?>:</strong> <?= $c ?> appointments</p>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

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
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

<script>
    let appointments = <?= json_encode($all_appointments) ?>;
    let table;

    function loadTable(data) {
        table.clear().rows.add(data).draw();
    }

    function resetFilters() {
        $('#statusFilter').val('all');
        $('#dateRange').val('');
        loadTable(appointments);
    }

    function applyFilters() {
    let status = $('#statusFilter').val();
    let range = $('#dateRange').val();
    let filtered = appointments;

    if (status !== 'all') {
        filtered = filtered.filter(a => a.status === status);
    }

    if (range) {
        const [start, end] = range.split(' - ').map(d =>
            moment(d, 'DD-MM-YY').format('YYYY-MM-DD')
        );
        filtered = filtered.filter(a => a.date >= start && a.date <= end);
    }

    loadTable(filtered);
}


    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This appointment will be permanently deleted.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74c3c',
            cancelButtonColor: '#aaa',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `view_appointments.php?delete_id=${id}`;
            }
        });
    }

    $(document).ready(function () {
        $('#dateRange').daterangepicker({
    autoUpdateInput: false,
    locale: {
        format: 'DD-MM-YY',
        cancelLabel: 'Clear'
    }
});


$('#dateRange').on('apply.daterangepicker', function(ev, picker) {
    $(this).val(picker.startDate.format('DD-MM-YY') + ' - ' + picker.endDate.format('DD-MM-YY'));
    applyFilters();
});


$('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
    $(this).val('');
    applyFilters();
});


        $.fn.dataTable.ext.errMode = 'throw'; 

        table = $('#appointmentsTable').DataTable({
    data: appointments,
    dom: 'Bfrtip',
    buttons: [
        'copy', 'excel', 'pdf', 'print',
        {
            extend: 'colvis',
            text: 'Toggle Columns'
        },
        {
            text: 'Reset Columns',
            action: function (e, dt, node, config) {
                dt.columns().visible(true); 
            }
        }
    ],
    columns: [
    { data: null, render: (data, type, row, meta) => meta.row + 1 },
    { data: 'therapist_name' },
    { data: 'student_name' },
    { data: 'student_id' },
    { data: 'formatted_date' },
    { data: 'formatted_time' },
    {
        data: 'status',
        render: (data, type, row) => `<span style="color:${row.status_color};font-weight: bold;">${data}</span>`
    },
    {
        data: 'rating',
        render: (data) => data ? '★'.repeat(data) + '☆'.repeat(5 - data) : 'not available'
    },
   
    {
    data: 'report_file', 
    render: (data) => data ? `<a href='../../counsellor/appointments/uploads/${data}' download='${data}'>Download</a>` : 'No File'
},

]
    
});

        $('#statusFilter, #dateRange').on('change', applyFilters);
    });


</script>

</body>
</html>
