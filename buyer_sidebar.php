<?php
if (!isset($cart_items)) {
    $cart_items = 0;
}
?>

<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-car"></i> PartsLo.pk</h4>
        <p>Buyer Dashboard</p>
    </div>

    <div class="sidebar-menu">
        <ul class="nav flex-column">

            <li class="nav-item">
                <a class="nav-link active" href="buyer_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="browse_products.php">
                    <i class="fas fa-search"></i>
                    <span>Browse Parts</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="view_cart.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Shopping Cart</span>

                    <?php if ($cart_items > 0): ?>
                        <span class="badge bg-danger ms-2"><?= $cart_items ?></span>
                    <?php endif; ?>

                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="my_orders.php">
                    <i class="fas fa-list"></i>
                    <span>My Orders</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="feedback.php">
                    <i class="fas fa-comments"></i>
                    <span>Feedback</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="buyer_update_profile.php">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link" href="change_password.php">
                    <i class="fas fa-key"></i>
                    <span>Change Password</span>
                </a>
            </li>

            <li class="nav-item mt-4">
                <a class="nav-link" href="logout.php" style="color: #e74c3c;">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>

        </ul>
    </div>
</div>

<style>
    :root {
        --sidebar-width: 250px;
        --sidebar-bg: #2c3e50;
        --sidebar-color: #ecf0f1;
        --sidebar-active: #3498db;
    }
    
    /* Sidebar Styles */
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: var(--sidebar-width);
        background: var(--sidebar-bg);
        color: var(--sidebar-color);
        transition: all 0.3s;
        z-index: 1000;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
    }
    
    .sidebar-header {
        padding: 20px;
        background: rgba(0, 0, 0, 0.2);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .sidebar-header h4 {
        margin: 0;
        color: white;
        font-weight: 600;
    }
    
    .sidebar-header p {
        margin: 5px 0 0 0;
        font-size: 0.9em;
        opacity: 0.8;
    }
    
    .sidebar-menu {
        padding: 20px 0;
        height: calc(100vh - 140px);
        overflow-y: auto;
    }
    
    .nav-link {
        color: var(--sidebar-color);
        padding: 12px 20px;
        margin: 2px 10px;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateX(5px);
    }
    
    .nav-link.active {
        background: var(--sidebar-active);
        color: white;
    }
    
    .nav-link i {
        width: 20px;
        text-align: center;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 70px;
        }
        
        .sidebar .nav-link span {
            display: none;
        }
        
        .sidebar-header h4,
        .sidebar-header p {
            display: none;
        }
    }
</style>