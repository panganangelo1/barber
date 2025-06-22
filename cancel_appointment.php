<?php
require 'db_connection.php'; // Ensure this file contains your database connection

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $appointmentId = intval($_POST['id']);

    // Check if the appointment exists
    $stmt = $conn->prepare("SELECT status FROM appointments WHERE id = ?");
    $stmt->bind_param("i", $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "Appointment not found."]);
        exit;
    }

    $row = $result->fetch_assoc();
    $currentStatus = $row['status'];

    // Only allow cancellation if it's not already Confirmed or Finished
    if ($currentStatus === 'Confirmed' || $currentStatus === 'Finished') {
        echo json_encode(["status" => "error", "message" => "This appointment cannot be canceled."]);
        exit;
    }

    // Update the appointment status to "Cancelled"
    $stmt = $conn->prepare("UPDATE appointments SET status = 'Cancelled' WHERE id = ?");
    $stmt->bind_param("i", $appointmentId);
    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment successfully cancelled."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to cancel appointment."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
