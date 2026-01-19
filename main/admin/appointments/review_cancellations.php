<?php
$conn = mysqli_connect("localhost", "root", "root", "therapist") or die("Connection failed");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['request_id'])) {
    $id = (int) $_POST['request_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $req = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cancellation_requests WHERE id = $id"));
        $appt_id = $req['appointment_id'];

        mysqli_query($conn, "UPDATE appointments SET status = 'cancelled' WHERE id = $appt_id");
        $avail = mysqli_fetch_assoc(mysqli_query($conn, "SELECT availability_id FROM appointments WHERE id = $appt_id"))['availability_id'];
        mysqli_query($conn, "UPDATE availability SET status = 'available' WHERE id = $avail");

        mysqli_query($conn, "UPDATE cancellation_requests SET status = 'approved' WHERE id = $id");
    } elseif ($action === 'reject') {
        mysqli_query($conn, "UPDATE cancellation_requests SET status = 'rejected' WHERE id = $id");
    }

    header("Location: review_cancellations.php");
    exit();
}

$cancellation_sql = "
    SELECT reason, COUNT(*) as total 
    FROM cancellation_requests
";

$cancellation_month = $_GET['cancellation_month'] ?? 'overall';
$cancellation_year = $_GET['cancellation_year'] ?? 'overall'; // 

$cancellation_sql = "
    SELECT reason, COUNT(*) as total 
    FROM cancellation_requests
";

if ($cancellation_year !== 'overall') {
    $cancellation_sql .= " WHERE YEAR(request_date) = " . (int)$cancellation_year;
}

if ($cancellation_month !== 'overall') {

    if ($cancellation_year !== 'overall') {
        $cancellation_sql .= " AND MONTH(request_date) = " . (int)$cancellation_month;
    } else {

        $cancellation_sql .= " WHERE MONTH(request_date) = " . (int)$cancellation_month;
    }
}
$cancellation_sql .= " GROUP BY reason";
$cancellation_data = mysqli_query($conn, $cancellation_sql);

if (mysqli_num_rows($cancellation_data) === 0) {
    $noCancellationData = true;
} else {
    $noCancellationData = false;
}

$cancel_labels = [];
$cancel_counts = [];

while ($row = mysqli_fetch_assoc($cancellation_data)) {
    $cancel_labels[] = $row['reason'];
    $cancel_counts[] = (int)$row['total'];
}

$filter_name = $_GET['therapist'] ?? 'all';
$history_query = "
    SELECT r.*, a.student_name, a.student_id, t.name AS therapist_name, v.date, v.start_time, v.end_time
    FROM cancellation_requests r
    JOIN appointments a ON r.appointment_id = a.id
    JOIN therapists t ON a.therapist_id = t.id
    JOIN availability v ON a.availability_id = v.id
    WHERE r.status IN ('approved', 'rejected')
";

if ($filter_name !== 'all') {
    $history_query .= " AND t.name = '" . mysqli_real_escape_string($conn, $filter_name) . "'";
}

$search_text = $_GET['search'] ?? '';
if (!empty($search_text)) {
    $search_text = mysqli_real_escape_string($conn, $search_text);
    $history_query .= " AND (a.student_name LIKE '%$search_text%' OR r.reason LIKE '%$search_text%')";
}

$history_query .= " ORDER BY r.request_date DESC";
$history = mysqli_query($conn, $history_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta charset="UTF-8">
    <title>Review Cancellations</title>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> 
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f1ec;
            margin: 0;
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

        .box {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        table th, table td {
            padding: 14px 18px !important;
            border: 1px solid #ddd;
            text-align: left;
            font-size: 14px;
        }
        table th {
            background-color: #5e5c4f; color: #fff; font-weight: 700; font-size: 14px;
        }
        .action-btn {
            padding: 8px 16px;
            background: #5e5c51;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .action-btn:hover {
            background-color: #4a4a3c;
        }
        .approve {
            background-color: #2ecc71;
        }
        .reject {
            background-color: #e74c3c;
        }
        .no-data-message {
            text-align: center;
            color: gray;
            font-size: 16px;
        }
        #history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 20px;  
        }

        #history-table_wrapper .dataTables_length{
            margin-bottom: 20px; 
        }
        #history-table_wrapper .dataTables_filter{
            margin-bottom: 20px; 
        }
        #history-table tbody tr.odd {
            background-color:rgb(238, 238, 238);        
        }

        #history-table tbody tr.even {
            background-color: #ffffff;         
        }

        #history-table tbody tr:hover {
            background-color:rgb(239, 239, 239);         
        }
        #history-table tbody tr:hover td:first-child {
            background-color:rgb(233, 233, 233);        
        }


        .chart-container {
            width: 100%;            
            max-width: 900px;             
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        canvas {
            width: 100% !important;             
            height: 500px !important;             
            max-width: 75% !important;         
        }
 
        .styled-select {
            width: 100%;
            max-width: 300px;  
            padding: 8px 12px;
            font-size: 14px;
            color: #333;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            appearance: none; 
            -webkit-appearance: none; 
            -moz-appearance: none;
            transition: all 0.3s ease; 
        }

        .styled-select:focus {
            border-color: #5e5c51;
            outline: none;  /
            box-shadow: 0 0 6px rgba(94, 92, 81, 0.4);
        }

        .styled-select option {
            padding: 10px;
            font-size: 14px;
            background-color: #fff;
            color: #333;
        }

        .styled-select option:hover {
            background-color: #f1f1f1; 
        }

        .styled-select option:checked {
            background-color: #5e5c51;  
            color: #fff;  
        }
        

    </style>
</head>
<body>

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
                <a href="review_cancellations.php">Cancellation</a>
                
            </div>
        </div>
        <a href="../credentials/logout.php" id="logout-link">Logout</a>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('logout-link').addEventListener('click', function(e) {
  e.preventDefault(); // prevent immediate navigation
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
      window.location.href = this.href; // proceed to logout
    }
  });
});
</script>

    </div>
