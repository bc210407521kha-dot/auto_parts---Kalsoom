<?php
include "config.php";
if (!isset($_SESSION['buyer_id'])) {
    header("Location: login.php");
    exit;
}

// Get buyer stats
$buyer_id = $_SESSION['buyer_id'];
$total_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE user_id = $buyer_id")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE user_id = $buyer_id AND status = 'Pending'")->fetch_assoc()['count'];
$delivered_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE user_id = $buyer_id AND status = 'Delivered'")->fetch_assoc()['count'];
$cart_items = $conn->query("SELECT COUNT(*) as count FROM cart WHERE user_id = $buyer_id")->fetch_assoc()['count'];

$buyer_username = $_SESSION['buyer_username'];
$buyer_name = $_SESSION['full_name'] ?? $buyer_username;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Buyer Dashboard - PartsLo.pk</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
    body {
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }
    
    .main-content {
        margin-left: 250px; /* Same width as sidebar */
        padding: 20px;
        transition: all 0.3s;
    }
    
    /* Stats Cards */
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
    
    .stat-card.orders { border-left: 4px solid #dc3545; }
    .stat-card.pending { border-left: 4px solid #ffc107; }
    .stat-card.delivered { border-left: 4px solid #198754; }
    .stat-card.cart { border-left: 4px solid #6f42c1; }
    
    .stat-card i {
        font-size: 2.5rem;
        margin-bottom: 15px;
        opacity: 0.8;
    }
    
    .stat-card h4 {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stat-card p {
        color: #6c757d;
        margin: 0;
        font-weight: 500;
    }
    
    /* Quick Actions */
    .quick-actions {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    
    .action-btn {
        padding: 15px;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s;
        text-decoration: none;
        color: #495057;
        display: block;
        margin-bottom: 10px;
    }
    
    .action-btn:hover {
        border-color: #3498db;
        background: rgba(52, 152, 219, 0.05);
        transform: translateY(-2px);
        color: #3498db;
        text-decoration: none;
    }
    
    .action-btn i {
        font-size: 1.5rem;
        margin-bottom: 8px;
        display: block;
    }
    
    /* Recent Orders */
    .recent-orders {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .order-item {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .order-item:last-child {
        border-bottom: none;
    }
    
    .order-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .status-pending { background: #fff3cd; color: #856404; }
    .status-accepted { background: #d1ecf1; color: #0c5460; }
    .status-shipped { background: #d1ecf1; color: #0c5460; }
    .status-delivered { background: #d4edda; color: #155724; }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .main-content {
            margin-left: 70px; /* Adjust for mobile view */
        }
        
        .sidebar {
            width: 70px; /* Sidebar width */
        }
    }
</style>
</head>
<body>

<!-- Include Sidebar -->
<?php include 'buyer_sidebar.php'; ?>

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Welcome, <?= htmlspecialchars($buyer_name) ?>!</h2>
            <p class="text-muted mb-0">Manage your auto parts purchases and orders</p>
        </div>
        <div class="text-end">
            <small class="text-muted"><?= date('l, F j, Y') ?></small>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6">
            <div class="stat-card orders">
                <i class="fas fa-shopping-bag text-danger"></i>
                <h4 class="text-danger"><?= $total_orders ?></h4>
                <p>Total Orders</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card pending">
                <i class="fas fa-clock text-warning"></i>
                <h4 class="text-warning"><?= $pending_orders ?></h4>
                <p>Pending Orders</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card delivered">
                <i class="fas fa-check-circle text-success"></i>
                <h4 class="text-success"><?= $delivered_orders ?></h4>
                <p>Delivered Orders</p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card cart">
                <i class="fas fa-shopping-cart text-primary"></i>
                <h4 class="text-primary"><?= $cart_items ?></h4>
                <p>Cart Items</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-md-8">
            <div class="quick-actions">
                <h4 class="mb-4"><i class="fas fa-bolt text-warning"></i> Quick Actions</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <a href="browse_products.php" class="action-btn text-center">
                            <i class="fas fa-search text-primary"></i>
                            <strong>Browse Parts</strong>
                            <small class="d-block text-muted">Find auto parts you need</small>
                        </a>
                    </div>

                    <div class="col-md-4 mb-3">
                        <a href="view_cart.php" class="action-btn text-center">
                            <i class="fas fa-shopping-cart text-info"></i>
                            <strong>View Cart</strong>
                            <small class="d-block text-muted"><?= $cart_items ?> items in cart</small>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="my_orders.php" class="action-btn text-center">
                            <i class="fas fa-list text-warning"></i>
                            <strong>My Orders</strong>
                            <small class="d-block text-muted">Track your orders</small>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="feedback.php" class="action-btn text-center">
                            <i class="fas fa-comments text-secondary"></i>
                            <strong>Give Feedback</strong>
                            <small class="d-block text-muted">Share your experience</small>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="buyer_update_profile.php" class="action-btn text-center">
                            <i class="fas fa-user text-dark"></i>
                            <strong>My Profile</strong>
                            <small class="d-block text-muted">Update your information</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders & Quick Links -->
        <div class="col-md-4">
            <!-- Recent Orders -->
            <div class="recent-orders mb-4">
                <h5 class="mb-3"><i class="fas fa-history text-primary"></i> Recent Orders</h5>
                <?php
                $recent_orders = $conn->query("
                    SELECT order_id, total_amount, status, created_at 
                    FROM orders 
                    WHERE user_id = $buyer_id 
                    ORDER BY created_at DESC 
                    LIMIT 3
                ");
                
                if ($recent_orders->num_rows > 0):
                    while ($order = $recent_orders->fetch_assoc()):
                ?>
                    <div class="order-item">
                        <div>
                            <strong>Order #<?= $order['order_id'] ?></strong>
                            <p class="mb-1 text-muted">₹<?= number_format($order['total_amount'], 2) ?></p>
                            <small class="text-muted"><?= date('M j, Y', strtotime($order['created_at'])) ?></small>
                        </div>
                        <span class="order-status status-<?= strtolower($order['status']) ?>">
                            <?= $order['status'] ?>
                        </span>
                    </div>
                <?php 
                    endwhile;
                else:
                ?>
                    <div class="text-center text-muted py-3">
                        <i class="fas fa-shopping-bag fa-2x mb-2"></i>
                        <p>No orders yet</p>
                    </div>
                <?php endif; ?>
                
                <div class="text-center mt-3">
                    <a href="my_orders.php" class="btn btn-sm btn-outline-primary">View All Orders</a>
                </div>
            </div>

            <!-- Quick Support -->
            <div class="quick-actions">
                <h5 class="mb-3"><i class="fas fa-headset text-success"></i> Need Help?</h5>
                <div class="d-grid gap-2">
                    <a href="contact.php" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-envelope"></i> Contact Support
                    </a>
                    <a href="faq.php" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-question-circle"></i> FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Add active class to current page in sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const currentPage = window.location.pathname.split('/').pop();
        const navLinks = document.querySelectorAll('.nav-link');
        
        navLinks.forEach(link => {
            if (link.getAttribute('href') === currentPage) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    });
</script>
</body>
</html>