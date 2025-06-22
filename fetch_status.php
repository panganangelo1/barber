<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(["scheduled_count" => 0, "confirmed_count" => 0]);
    exit();
}

$email = $_SESSION['email'];

$query = "SELECT 
            SUM(CASE WHEN status = 'Scheduled' THEN 1 ELSE 0 END) AS scheduled_count, 
            SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_count 
          FROM appointments 
          WHERE email = '$email'";

$result = mysqli_query($conn, $query);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo json_encode([
        "scheduled_count" => $row['scheduled_count'] ?? 0,
        "confirmed_count" => $row['confirmed_count'] ?? 0
    ]);
} else {
    echo json_encode(["error" => mysqli_error($conn)]);
}

mysqli_close($conn);
?>
