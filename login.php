
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NICE GUYS</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="icon" href="image/7338646.png">
</head>
<body>
    <div class="container">
        <div class="forms-container">
            <div class="logo">
                <h1>NICE<span>GUYS</span></h1>
            </div>
            
            <div class="forms-wrapper">
                <!-- Login Form -->
                <div class="form-container login-container" id="login-form">
                    <h2>Welcome Back</h2>
                    <p class="form-subtitle">Login to manage your appointments</p>
                    
                    <form action="process_login.php" method="POST">
                        <div class="form-group">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                        </div>

                        <div class="form-options">
                            <div class="remember-me">
                                <input type="checkbox" id="remember" name="remember">
                                <label for="remember">Remember me</label>
                            </div>
                            <div class="forgot-password">
                                <a href="#">Forgot Password?</a>
                            </div>
                        </div>

                        <button type="submit" class="btn">Login</button>
                    </form>

                    <div class="social-login">
                        <p>Or login with</p>
                        <div class="social-icons">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-google"></i></a>
                        </div>
                    </div>

                    <div class="form-footer">
                        <p>Don't have an account? <a href="#" class="switch-form" data-form="signup">Sign Up</a></p>
                    </div>
                </div>

                <!-- Signup Form -->
                <div class="form-container signup-container hidden" id="signup-form">
                    <h2>Create Account</h2>
                    <p class="form-subtitle">Sign up for easy appointment booking</p>

                    <form action="signup.php" method="POST">
                        <div class="form-group">
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" name="full_name" placeholder="Full Name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" placeholder="Email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <input type="tel" name="phone" placeholder="Phone Number" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" placeholder="Password" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                            </div>
                        </div>

                        <div class="form-agreement">
                            <input type="checkbox" id="agree" name="agree" required>
                            <label for="agree">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
                        </div>

                        <button type="submit" class="btn">Sign Up</button>
                    </form>
                    
                    <div class="form-footer">
                        <p>Already have an account? <a href="#" class="switch-form" data-form="login">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="image-container">
            <div class="overlay"></div>
            <div class="image-content">
                <h2>Premium Barbershop Experience</h2>
                <p>Join Nice Guys for the best barbershop experience in town.</p>
                <a href="index.php" class="btn outline-btn">Back to Home</a>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const switchFormLinks = document.querySelectorAll('.switch-form');
            
            switchFormLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const formToShow = this.getAttribute('data-form');
                    
                    if (formToShow === 'login') {
                        document.getElementById('login-form').classList.remove('hidden');
                        document.getElementById('signup-form').classList.add('hidden');
                    } else {
                        document.getElementById('login-form').classList.add('hidden');
                        document.getElementById('signup-form').classList.remove('hidden');
                    }
                });
            });
        });
    </script>
</body>
</html>