</nav>

<div class="box">
    <h2>Cancellation Request History</h2>

    <form method="GET" class="filter-form" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
        <label for="filter_therapist">Filter by Counsellor:</label>
        <select name="therapist" id="filter_therapist" class="styled-select"onchange="this.form.submit()">
        <option value="all">View All</option>
        <?php
        $tlist = mysqli_query($conn, "SELECT DISTINCT name FROM therapists");
        while ($t = mysqli_fetch_assoc($tlist)) {
            $selected = ($filter_name === $t['name']) ? 'selected' : '';
            echo "<option value='{$t['name']}' $selected>{$t['name']}</option>";
        }
        ?>
        </select>
    </form>
    <div style="margin-top: 20px;margin-bottom: 20px;"></div>

    <?php if (mysqli_num_rows($history) > 0): ?>
        <table id="history-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Counsellor</th>
                    <th>Student</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Email</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; while ($h = mysqli_fetch_assoc($history)): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $h['therapist_name'] ?></td>
                        <td><?= $h['student_name'] ?></td>
                        <td><?= date('j F Y', strtotime($h['date'])) ?></td>
                        <td><?= date('g:i A', strtotime($h['start_time'])) ?> - <?= date('g:i A', strtotime($h['end_time'])) ?></td>
                        <td><?= $h['email'] ?></td>
                        <td><?= htmlspecialchars($h['reason']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    <?php else: ?>
        <p>No historical records found.</p>
    <?php endif; ?>
</div>

<div class="box">
<form method="GET" class="filter-form" style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
    <label for="cancellation_year">Select Year:</label>
    <select name="cancellation_year"class="styled-select" onchange="this.form.submit()">
        <option value="overall" <?= ($cancellation_year === 'overall') ? 'selected' : '' ?>>All Years</option>
        <?php for ($year = 2020; $year <= date('Y'); $year++): ?>
            <option value="<?= $year ?>" <?= ($cancellation_year == $year) ? 'selected' : '' ?>>
                <?= $year ?>
            </option>
        <?php endfor; ?>
    </select>

    <label for="cancellation_month">Select Month:</label>
    <select name="cancellation_month"class="styled-select" onchange="this.form.submit()">
        <option value="overall" <?= ($cancellation_month === 'overall') ? 'selected' : '' ?>>All Months</option>
        <?php foreach (range(1, 12) as $m): ?>
            <option value="<?= $m ?>" <?= ($cancellation_month == $m) ? 'selected' : '' ?>>
                <?= date("F", mktime(0, 0, 0, $m, 1)) ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

    <h3>Cancellation Graph <?= $cancellation_month === 'overall' ? '(Overall)' : '(' . date("F", mktime(0, 0, 0, $cancellation_month, 1)) . ')' ?></h3>

    <?php if (isset($noCancellationData) && $noCancellationData): ?>
        <p class="no-data-message">No cancellation data available for the selected month.</p>
    <?php else: ?>
        <div class="chart-container">
            <canvas id="cancellationChart" height="300"></canvas>
        </div>
    <?php endif; ?>
</div>

<script>
$(document).ready(function() {
    $('#history-table').DataTable({
        "pageLength": 10, // Number of rows per page
        "lengthMenu": [10, 25, 50, 100], // Options for number of rows per page
        "searching": true, // Enable search
        "ordering": true,  // Enable sorting
        "paging": true,    // Enable pagination
        "info": true,      // Show info about how many entries are shown
    });
});
</script>


<script>
<?php if (!isset($noCancellationData) || !$noCancellationData): ?>
const ctxCancel = document.getElementById('cancellationChart').getContext('2d');

new Chart(ctxCancel, {
    type: 'bar',
    data: {
        labels: <?= json_encode($cancel_labels) ?>,
        datasets: [{
            label: 'Total Cancellations',
            data: <?= json_encode($cancel_counts) ?>,
            backgroundColor: ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6', '#e67e22'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            title: { display: true, text: 'Cancellation Reasons' }
        },
        scales: {
            y: {
                beginAtZero: true,
                title: { display: true, text: 'Number of Students' },
                ticks: {
                    stepSize: 1,
                    precision: 0 
                }
                
            },
            x: {
                ticks: { autoSkip: false },
                title: { display: true, text: 'Reasons' }
                
            }
        }
    }
});
<?php endif; ?>
</script>


</body>
</html>

