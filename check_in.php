<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $appointment_id = intval($_POST['id']);

    // Instead of updating to "Checked-In", ensure it stays "Confirmed"
    $sql = "UPDATE appointments SET status = 'Confirmed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Confirmed successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to update appointment status."]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request."]);
}
?>
