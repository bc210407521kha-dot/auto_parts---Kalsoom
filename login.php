<?php
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username_or_email = trim($_POST['username_or_email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, username, email, password, user_type FROM users 
                            WHERE (username=? OR email=?)");
    $stmt->bind_param("ss", $username_or_email, $username_or_email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        $stmt->bind_result($id, $username, $email, $hashed_password, $user_type);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $id;
            $_SESSION['user_type'] = $user_type;

            if ($user_type == 'admin') {
                $_SESSION['admin_id'] = $id;
                $_SESSION['admin_username'] = $username;
                header("Location: admin_dashboard.php");
                exit;
            } elseif ($user_type == 'buyer') {
                $_SESSION['buyer_id'] = $id;
                $_SESSION['buyer_username'] = $username;
                header("Location: buyer_dashboard.php");
                exit;
            } else {
                $error = "Invalid user role.";
            }
        } else {
            $error = "Invalid password. Please try again.";
        }
    } else {
        $error = "User not found. Please check your credentials.";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | PartsLo.pk</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login to your PartsLo.pk account - Auto Parts Online Store">
    
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
        
        .login-container {
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
        
        .benefit-list {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        
        .benefit-list li {
            padding: 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .benefit-list li i {
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
        
        .btn-login {
            background: var(--primary-color);
            border: none;
            padding: 14px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }
        
        .btn-login:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.3);
        }
        
        .link-primary {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .link-primary:hover {
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
        
        .guest-notice {
            background: #f8f9fa;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            margin-top: 30px;
        }
        
        @media (max-width: 768px) {
            .login-container {
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
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div class="container-fluid py-4">
    <div class="login-container">
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
                
                <h4 class="mb-4">Welcome Back to PartsLo.pk</h4>
                
                <div class="mb-4">
                    <p class="opacity-90">
                        Sign in to access your account, track orders, manage your profile, 
                        and enjoy exclusive member benefits.
                    </p>
                </div>
                
                <h5 class="mb-3">Member Benefits</h5>
                <ul class="benefit-list">
                    <li><i class="bi bi-truck"></i> Track your orders in real-time</li>
                    <li><i class="bi bi-percent"></i> Exclusive discounts & offers</li>
                    <li><i class="bi bi-heart"></i> Save favorite items to wishlist</li>
                    <li><i class="bi bi-clock-history"></i> View order history</li>
                    <li><i class="bi bi-shield-check"></i> Secure payment options</li>
                    <li><i class="bi bi-star"></i> Earn loyalty points on purchases</li>
                </ul>
                
                <div class="panel-footer">
                    <i class="bi bi-shield-lock"></i> Your login is secure & encrypted
                </div>
            </div>

            <!-- RIGHT PANEL (Login Form) -->
            <div class="col-lg-6 right-panel d-flex align-items-center">
                <div class="w-100">
                    <div class="form-header">
                        <h2>Welcome Back</h2>
                        <p>Sign in to continue to your account</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger d-flex align-items-center">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label class="form-label required-field">Username or Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" name="username_or_email" required
                                       placeholder="Enter your username or email"
                                       value="<?= htmlspecialchars($_POST['username_or_email'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label required-field">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" class="form-control" name="password" required
                                       placeholder="Enter your password">
                            </div>
                            <div class="text-end mt-2">
                                <a href="forgot_password.php" class="link-primary" style="font-size: 0.9rem;">
                                    <i class="bi bi-question-circle"></i> Forgot Password?
                                </a>
                            </div>
                        </div>

                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                            <label class="form-check-label" for="rememberMe">
                                Remember me on this device
                            </label>
                        </div>

                        <div class="mb-4">
                            <button class="btn btn-login btn-lg w-100">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                            </button>
                        </div>

                        <div class="divider">
                            <span>New to PartsLo.pk?</span>
                        </div>

                        <div class="text-center mb-4">
                            <p class="mb-3">Don't have an account? Join our community today</p>
                            <a href="register.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person-plus me-2"></i> Create New Account
                            </a>
                        </div>

                        <div class="guest-notice">
                            <h6 class="mb-2"><i class="bi bi-info-circle me-2"></i>Continue as Guest</h6>
                            <p class="mb-2 small text-muted">
                                You can browse products without logging in. However, you'll need an account to:
                            </p>
                            <ul class="small text-muted mb-0">
                                <li>Place orders</li>
                                <li>Track shipments</li>
                                <li>Save payment methods</li>
                                <li>Access exclusive deals</li>
                            </ul>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top text-center">
                            <a href="index.php" class="link-primary">
                                <i class="bi bi-house me-1"></i> Back to Homepage
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>