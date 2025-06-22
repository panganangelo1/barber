<?php
include 'db_connection.php'; // Ensure the connection file is included

date_default_timezone_set('Asia/Manila');
$current_time = date('H:i:s');

$query = "SELECT customer_name, service, TIME_FORMAT(appointment_time, '%h:%i %p') AS appointment_time 
          FROM appointments 
          WHERE TIME(appointment_time) > '$current_time' 
          ORDER BY appointment_time ASC";

$result = $conn->query($query);
$appointments = [];

while ($row = $result->fetch_assoc()) {
    $appointments[] = $row;
}

echo json_encode($appointments);
?>
