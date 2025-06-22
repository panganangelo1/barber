<?php
require 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'] ?? '';
    $barber = $_POST['barber'] ?? '';

    if (empty($date) || empty($barber)) {
        echo json_encode(["status" => "error", "message" => "Invalid request."]);
        exit();
    }

    // Fetch only "Confirmed" bookings
    $stmt = $conn->prepare("SELECT TIME_FORMAT(appointment_time, '%H:%i') AS time FROM appointments WHERE barber = ? AND DATE(appointment_time) = ? AND status = 'Confirmed'");
    $stmt->bind_param("ss", $barber, $date);
    $stmt->execute();
    $result = $stmt->get_result();

    $booked_times = [];
    while ($row = $result->fetch_assoc()) {
        $booked_times[] = $row['time'];
    }

    echo json_encode(["status" => "success", "booked_times" => $booked_times]);
    $stmt->close();
    $conn->close();
}
?>
