<?php
include 'db_connection.php';

$date_today = date('Y-m-d');

// Get today's appointment count
$sql_today = "SELECT COUNT(*) AS total FROM appointments WHERE DATE(appointment_time) = '$date_today'";
$result_today = $conn->query($sql_today);
$row_today = $result_today->fetch_assoc();
$total_today = $row_today['total'];

// Get yesterday's appointment count
$date_yesterday = date('Y-m-d', strtotime('-1 day'));
$sql_yesterday = "SELECT COUNT(*) AS total FROM appointments WHERE DATE(appointment_time) = '$date_yesterday'";
$result_yesterday = $conn->query($sql_yesterday);
$row_yesterday = $result_yesterday->fetch_assoc();
$total_yesterday = $row_yesterday['total'];

// Calculate percentage change
$percentage_change = ($total_yesterday > 0) ? (($total_today - $total_yesterday) / $total_yesterday) * 100 : 0;

$conn->close();

// Return JSON response
echo json_encode([
    'total_today' => $total_today,
    'percentage_change' => round($percentage_change, 2)
]);
?>
