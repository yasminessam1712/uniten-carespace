<?php
$conn = mysqli_connect("localhost", "root", "root", "therapist") or die("Cannot connect");

$mode = $_GET['mode'] ?? 'monthly';
$selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
$rankingMonth = $_GET['ranking_month'] ?? 'overall';
$therapistId = $_GET['therapist'] ?? 'all';

$therapists = mysqli_query($conn, "SELECT id, name FROM therapists");
$therapist_map = [];
while ($row = mysqli_fetch_assoc($therapists)) {
    $therapist_map[$row['id']] = $row['name'];
}

$labels = [];
$datasets = [];
$colors = ['#3498db', '#e74c3c', '#2ecc71', '#f1c40f', '#9b59b6', '#e67e22'];
$colorIndex = 0;

if ($mode === 'weekly') {
    $start = date('Y-m-d', strtotime("monday this week"));
    $end = date('Y-m-d', strtotime("sunday this week"));

    $query = "
        SELECT t.name AS therapist_name, DATE(a.created_at) AS day, COUNT(*) AS total
        FROM appointments a
        JOIN therapists t ON a.therapist_id = t.id
        WHERE DATE(a.created_at) BETWEEN '$start' AND '$end'
    ";
    if ($therapistId !== 'all') $query .= " AND a.therapist_id = $therapistId";
    $query .= " GROUP BY t.name, DATE(a.created_at)";

    $results = mysqli_query($conn, $query);
    foreach (range(0, 6) as $i) {
        $labels[] = date('D, d M', strtotime("$start +$i day"));
    }

    while ($row = mysqli_fetch_assoc($results)) {
        $t = $row['therapist_name'];
        $day = date('D, d M', strtotime($row['day']));
        $datasets[$t][$day] = $row['total'];
    }

} else {
    $query = "
        SELECT t.name AS therapist_name, DAY(a.created_at) AS day, COUNT(*) AS total
        FROM appointments a
        JOIN therapists t ON a.therapist_id = t.id
        WHERE MONTH(a.created_at) = $selectedMonth
    ";
    if ($therapistId !== 'all') $query .= " AND a.therapist_id = $therapistId";
    $query .= " GROUP BY t.name, DAY(a.created_at)";

    $results = mysqli_query($conn, $query);

    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, date('Y'));
    foreach (range(1, $daysInMonth) as $i) {
        $labels[] = str_pad($i, 2, '0', STR_PAD_LEFT);
    }

    while ($row = mysqli_fetch_assoc($results)) {
        $t = $row['therapist_name'];
        $day = str_pad($row['day'], 2, '0', STR_PAD_LEFT);
        $datasets[$t][$day] = $row['total'];
    }
}

$chartData = [];
foreach ($datasets as $therapist => $data) {
    $points = [];
    foreach ($labels as $label) {
        $points[] = $data[$label] ?? 0;
    }
    $chartData[] = [
        'label' => $therapist,
        'data' => $points,
        'fill' => false,
        'borderColor' => $colors[$colorIndex++ % count($colors)],
        'tension' => 0.3
    ];
}

