<?php
include 'db_connection.php'; // Ensure database connection

date_default_timezone_set('Asia/Manila'); // Set your timezone
$current_time = date('H:i:s');

$query = "SELECT 
            customer_name, 
            service, 
            barber, 
            TIME_FORMAT(appointment_time, '%h:%i %p') AS appointment_time 
          FROM appointments 
          WHERE TIME(appointment_time) <= '$current_time' 
          AND TIME(appointment_time) >= SUBTIME('$current_time', '01:00:00') 
          ORDER BY appointment_time ASC";

$result = $conn->query($query);
$ongoing_appointments = [];

while ($row = $result->fetch_assoc()) {
    $ongoing_appointments[] = $row;
}

echo json_encode($ongoing_appointments);
?>
