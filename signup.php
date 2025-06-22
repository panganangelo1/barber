<?php
require 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    // Check if passwords match
    if ($password !== $confirm_password) {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Passwords do not match!',
                    icon: 'error',
                    confirmButtonText: 'Try Again'
                });
              </script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
    $stmt->bind_param("ssss", $full_name, $email, $phone, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({
                    title: 'Signup Successful!',
                    text: 'You can now log in.',
                    icon: 'success',
                    confirmButtonText: 'Go to Login'
                }).then(() => {
                    window.location.href = 'login.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>

</body>
</html>