if ($rankingMonth === 'overall') {
    $ranking = mysqli_query($conn, "
        SELECT t.name, COUNT(*) as total, AVG(a.rating) as avg_rating
        FROM appointments a
        JOIN therapists t ON a.therapist_id = t.id
        GROUP BY t.name
        ORDER BY avg_rating DESC, total DESC

    ");
} else {
    $ranking = mysqli_query($conn, "
        SELECT t.name, COUNT(*) as total, AVG(a.rating) as avg_rating
        FROM appointments a
        JOIN therapists t ON a.therapist_id = t.id
        WHERE MONTH(a.created_at) = $rankingMonth
        GROUP BY t.name
        ORDER BY avg_rating DESC, total DESC

    ");
}
$rankings = mysqli_fetch_all($ranking, MYSQLI_ASSOC);

$feedback_month = $_GET['feedback_month'] ?? 'overall';
$feedback_therapist = $_GET['feedback_therapist'] ?? 'all';

$feedback_query = "
    SELECT t.name AS therapist_name, a.feedback, a.rating, DATE(a.created_at) AS date
    FROM appointments a
    JOIN therapists t ON a.therapist_id = t.id
    WHERE a.feedback IS NOT NULL AND a.feedback != ''
";

if ($feedback_month !== 'overall') {
    $feedback_query .= " AND MONTH(a.created_at) = " . (int)$feedback_month;
}
if ($feedback_therapist !== 'all') {
    $feedback_query .= " AND a.therapist_id = " . (int)$feedback_therapist;
}

$feedback_query .= " ORDER BY a.created_at DESC";
$feedback_result = mysqli_query($conn, $feedback_query);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <title>Therapist Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

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
        }

        .dropdown-content a {
        color: #333;
        padding: 10px 16px;
        text-decoration: none;
        display: block;
        font-size: 14px;
        font-weight: 600;
        }

        .dropdown-content a:hover {
        background-color: #f3f3f3;
        }

        .dropdown:hover .dropdown-content {
        display: block;
        }

        .main {
            padding: 40px;
        }
        .filters {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        select {
            padding: 6px 10px;
            border-radius: 6px;
        }
        .card {
            background: white;
            padding: 12px;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
    canvas {
        height: 100px; 
        max-height: 500px; 
    }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
            font-size: 14px;
            background-color: white;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }

        table th {
            background-color: #f4f4f4;
            color: #333;
            font-weight: 600;
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        table td {
            padding: 12px;
            border: 1px solid #eee;
            color: #333;
        }

        table tr:nth-child(even) td {
            background-color: #f9f9f9;
        }

        table tr:hover td {
            background-color: #f1f1f1;
        }

        .no-data-message {
            text-align: center;
            color: gray;
            font-size: 16px;
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
    <h2 style="text-align:center;">Counsellor Booking Summary</h2>

    <form class="filters" method="GET">
        <select name="mode" onchange="this.form.submit()">
            <option value="monthly" <?= $mode === 'monthly' ? 'selected' : '' ?>>Monthly</option>
            <option value="weekly" <?= $mode === 'weekly' ? 'selected' : '' ?>>Weekly</option>
        </select>
        <?php if ($mode === 'monthly'): ?>
            <select name="month" onchange="this.form.submit()">
                <?php foreach (range(1, 12) as $m): ?>
                    <option value="<?= $m ?>" <?= $selectedMonth == $m ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $m, 1)) ?></option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
        <select name="therapist" onchange="this.form.submit()">
            <option value="all" <?= $therapistId === 'all' ? 'selected' : '' ?>>All Therapists</option>
            <?php foreach ($therapist_map as $id => $name): ?>
                <option value="<?= $id ?>" <?= $therapistId == $id ? 'selected' : '' ?>><?= $name ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="card">
    <h3>Total Bookings <?= $mode === 'weekly' ? 'This Week' : 'in ' . date('F', mktime(0,0,0,$selectedMonth,1)) ?></h3>
    <canvas id="bookingChart"></canvas>
    <button onclick="downloadChart()" style="font-weight: bold; margin-top:10px; padding:8px 14px; border:none; background:#5e5c51; color:white; border-radius:5px; cursor:pointer;">Download Chart</button>
</div>

    <form class="filters" method="GET">
        <input type="hidden" name="mode" value="<?= $mode ?>">
        <input type="hidden" name="month" value="<?= $selectedMonth ?>">
        <input type="hidden" name="therapist" value="<?= $therapistId ?>">
        <label>Ranking:</label>
        <select name="ranking_month" onchange="this.form.submit()">
            <option value="overall" <?= $rankingMonth === 'overall' ? 'selected' : '' ?>>Overall</option>
            <?php foreach (range(1, 12) as $m): ?>
                <option value="<?= $m ?>" <?= $rankingMonth == $m ? 'selected' : '' ?>><?= date("F", mktime(0, 0, 0, $m, 1)) ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <div class="card">
        <h3>Counselor Rankings (<?= $rankingMonth === 'overall' ? 'Overall' : date("F", mktime(0,0,0,$rankingMonth,1)) ?>)</h3>
        <table id="rankingTable">

            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Counsellors</th>
                    <th>Total Bookings</th>
                    <th>Averge Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rankings as $i => $row): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= $row['name'] ?></td>
                        <td><?= $row['total'] ?></td>
                        <td><?= is_null($row['avg_rating']) ? 'not available' : number_format($row['avg_rating'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<script>
$(document).ready(function () {
    $('#feedbackTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        paging: false,
        ordering: false,
        info: false
    });
});
</script>

<script>
const ctx = document.getElementById('bookingChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: <?= json_encode($chartData) ?>
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'top' },
            title: {
                display: true,
                text: 'Booking Trends by Therapist'
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: '<?= $mode === "weekly" ? "Day of Week" : "Day of Month" ?>',
                    font: {
                        size: 16, 
                        weight: 'bold', 
                        family: "'Poppins', sans-serif", 
                    },
                }
            },
            y: {
                beginAtZero: true,
                title: {
                    display: true,
                    text: 'Bookings',
                    font: {
                        size: 16, 
                        weight: 'bold', 
                        family: "'Poppins', sans-serif", 
                    },
                }
            }
        }
    }
});

</script>
<script>
$(document).ready(function () {
    $('#rankingTable').DataTable({
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'csv', 'pdf', 'print'],
        paging: false,
        ordering: false,
        info: false
    });
});
</script>
<script>
function downloadChart() {
    const link = document.createElement('a');
    link.download = 'booking_chart.png';
    link.href = document.getElementById('bookingChart').toDataURL('image/png');
    link.click();
}
</script>

</body>
</html>
