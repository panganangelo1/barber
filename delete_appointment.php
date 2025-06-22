<?php
require 'db_connection.php'; // Ensure this file properly connects to your database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id'])) {
        $appointmentId = intval($_POST['id']);

        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
        $stmt->bind_param("i", $appointmentId);
        
        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Appointment deleted successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to delete appointment."]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid request. Missing appointment ID."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}

$conn->close();
