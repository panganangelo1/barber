<?php
session_start();
include 'db_connection.php';

$email = $_SESSION['email'] ?? null;

$scheduled_count = 0;
$confirmed_count = 0;

if ($email) {
    $query = "SELECT 
                SUM(CASE WHEN status = 'Scheduled' THEN 1 ELSE 0 END) AS scheduled_count, 
                SUM(CASE WHEN status = 'Confirmed' THEN 1 ELSE 0 END) AS confirmed_count 
              FROM appointments 
              WHERE email = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $scheduled_count = $row['scheduled_count'] ?? 0;
        $confirmed_count = $row['confirmed_count'] ?? 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NICE GUYS</title>
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="image/7338646.png">


</head>
<body>

<!-- Header -->
<header>
    <div class="container">
        <div class="logo">
            <h1>NICE GUYS</h1>
        </div>
        <nav>
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#services">Services</a></li>
                <li><a href="#barbers">Our Barbers</a></li>
                <li><a href="#gallery">Gallery</a></li>
                <li><a href="#booking">Book Appointment</a></li>

                <?php if (isset($_SESSION['email'])): ?>
                    <li><a href="#">Profile</a></li>
                    <li>
                        <a href="#" id="viewAppointment">
                            Status
                            <span id="scheduledBadge" class="badge pending" style="display: <?= ($scheduled_count > 0) ? 'inline' : 'none'; ?>">
                                <?= $scheduled_count; ?>
                            </span>
                            <span id="confirmedBadge" class="badge confirmed" style="display: <?= ($confirmed_count > 0) ? 'inline' : 'none'; ?>">
                                <?= $confirmed_count; ?>
                            </span>
                        </a>
                    </li>
                    <li><a href="logout.php">Logout</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</header>

<style>
    .badge {
        font-size: 12px;
        font-weight: bold;
        padding: 3px 7px;
        border-radius: 50%;
        position: relative;
        top: -10px;
        left: 5px;
    }
    .pending {
        background: red;
        color: white;
    }
    .confirmed {
        background: green;
        color: white;
        margin-left: 5px;
    }
</style>

<script>
function updateStatus() {
    fetch('fetch_status.php')
    .then(response => response.json())
    .then(data => {
        let scheduledBadge = document.getElementById("scheduledBadge");
        let confirmedBadge = document.getElementById("confirmedBadge");

        if (data.scheduled_count > 0) {
            scheduledBadge.style.display = "inline";
            scheduledBadge.textContent = data.scheduled_count;
        } else {
            scheduledBadge.style.display = "none";
        }

        if (data.confirmed_count > 0) {
            confirmedBadge.style.display = "inline";
            confirmedBadge.textContent = data.confirmed_count;
        } else {
            confirmedBadge.style.display = "none";
        }
    })
    .catch(error => console.error("Error fetching status:", error));
}

// Refresh every 5 seconds
setInterval(updateStatus, 5000);
</script>

<!-- Appointment Modal -->
<div id="appointmentModal" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Your Appointment</h2>
        <div id="appointmentDetails">
            <p>Loading...</p>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    let viewAppointment = document.getElementById("viewAppointment");
    let modal = document.getElementById("appointmentModal");
    let closeModal = document.querySelector(".close-btn");

    // Open modal when clicking the "Status" button
    viewAppointment.addEventListener("click", function(event) {
        event.preventDefault();
        modal.style.display = "flex"; 

        fetch('fetch_appointment.php')
            .then(response => response.json())
            .then(data => {
                let detailsDiv = document.getElementById("appointmentDetails");
                if (data.status === "success") {
                    let tableHTML = `<table border="1">
                                        <tr>
                                            <th>Date</th>
                                            <th>Time</th>
                                            <th>Service</th>
                                            <th>Barber</th>
                                            <th>Status</th>
                                        </tr>`;
                    data.appointments.forEach(appointment => {
                        tableHTML += `<tr>
                                        <td>${appointment.date}</td>
                                        <td>${appointment.time}</td>
                                        <td>${appointment.service}</td>
                                        <td>${appointment.barber}</td>
                                        <td class="status-${appointment.status.toLowerCase()}">${appointment.status}</td>
                                      </tr>`;
                    });
                    tableHTML += `</table>`;
                    detailsDiv.innerHTML = tableHTML;
                } else {
                    detailsDiv.innerHTML = `<p style="color:red;">${data.message}</p>`;
                }
            })
            .catch(error => {
                detailsDiv.innerHTML = `<p style="color:red;">Error loading appointment data</p>`;
            });
    });

    // Close modal when clicking the X
    closeModal.addEventListener("click", function() {
        modal.style.display = "none";
    });

    // Close modal when clicking outside the modal content
    window.addEventListener("click", function(event) {
        if (event.target === modal) {
            modal.style.display = "none";
        }
    });
});
</script>

