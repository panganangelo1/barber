<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $appointmentId = intval($_POST['id']);

    // Check if the appointment exists and is confirmed
    $checkQuery = "SELECT * FROM appointments WHERE id = ? AND status = 'Confirmed'";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bind_param('i', $appointmentId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update the status to Finished
        $updateQuery = "UPDATE appointments SET status = 'Finished' WHERE id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param('i', $appointmentId);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Appointment marked as finished."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update appointment."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid appointment or status."]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
