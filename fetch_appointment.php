<?php
session_start(); // Ensure session is started
require 'db_connection.php';

header('Content-Type: application/json'); // Ensure JSON response

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(["status" => "error", "message" => "User not logged in."]);
    exit;
}

$email = $_SESSION['email'];
$currentTime = date('Y-m-d H:i:s'); // Get current time

// Update status to 'Ongoing' if appointment time has started
$updateOngoingQuery = "UPDATE appointments 
                       SET status = 'Ongoing' 
                       WHERE appointment_time <= ? 
                       AND status = 'Confirmed'";

$stmt = $conn->prepare($updateOngoingQuery);
$stmt->bind_param("s", $currentTime);
$stmt->execute();

// Update status to 'Finished' **one hour after appointment_time**
$updateFinishedQuery = "UPDATE appointments 
                        SET status = 'Finished' 
                        WHERE appointment_time <= DATE_SUB(?, INTERVAL 1 HOUR) 
                        AND status = 'Ongoing'";

$stmt = $conn->prepare($updateFinishedQuery);
$stmt->bind_param("s", $currentTime);
$stmt->execute();

// Fetch updated user's appointments
$sql = "SELECT appointment_time, service, barber, status FROM appointments WHERE email = ? ORDER BY appointment_time ASC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

$appointments = [];
while ($row = $result->fetch_assoc()) {
    $appointments[] = [
        "date" => date("Y-m-d", strtotime($row['appointment_time'])),
        "time" => date("h:i A", strtotime($row['appointment_time'])),
        "service" => htmlspecialchars($row['service']),
        "barber" => htmlspecialchars($row['barber']),
        "status" => htmlspecialchars($row['status'])
    ];
}

// Debugging: Log the result
error_log("Appointments fetched: " . json_encode($appointments));

// If no appointments found
if (empty($appointments)) {
    echo json_encode(["status" => "error", "message" => "No appointments found."]);
    exit;
}

// Success response
echo json_encode(["status" => "success", "appointments" => $appointments]);
?>