<!-- Updated CSS -->
<style>
    /* Modal Background */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Dark overlay */
        backdrop-filter: blur(5px);
        justify-content: center;
        align-items: center;
    }

    /* Modal Content */
    .modal-content {
        background: white;
        width: 90%;
        max-width: 500px;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.3);
        position: relative;
        text-align: center;
        animation: fadeIn 0.3s ease-in-out;
    }

    /* Fade In Animation */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Close Button */
    .close-btn {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 24px;
        color: #333;
        cursor: pointer;
        transition: 0.3s;
    }

    .close-btn:hover {
        color: red;
        transform: scale(1.2);
    }

    /* Table Styling */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }
    
    th, td {
        padding: 8px 12px;
        border: 1px solid #ddd;
    }
    
    th {
        background-color: #f2f2f2;
        font-weight: bold;
    }
    
    tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    /* Appointment Details */
    #appointmentDetails {
        font-size: 16px;
        color: #555;
        line-height: 1.6;
    }

    /* Status Colors */
    .status-confirmed {
        color: green;
        font-weight: bold;
    }

    .status-pending {
        color: orange;
        font-weight: bold;
    }

    .status-cancelled {
        color: red;
        font-weight: bold;
    }

    /* Button Styling */
    #viewAppointmentBtn {
        padding: 10px 20px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    #viewAppointmentBtn:hover {
        background-color: #45a049;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .modal-content {
            width: 95%;
            padding: 15px;
        }
        
        table {
            font-size: 14px;
        }
        
        th, td {
            padding: 6px 8px;
        }
    }
    
    @media (max-width: 480px) {
        table {
            font-size: 12px;
        }
        
        th, td {
            padding: 4px 6px;
        }
    }
</style>


        <div class="mobile-menu">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</header>

<section id="home" class="hero">
    <video id="hero-video" autoplay muted playsinline class="hero-video">
        <source id="video-source" src="image/25.mp4" type="video/mp4">
    </video>
    <div class="container">
        <div class="hero-content">
            <h2>Expert Barbers for the Man</h2>
            <p>Premium cuts and styles by experienced professionals</p>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="btn-login">LOGIN</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    const videos = ["image/24.mp4", "image/23.mp4", "image/25.mp4"]; // Ensure '25.mp4' is also included
    let currentVideo = 0;
    const videoElement = document.getElementById("hero-video");

    function playNextVideo() {
        currentVideo = (currentVideo + 1) % videos.length; // Loop through videos
        videoElement.src = videos[currentVideo];
        videoElement.load();
        videoElement.play();
    }

    videoElement.addEventListener("ended", playNextVideo);

    // Handle when the tab is inactive (pause prevention)
    document.addEventListener("visibilitychange", () => {
        if (!document.hidden && videoElement.paused) {
            videoElement.play();
        }
    });

    // Restart video in case it gets paused due to inactivity
    setInterval(() => {
        if (videoElement.paused) {
            videoElement.play();
        }
    }, 5000); // Check every 5 seconds
