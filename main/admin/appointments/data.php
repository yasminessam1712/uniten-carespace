<?php

header('Content-Type: application/json');

$conn = mysqli_connect("localhost", "root", "root", "therapist") or die("Connection failed");

$month = $_GET['month'] ?? 'all';
$counselor = $_GET['counselor'] ?? 'all';

$conditions = [];
if ($month !== 'all') {
    $conditions[] = "MONTH(v.date) = " . intval($month);
}
if ($counselor !== 'all') {
    $conditions[] = "t.id = " . intval($counselor);
}
$where = count($conditions) > 0 ? 'WHERE ' . implode(' AND ', $conditions) : '';

$line_query = "
    SELECT MONTH(v.date) AS month, COUNT(*) AS total
    FROM appointments a
    JOIN availability v ON a.availability_id = v.id
    JOIN therapists t ON a.therapist_id = t.id
    $where
    GROUP BY MONTH(v.date)
    ORDER BY MONTH(v.date)
";
$line_result = mysqli_query($conn, $line_query);
$line_labels = [];
$line_data = [];
while ($row = mysqli_fetch_assoc($line_result)) {
    $line_labels[] = date('F', mktime(0, 0, 0, $row['month'], 10));
    $line_data[] = $row['total'];
}

$bar_query = "
    SELECT t.name AS counselor, COUNT(*) AS total
    FROM appointments a
    JOIN availability v ON a.availability_id = v.id
    JOIN therapists t ON a.therapist_id = t.id
    $where
    GROUP BY t.name
    ORDER BY total DESC
";
$bar_result = mysqli_query($conn, $bar_query);
$bar_labels = [];
$bar_data = [];
while ($row = mysqli_fetch_assoc($bar_result)) {
    $bar_labels[] = $row['counselor'];
    $bar_data[] = $row['total'];
}

echo json_encode([
    'lineChart' => [
        'labels' => $line_labels,
        'data' => $line_data
    ],
    'barChart' => [
        'labels' => $bar_labels,
        'data' => $bar_data
    ]
]);
?>
