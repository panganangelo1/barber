<?php
include 'db_connection.php';

// Query to count customers
$query = "SELECT COUNT(*) as total FROM users WHERE role = 'customer'";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$total_customers = $row['total'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NICE GUYS</title>
    <link rel="stylesheet" href="admin_dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
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
                    <li class="active"><a href="#"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="appointment.php"><i class="fas fa-calendar-alt"></i> Appointments</a></li>
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
                <!-- Stats Cards -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-details">
                            <h3>Today's Appointments</h3>
                            <p class="stat-number">Loading...</p>
                            <p class="stat-change">Loading...</p>
                        </div>
                    </div>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                fetch('get_appointments.php')
                                    .then(response => response.json())
                                    .then(data => {
                                        document.querySelector(".stat-number").textContent = data.total_today;
                                        document.querySelector(".stat-change").innerHTML = 
                                            `<i class="fas ${data.percentage_change >= 0 ? 'fa-arrow-up' : 'fa-arrow-down'}"></i> 
                                            ${Math.abs(data.percentage_change)}% from yesterday`;
                                        document.querySelector(".stat-change").classList.add(data.percentage_change >= 0 ? 'positive' : 'negative');
                                    })
                                    .catch(error => console.error('Error fetching data:', error));
                            });
                        </script>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <div class="stat-details">
                        <h3>New Clients</h3>
                        <p class="stat-number"><?php echo $total_customers; ?></p>
                    </div>
                </div>
            </div>


<!-- Appointments & Schedule -->
<div class="dashboard-grid">
    <!-- Upcoming Appointments -->
    <div class="grid-item upcoming-appointments">
        <div class="section-header">
            <h2>Upcoming Appointments</h2>
        </div>
        <div class="appointment-list" id="appointment-list">
            <p>Loading upcoming appointments...</p>
        </div>
    </div>

    <!-- Ongoing Appointments -->
    <div class="grid-item ongoing-appointments">
        <div class="section-header">
            <h2>Ongoing Appointments</h2>
        </div>
        <div class="appointment-list" id="ongoing-appointment-list">
            <p>Loading ongoing appointments...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Fetch upcoming appointments
    fetch('get_today_appointments.php')
    .then(response => response.json())
    .then(data => {
        let appointmentList = document.getElementById("appointment-list");
        appointmentList.innerHTML = ""; // Clear existing content

        if (!Array.isArray(data) || data.length === 0) {
            appointmentList.innerHTML = "<p>No upcoming appointments for today.</p>";
            return;
        }

        data.forEach(appointment => {
            let time = appointment.appointment_time && appointment.appointment_time !== "00:00:00" 
                       ? appointment.appointment_time 
                       : "Time not set";
            
            let appointmentItem = `
                <div class="appointment-item">
                    <div class="appointment-time"><strong>${time}</strong></div>
                    <div class="appointment-info">
                        <h4>${appointment.customer_name || "Unknown"}</h4>
                        <p>${appointment.service || "No service specified"}</p>
                    </div>
                </div>
            `;
            appointmentList.innerHTML += appointmentItem;
        });
    })
    .catch(error => console.error('Error fetching data:', error));

    // Fetch ongoing appointments
    fetch('get_ongoing_appointments.php')
    .then(response => response.json())
    .then(data => {
        let ongoingAppointmentList = document.getElementById("ongoing-appointment-list");
        ongoingAppointmentList.innerHTML = ""; // Clear existing content

        if (!Array.isArray(data) || data.length === 0) {
            ongoingAppointmentList.innerHTML = "<p>No ongoing appointments.</p>";
            return;
        }

        data.forEach(appointment => {
            let time = appointment.appointment_time ? appointment.appointment_time : "Time not set";
            let customerName = appointment.customer_name || "Unknown";
            let service = appointment.service || "No service specified";
            let barber = appointment.barber || "No barber assigned"; // Now directly from the appointments table

            let appointmentItem = `
                <div class="appointment-item ongoing">
                    <div class="appointment-time"><strong>${time}</strong></div>
                    <div class="appointment-info">
                        <h4>${customerName}</h4>
                        <p>Service: ${service}</p>
                        <p>Barber: ${barber}</p>
                    </div>
                </div>
            `;
            ongoingAppointmentList.innerHTML += appointmentItem;
        });
    })
    .catch(error => console.error('Error fetching ongoing appointments:', error));

});
</script>


<style>
/* General Layout */
.dashboard-grid {
    display: flex;
    gap: 20px;
}

/* Grid Items */
.grid-item {
    flex: 1;
    padding: 15px;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
}

/* Appointment Items */
.appointment-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
}

.appointment-item:last-child {
    border-bottom: none;
}

.appointment-time {
    font-weight: bold;
    color: #555;
}

.appointment-info {
    flex: 1;
    text-align: right;
}

.appointment-info h4 {
    font-size: 1rem;
    margin-bottom: 3px;
    color: #333;
}

.appointment-info p {
    font-size: 0.9rem;
    color: #777;
}

/* Highlight Ongoing Appointments */
.ongoing {
    background: #fffae6;
    border-left: 5px solid #ffa500;
}
</style>

</body>
</html>