</script>

    
    <style>
    .hero {
        position: relative;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
    }
    
    .hero-video {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: -1;
        transition: opacity 1s ease-in-out; /* Smooth transition */
    }
    
    .hero-video.fade-out {
        opacity: 0; /* Video fades out before switching */
    }
    
    .hero-content {
        position: relative;
        z-index: 1;
        padding: 20px;
        border-radius: 10px;
    }
    .btn-login {
        display: inline-block;
        margin-top: 10px;
        padding: 10px 20px;
        background-color: #c59d5f;
        color: white;
        text-decoration: none;
        font-size: 16px;
        font-weight: bold;
        font-family: 'Poppins', sans-serif;
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    .btn-login:hover {
        background-color: #a8864f;
    }
    </style>
    
    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <div class="section-header">
                <h2>Our Services</h2>
                <p></p>
            </div>
            <div class="services-grid">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-cut"></i>
                    </div>
                    <h3>Haircut</h3>
                    <p>Precision cutting tailored to your style</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h3>Beard Trim</h3>
                    <p>Shape and style your beard with precision</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-soap"></i>
                    </div>
                    <h3>Shave</h3>
                    <p>Traditional hot towel shave experience</p>
                </div>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <h3>Full Package</h3>
                    <p>Haircut, beard trim, and hot towel shave</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Barbers Section -->
    <section id="barbers" class="barbers">
        <div class="container">
            <div class="section-header">
                <h2>Meet Our Barbers</h2>
                <p>Skilled professionals with years of experience</p>
            </div>
            <div class="barbers-grid">
                <div class="barber-card">
                    <div class="barber-img">
                        <img src="image/barber 3.jpg" alt="Barber Frederick">
                    </div>
                    <h3>Frederick Garcia</h3>
                    <p>Barber</p>
                    <div class="social-icons">
                        <a href="https://www.instagram.com/frederickgarcia749/"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/frederick.garcia.722584"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
                <div class="barber-card">
                    <div class="barber-img">
                        <img src="image/barber 2.jpg" alt="Barber John">
                    </div>
                    <h3>John Froilan</h3>
                    <p>Senior Stylist</p>
                    <div class="social-icons">
                        <a href="https://www.instagram.com/jkatsupoy/"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/katsupoyl"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
                <div class="barber-card">
                    <div class="barber-img">
                        <img src="image/barber 1.jpg" alt="Barber Angelo">
                    </div>
                    <h3>Angelo Pangan</h3>
                    <p>Beard Specialist</p>
                    <div class="social-icons">
                        <a href="https://www.instagram.com/ur.gello/"><i class="fab fa-instagram"></i></a>
                        <a href="https://www.facebook.com/eloychokoy"><i class="fab fa-facebook"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section id="gallery" class="gallery">
        <div class="container">
            <div class="section-header">
                <h2>Our Work</h2>
                <p>Browse our portfolio of premium cuts and styles</p>
            </div>
            <div class="gallery-grid">
                <div class="gallery-item">
                    <img src="image/beard trim.jpg" alt="Haircut Style 1">
                </div>
                <div class="gallery-item">
                    <img src="image/grooming.jpg" alt="Haircut Style 2">
                </div>
                <div class="gallery-item">
                    <img src="image/hairstyle.jpg" alt="Haircut Style 3">
                </div>
                <div class="gallery-item">
                    <img src="image/special cut.jpg" alt="Haircut Style 4">
                </div>
                <div class="gallery-item">
                    <img src="image/styling.jpg" alt="Haircut Style 5">
                </div>
                <div class="gallery-item">
                    <img src="image/shaving.jpg" alt="Haircut Style 6">
                </div>
            </div>
        </div>
    </section>

<!-- Booking Section -->
<section id="booking" class="booking">
    <div class="container">
        <div class="section-header">
            <h2>Book Your Appointment</h2>
            <p>Schedule your next premium grooming experience</p>
        </div>
        <div class="booking-content">
            <div class="booking-form">
                <form id="bookingForm" method="POST">
                    <!-- Hidden Fields for Auto-Filled Customer Info -->
                    <input type="hidden" id="customer_name" name="customer_name">
                    <input type="hidden" id="email" name="email">
                    <input type="hidden" id="phone" name="phone">

                    <div class="form-group">
                        <label for="service">Select Service</label>
                        <select id="service" name="service" required>
                            <option value="">Choose a service</option>
                            <option value="haircut">Haircut</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="barber">Select Barber</label>
                        <select id="barber" name="barber" required>
                            <option value="">Choose a barber</option>
                            <option value="Angelo Pangan">Angelo Pangan</option>
                            <option value="John Froilan">John Froilan</option>
                            <option value="Frederick Garcia">Frederick Garcia</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="date">Select Date</label>
                        <input type="date" id="date" name="date" required>
                    </div>
                    <div class="form-group">
                        <label for="time">Select Time</label>
                        <select id="time" name="time" required>
                            <option value="">Choose a time</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-block">Book Now</button>
                    </div>
                </form>
            </div>
            <div class="booking-info">
                <h3>Booking Information</h3>
                <ul>
                    <li><i class="fas fa-clock"></i> Mon-Fri: 9AM - 6PM</li>
                    <li><i class="fas fa-clock"></i> Sat-Sun: 10AM - 5PM</li>
                    <li><i class="fas fa-phone"></i>+639 219-413-3963</li>
                    <li><i class="fas fa-map-marker-alt"></i>BF Resort Village, Las Pinas City</li>
                </ul>
                <p>Please arrive 10 minutes before your appointment. Cancellations require 24-hour notice.</p>
            </div>
        </div>
    </div>
</section>

<!-- Bootstrap & jQuery -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    let today = new Date().toISOString().split('T')[0];
    $("#date").attr("min", today);

    fetchUserDetails();
    populateTimeSlots();

    $("#date, #barber").on("change", function () {
        let selectedDate = $("#date").val();
        let selectedBarber = $("#barber").val();

        if (selectedDate) {
            disablePastTimeSlots(selectedDate);
        }

        if (selectedDate && selectedBarber) {
            fetchBookedTimes(selectedDate, selectedBarber);
        }
    });

    $("#bookingForm").submit(function (e) {
        e.preventDefault();

        if ($("#time option:selected").prop("disabled")) {
            Swal.fire({
                title: "Time Slot Unavailable",
                text: "This time is already fully booked or past. Please choose another.",
                icon: "error",
                confirmButtonText: "OK"
            });
            return;
        }

        var isLoggedIn = <?php echo isset($_SESSION['user_id']) ? 'true' : 'false'; ?>;
        if (!isLoggedIn) {
            Swal.fire({
                icon: 'warning',
                title: 'Login Required',
                text: 'You must be logged in to book an appointment.',
                confirmButtonText: 'Login Now'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        } else {
            submitBooking();
        }
    });

    function fetchUserDetails() {
        $.ajax({
            url: "fetch_user.php",
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    $("#customer_name").val(response.data.full_name);
                    $("#email").val(response.data.email);
                    $("#phone").val(response.data.phone);
                }
            },
            error: function () {
                console.error("Error fetching user data.");
            }
        });
    }

    function populateTimeSlots() {
        let timeSlots = [
            "09:00 AM", "10:00 AM", "11:00 AM", "12:00 PM",
            "01:00 PM", "02:00 PM", "03:00 PM", "04:00 PM", "05:00 PM"
        ];

        let timeDropdown = $("#time");
        timeDropdown.empty();
        timeDropdown.append('<option value="">Choose a time</option>');
        timeSlots.forEach(time => {
            let value = convertTo24HourFormat(time);
            timeDropdown.append(`<option value="${value}">${time}</option>`);
        });
    }

    function fetchBookedTimes(date, barber) {
        $.ajax({
            url: "fetch_booked_times.php",
            type: "POST",
            data: { date: date, barber: barber },
            dataType: "json",
            success: function (response) {
                populateTimeSlots();
                if (response.status === "success") {
                    let bookedTimes = response.booked_times;
                    let bookedCount = {};

                    bookedTimes.forEach(time => {
                        bookedCount[time] = (bookedCount[time] || 0) + 1;
                        if (bookedCount[time] >= 2) {
                            let option = $("#time option[value='" + time + "']");
                            if (option.length) {
                                option.prop("disabled", true).text(convertTo12HourFormat(time) + " (Fully Booked)");
                            }
                        }
                    });
                }
                disablePastTimeSlots(date);
            },
            error: function () {
                console.error("Error fetching booked times.");
            }
        });
    }

    function disablePastTimeSlots(selectedDate) {
        let now = new Date();
        let currentTime = now.getHours().toString().padStart(2, '0') + ":" + now.getMinutes().toString().padStart(2, '0');

        $("#time option").each(function () {
            let optionTime = $(this).val();
            if (optionTime && selectedDate === today && optionTime < currentTime) {
                $(this).prop("disabled", true).text(convertTo12HourFormat(optionTime) + " (Past)");
            }
        });
    }

    function submitBooking() {
        $.ajax({
            url: "booking.php",
            type: "POST",
            data: $("#bookingForm").serialize(),
            dataType: "json",
            success: function (response) {
                if (response.status === "success") {
                    Swal.fire({
                        title: "Booking Successful!",
                        text: response.message,
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        $("#bookingForm")[0].reset();
                        populateTimeSlots();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: "Booking Failed",
                        text: response.message,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            },
            error: function () {
                Swal.fire({
                    title: "Error",
                    text: "An unexpected error occurred. Please try again.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        });
    }

    function convertTo24HourFormat(time) {
        let match = time.match(/(\d+):(\d+)\s(AM|PM)/);
        if (!match) return "";

        let [_, hour, minute, period] = match;
        hour = parseInt(hour);
        if (period === "PM" && hour !== 12) hour += 12;
        if (period === "AM" && hour === 12) hour = 0;

        return `${hour.toString().padStart(2, "0")}:${minute}`;
    }

    function convertTo12HourFormat(time) {
        if (!time || !time.includes(":")) return "Invalid Time";
        
        let [hour, minute] = time.split(":");
        hour = parseInt(hour);
        minute = minute.padStart(2, "0");

        if (isNaN(hour)) return "Invalid Time";
        
        let period = hour >= 12 ? "PM" : "AM";
        hour = hour % 12 || 12;

        return `${hour}:${minute} ${period}`;
    }
});
</script>


    <!-- Footer Section -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <h2>NICE GUYS</h2>
                    <p></p>
                </div>
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#barbers">Our Barbers</a></li>
                        <li><a href="#gallery">Gallery</a></li>
                        <li><a href="#booking">Book Now</a></li>
                    </ul>
                </div>
                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i>BF Resort Village, Las Pinas City</li>
                        <li><i class="fas fa-phone"></i>+639 219-413-3963</li>
                        <li><i class="fas fa-envelope"></i> info@niceguys.com</li>
                    </ul>
                </div>
                <div class="footer-social">
                    <h3>Follow Us</h3>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-yelp"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 Nice Guys Barber Shop. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="script.js"></script>
</body>
</html>
