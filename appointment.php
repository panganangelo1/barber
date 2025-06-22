<?php
require 'db_connection.php';

// Get the current timestamp
$current_time = date('Y-m-d H:i:s');

// Update status to 'On Going' if the appointment time has started but is still 'Confirmed'
$update_query = "UPDATE appointments SET status = 'On Going' WHERE appointment_time <= ? AND status = 'Confirmed'";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("s", $current_time);
$stmt->execute();

// Fetch all appointments
$query = "SELECT id, customer_name, email, phone, service, barber, appointment_time, status FROM appointments ORDER BY appointment_time";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NICE GUYS</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="appointment.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="icon" href="image/7338646.png">
</head>
<body>
    <div class="dashboard">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <h2>NICE GUYS</h2>
                <p>Barber Shop</p>
            </div>
            <nav class="menu">
                <ul>
                    <li><a href="admin_dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li class="active"><a href="appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
                </ul>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header -->
            <header>
                <div class="header-content">
                    <h1>Dashboard</h1>
                    <div class="header-actions">
                        <div class="search">
                            <input type="text" placeholder="Search...">
                            <button><i class="fas fa-search"></i></button>
                        </div>
                        <div class="user-profile">
                            <img src="/api/placeholder/40/40" alt="Profile">
                            <span>Admin</span>
                        </div>
                    </div>
                </div>
            </header>

<!-- Dashboard Content -->
<div class="dashboard-content">
    <!-- Appointments Table Section -->
    <div class="grid-item appointments-table">
        <div class="section-header">
            <h2>Appointments Schedule</h2>
            <div class="filter-actions">
                <select class="date-filter">
                    <option selected>Today</option>
                    <option>Tomorrow</option>
                    <option>This Week</option>
                    <option>Next Week</option>
                </select>
            </div>
        </div>

        <?php
include 'db_connection.php';

$currentTime = date('Y-m-d H:i:s');

// Automatically update past "Confirmed" appointments to "Finished"
$conn->query("UPDATE appointments SET status = 'Finished' WHERE appointment_time < '$currentTime' AND status = 'Confirmed'");

// Fetch updated appointments
$result = $conn->query("SELECT * FROM appointments ORDER BY appointment_time ASC");
?>

<div class="table-responsive">
    <table class="appointments-table">
        <thead>
            <tr>
                <th>Time</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Service</th>
                <th>Barber</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="appointments-body">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= date('h:i A', strtotime($row['appointment_time'])) ?></td>
                    <td><?= date('Y-m-d', strtotime($row['appointment_time'])) ?></td>
                    <td><?= htmlspecialchars($row['customer_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td><?= htmlspecialchars($row['service']) ?></td>
                    <td><?= htmlspecialchars($row['barber']) ?></td>
                    <td>
                        <span class="status-badge <?= strtolower($row['status']) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-btn btn-check-in" title="Check In" data-id="<?= $row['id'] ?>"
                                <?= ($row['status'] == 'Checked-In' || $row['status'] == 'Confirmed' || $row['status'] == 'Finished') ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                                <i class="fas fa-check-circle"></i>
                            </button>
                            <button class="action-btn btn-cancel" title="Cancel" data-id="<?= $row['id'] ?>"
                                <?= ($row['status'] == 'Confirmed' || $row['status'] == 'Finished') ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>>
                                <i class="fas fa-times"></i>
                            </button>
                            <button class="action-btn btn-delete" title="Delete" data-id="<?= $row['id'] ?>"><i class="fas fa-trash"></i></button>
                            <?php if ($row['status'] == 'Confirmed'): ?>
                            <button class="action-btn btn-finish" title="Finish" data-id="<?= $row['id'] ?>">
                                <i class="fas fa-check"></i>
                            </button>
                        <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {

    function updateAppointmentStatus() {
        $.ajax({
            url: "fetch_appointment.php",
            type: "GET",
            dataType: "json",
            success: function (data) {
                $("#appointments-body").html(data.html);
            },
            error: function () {
                console.log("Error fetching updated appointments.");
            }
        });
    }

    $(document).on("click", ".btn-check-in", function () {
        var button = $(this);
        var appointmentId = button.data("id");

        if (!appointmentId) {
            alert("Error: Appointment ID is missing.");
            return;
        }

        $.ajax({
            url: "check_in.php",
            type: "POST",
            data: { id: appointmentId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    var row = button.closest("tr");
                    row.find(".status-badge")
                        .text("Confirmed")
                        .removeClass()
                        .addClass("status-badge confirmed");
                    row.find(".btn-check-in, .btn-cancel").prop("disabled", true)
                        .css({ "opacity": "0.5", "cursor": "not-allowed" });
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function () {
                alert("An unexpected error occurred. Please try again.");
            }
        });
    });

    $(document).on("click", ".btn-cancel", function () {
        var button = $(this);
        var appointmentId = button.data("id");

        if (!confirm("Are you sure you want to cancel this appointment?")) {
            return;
        }

        $.ajax({
            url: "cancel_appointment.php",
            type: "POST",
            data: { id: appointmentId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    var row = button.closest("tr");
                    row.find(".status-badge")
                        .text("Cancelled")
                        .removeClass()
                        .addClass("status-badge cancelled");
                    row.find(".btn-check-in, .btn-cancel").prop("disabled", true)
                        .css({ "opacity": "0.5", "cursor": "not-allowed" });
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function () {
                alert("An unexpected error occurred. Please try again.");
            }
        });
    });

    $(document).on("click", ".btn-delete", function () {
        var button = $(this);
        var appointmentId = button.data("id");

        if (!confirm("Are you sure you want to delete this appointment?")) {
            return;
        }

        $.ajax({
            url: "delete_appointment.php",
            type: "POST",
            data: { id: appointmentId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    button.closest("tr").remove();
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function () {
                alert("An unexpected error occurred. Please try again.");
            }
        });
    });

    $(document).on("click", ".btn-finish", function () {
        var button = $(this);
        var appointmentId = button.data("id");

        if (!confirm("Mark this appointment as Finished?")) {
            return;
        }

        $.ajax({
            url: "finish_appointment.php",
            type: "POST",
            data: { id: appointmentId },
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    alert(response.message);
                    var row = button.closest("tr");
                    row.find(".status-badge")
                        .text("Finished")
                        .removeClass()
                        .addClass("status-badge finished");
                    button.remove();
                } else {
                    alert("Error: " + response.message);
                }
            },
            error: function () {
                alert("An unexpected error occurred. Please try again.");
            }
        });
    });
});
</script>

<style>
.status-badge {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    display: inline-block;
}

.status-badge.confirmed {
    background-color: green;
    color: white;
}

.status-badge.finished {
    background-color: gray;
    color: white;
}

.status-badge.cancelled {
    background-color: red;
    color: white;
}

.action-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>

    <div class="table-footer">
        <div class="pagination">
            <button class="page-btn prev-btn" disabled><i class="fas fa-chevron-left"></i></button>
            <span class="page-indicator">1 of 3</span>
            <button class="page-btn next-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
        <div class="records-info">
            Showing 5 of 15 appointments
        </div>
    </div>
</div>
</body>
</html>
<?php $conn->close(); ?>