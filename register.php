<?php
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? OR email=?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare(
                "INSERT INTO users (username, email, full_name, phone, address, password, user_type)
                 VALUES (?, ?, ?, ?, ?, ?, 'buyer')"
            );
            $stmt->bind_param("ssssss", $username, $email, $full_name, $phone, $address, $hashed_password);

            if ($stmt->execute()) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buyer Registration | PartsLo.pk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Register as a buyer on PartsLo.pk - Auto Parts Online Store">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        :root {
            --primary-color: #dc3545;
            --primary-dark: #c82333;
            --secondary-color: #2c3e50;
            --light-gray: #f8f9fa;
            --border-color: #dee2e6;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        .registration-container {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin: 2rem auto;
            max-width: 1200px;
        }
        
        .left-panel {
            background: linear-gradient(rgba(44, 62, 80, 0.85), rgba(44, 62, 80, 0.9)), 
                        url('img/pic2.jpg') center/cover no-repeat;
            color: #fff;
            padding: 60px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }
        
        .logo-container {
            margin-bottom: 40px;
        }
        
        .logo {
            font-size: 2.2rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-icon {
            color: var(--primary-color);
            font-size: 2.5rem;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        
        .feature-list li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .feature-list li i {
            color: var(--primary-color);
        }
        
        .panel-footer {
            position: absolute;
            bottom: 30px;
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        .right-panel {
            padding: 50px 60px;
        }
        
        .form-header {
            margin-bottom: 40px;
        }
        
        .form-header h2 {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-header p {
            color: #6c757d;
        }
        
        .form-label {
            font-weight: 500;
            color: var(--secondary-color);
            margin-bottom: 8px;
        }
        
        .form-control {
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }
        
        .required-field::after {
            content: " *";
            color: var(--primary-color);
        }
        
        .password-strength {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        
        .btn-register {
            background: var(--primary-color);
            border: none;
            padding: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }
        
        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        .login-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .login-link:hover {
            text-decoration: underline;
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }
        
        .divider::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: var(--border-color);
        }
        
        .divider span {
            background: #fff;
            padding: 0 15px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        @media (max-width: 768px) {
            .registration-container {
                border-radius: 0;
                margin: 0;
            }
            
            .left-panel {
                padding: 40px 30px;
            }
            
            .right-panel {
                padding: 40px 30px;
            }
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 15px 20px;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="registration-container">
        <div class="row min-vh-100">

            <!-- LEFT PANEL (Brand & Info) -->
            <div class="col-lg-6 left-panel">
                <div class="logo-container">
                    <a href="index.php" class="logo">
                        <i class="bi bi-gear-fill logo-icon"></i>
                        PartsLo.pk
                    </a>
                    <p class="mt-2 opacity-75">Auto Parts Online Store</p>
                </div>
                
                <h4 class="mb-4">Join Our Auto Parts Community</h4>
                
                <div class="mb-4">
                    <p class="opacity-90">
                        PartsLo.pk provides online purchase of auto parts for
                        Motorbikes, Cars, and SUVs. Buyers can browse categories,
                        place orders, and pay via Cash on Delivery.
                    </p>
                </div>
                
                <h5 class="mb-3">Why Register With Us?</h5>
                <ul class="feature-list">
                    <li><i class="bi bi-check-circle-fill"></i> Wide range of Engine, Electric & Accessories</li>
                    <li><i class="bi bi-check-circle-fill"></i> Secure Cash on Delivery payment</li>
                    <li><i class="bi bi-check-circle-fill"></i> Real-time Order Status Tracking</li>
                    <li><i class="bi bi-check-circle-fill"></i> Buyer Feedback & Rating System</li>
                    <li><i class="bi bi-check-circle-fill"></i> Exclusive discounts for registered users</li>
                    <li><i class="bi bi-check-circle-fill"></i> Fast and reliable delivery</li>
                </ul>
                
                <div class="panel-footer">
                    <i class="bi bi-shield-check"></i> Secure & Confidential Registration
                </div>
            </div>

            <!-- RIGHT PANEL (Registration Form) -->
            <div class="col-lg-6 right-panel">
                <div class="form-header">
                    <h2>Create Buyer Account</h2>
                    <p>Fill in your details to get started</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="username" required
                                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                       placeholder="Choose a username">
                            </div>
                            <small class="form-text text-muted">Minimum 4 characters</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" class="form-control" name="email" required
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       placeholder="your.email@example.com">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                            <input type="text" class="form-control" name="full_name" required
                                   value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>"
                                   placeholder="Enter your full name">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Phone Number</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                <input type="text" class="form-control" name="phone"
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                       placeholder="+92 300 1234567">
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label required-field">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" name="password" required
                                       placeholder="Create a strong password">
                            </div>
                            <div class="password-strength">
                                <i class="bi bi-info-circle"></i> Use 8+ characters with letters and numbers
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                            <textarea class="form-control" name="address" rows="2"
                                      placeholder="Enter your delivery address"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>
                        <small class="form-text text-muted">For accurate delivery</small>
                    </div>

                    <div class="mb-4">
                        <label class="form-label required-field">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                            <input type="password" class="form-control" name="confirm_password" required
                                   placeholder="Re-enter your password">
                        </div>
                    </div>

                    <div class="mb-4">
                        <button class="btn btn-register btn-lg w-100">
                            <i class="bi bi-person-plus me-2"></i> Create Account
                        </button>
                    </div>

                    <div class="divider">
                        <span>Already have an account?</span>
                    </div>

                    <div class="text-center">
                        <p class="mb-0">
                            Sign in to your existing account
                            <a href="login.php" class="login-link ms-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login Here
                                
                            </a><br>
                            <a href="index.php" class="index-link ms-2">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Back to Home
                                
                            </a>
                        </p>
                    </div>
                    
                    <div class="mt-4 pt-3 border-top text-center">
                        <small class="text-muted">
                            By registering, you agree to our 
                            <a href="#" class="text-decoration-none">Terms of Service</a> and 
                            <a href="#" class="text-decoration-none">Privacy Policy</a>
                        </small>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

</body>
</html>