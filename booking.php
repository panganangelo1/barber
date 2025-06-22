<?php
require 'db_connection.php';
session_start();

header('Content-Type: application/json'); // Ensure JSON response

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["status" => "error", "message" => "Please log in to book an appointment."]);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Fetch user details from the users table
    $user_stmt = $conn->prepare("SELECT full_name, email, phone FROM users WHERE id = ?");
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows === 0) {
        echo json_encode(["status" => "error", "message" => "User not found."]);
        exit();
    }

    $user_data = $user_result->fetch_assoc();
    $customer_name = $user_data['full_name'];
    $email = $user_data['email'];
    $phone = $user_data['phone'];

    // Get booking form data
    $service = trim($_POST['service'] ?? '');
    $barber = trim($_POST['barber'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');

    // Validate inputs
    if (empty($service) || empty($barber) || empty($date) || empty($time)) {
        echo json_encode(["status" => "error", "message" => "All fields are required."]);
        exit();
    }

    // ✅ Get current server date and time
    date_default_timezone_set('Asia/Manila'); // Change this based on your timezone
    $current_datetime = date("Y-m-d H:i:s");
    $current_date = date("Y-m-d");
    $current_time = date("H:i:s");

    // ✅ Convert selected time to proper format
    $formatted_time = date("H:i:s", strtotime($time));
    $appointment_time = $date . ' ' . $formatted_time;

    // ✅ Prevent booking for past times on the same day
    if ($date == $current_date && $formatted_time < $current_time) {
        echo json_encode(["status" => "error", "message" => "You cannot book an appointment for a past time today."]);
        exit();
    }

    // ✅ Prevent booking for past dates
    if ($appointment_time < $current_datetime) {
        echo json_encode(["status" => "error", "message" => "You cannot book an appointment in the past."]);
        exit();
    }

    // ✅ Check if the time slot already has two appointments
    $check_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM appointments WHERE barber = ? AND appointment_time = ? AND status = 'Confirmed'");
    $check_stmt->bind_param("ss", $barber, $appointment_time);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $row = $check_result->fetch_assoc();

    if ($row['total'] >= 2) {
        echo json_encode(["status" => "error", "message" => "This time slot is fully booked. Please choose another."]);
        exit();
    }

    // ✅ Insert new appointment
    $stmt = $conn->prepare("INSERT INTO appointments (customer_name, email, phone, service, barber, appointment_time, status) 
                            VALUES (?, ?, ?, ?, ?, ?, 'Scheduled')");
    if (!$stmt) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ssssss", $customer_name, $email, $phone, $service, $barber, $appointment_time);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Appointment booked successfully!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to book appointment. Try again."]);
    }
    
    // Close statements and connection
    $stmt->close();
    $check_stmt->close();
    $user_stmt->close();
    $conn->close();
}
?>
