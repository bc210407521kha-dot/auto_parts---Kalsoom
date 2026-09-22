<?php
include "config.php";
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get quick stats for dashboard
$today_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['count'];
$pending_orders = $conn->query("SELECT COUNT(*) as count FROM orders WHERE status = 'Pending'")->fetch_assoc()['count'];
$low_stock = $conn->query("SELECT COUNT(*) as count FROM auto_parts WHERE stock <= 5")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT COALESCE(SUM(total_amount), 0) as revenue FROM orders WHERE status = 'Delivered' AND MONTH(created_at) = MONTH(CURDATE())")->fetch_assoc()['revenue'];
$total_parts = $conn->query("SELECT COUNT(*) as count FROM auto_parts")->fetch_assoc()['count'];

$admin_username = $_SESSION['admin_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Dashboard - PartsLo.pk</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    :root {
        --sidebar-width: 250px;
        --sidebar-bg: #2c3e50;
        --sidebar-color: #ecf0f1;
        --sidebar-active: #3498db;
        --header-bg: #dc3545;
    }
    
    body {
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
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
        box-shadow: 3px 0 10px rgba(0,0,0,0.1);
    }
    
    .sidebar-header {
        padding: 20px;
        background: rgba(0,0,0,0.2);
        border-bottom: 1px solid rgba(255,255,255,0.1);
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
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .nav-link:hover {
        background: rgba(255,255,255,0.1);
        color: white;
        transform: translateX(5px);
    }
    
    .nav-link.active {
        background: var(--sidebar-active);
        color: white;
        box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
    }
    
    .nav-link i {
        width: 20px;
        text-align: center;
    }
    
    /* Main Content */
    .main-content {
        margin-left: var(--sidebar-width);
        padding: 20px;
        transition: all 0.3s;
    }
    
    /* Stats Cards */
    .stat-card {
        background: white;
        border-radius: 15px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        border: none;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
    }
    
    .stat-card.orders { border-left: 4px solid #dc3545; }
    .stat-card.pending { border-left: 4px solid #ffc107; }
    .stat-card.stock { border-left: 4px solid #fd7e14; }
    .stat-card.revenue { border-left: 4px solid #198754; }
    .stat-card.parts { border-left: 4px solid #6f42c1; }
    
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
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
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
        border-color: var(--sidebar-active);
        background: rgba(52, 152, 219, 0.05);
        transform: translateY(-2px);
        color: var(--sidebar-active);
        text-decoration: none;
    }
    
    .action-btn i {
        font-size: 1.5rem;
        margin-bottom: 8px;
        display: block;
    }
    
    /* Recent Activity */
    .recent-activity {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    
    .activity-item {
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .activity-item:last-child {
        border-bottom: none;
    }
    
    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .activity-icon.order { background: #dc3545; }
    .activity-icon.stock { background: #fd7e14; }
    .activity-icon.revenue { background: #198754; }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 70px;
        }
        
        .sidebar .nav-link span {
            display: none;
        }
        
        .sidebar-header h4, .sidebar-header p {
            display: none;
        }
        
        .main-content {
            margin-left: 70px;
        }
        
        .nav-link {
            justify-content: center;
            padding: 15px;
        }
        
        .nav-link i {
            margin: 0;
        }
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <div class="sidebar-header">
        <h4><i class="fas fa-car"></i> PartsLo.pk</h4>
        <p>Admin Dashboard</p>
    </div>
    
    <div class="sidebar-menu">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="admin_dashboard.php">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="list_parts.php">
                    <i class="fas fa-cog"></i>
                    <span>Auto Parts</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_orders.php">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Orders</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="list_category.php">
                    <i class="fas fa-tags"></i>
                    <span>Categories</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="list_subcategories.php">
                    <i class="fas fa-tag"></i>
                    <span>Subcategories</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_report.php">
                    <i class="fas fa-chart-bar"></i>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="admin_feedback.php">
                    <i class="fas fa-comments"></i>
                    <span>Feedback</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="change_password.php">
                    <i class="fas fa-cogs"></i>
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

<!-- Main Content -->
<div class="main-content">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Welcome back, <?= htmlspecialchars($admin_username) ?>!</h2>
            <p class="text-muted mb-0">Here's what's happening with your store today.</p>
        </div>
        <div class="text-end">
            <small class="text-muted"><?= date('l, F j, Y') ?></small>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-2 col-6">
            <div class="stat-card orders">
                <i class="fas fa-shopping-cart text-danger"></i>
                <h4 class="text-danger"><?= $today_orders ?></h4>
                <p>Today's Orders</p>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="stat-card pending">
                <i class="fas fa-clock text-warning"></i>
                <h4 class="text-warning"><?= $pending_orders ?></h4>
                <p>Pending Orders</p>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="stat-card stock">
                <i class="fas fa-exclamation-triangle text-warning"></i>
                <h4 class="text-warning"><?= $low_stock ?></h4>
                <p>Low Stock Items</p>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="stat-card revenue">
                <i class="fas fa-chart-line text-success"></i>
                <h4 class="text-success">₹<?= number_format($total_revenue, 2) ?></h4>
                <p>Monthly Revenue</p>
            </div>
        </div>
        <div class="col-md-2 col-6">
            <div class="stat-card parts">
                <i class="fas fa-box text-primary"></i>
                <h4 class="text-primary"><?= $total_parts ?></h4>
                <p>Total Parts</p>
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
                        <a href="list_parts.php" class="action-btn text-center">
                            <i class="fas fa-plus-circle text-primary"></i>
                            <strong>Add New Part</strong>
                            <small class="d-block text-muted">Add new auto part to inventory</small>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="admin_orders.php" class="action-btn text-center">
                            <i class="fas fa-edit text-success"></i>
                            <strong>Manage Orders</strong>
                            <small class="d-block text-muted">Process pending orders</small>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="admin_report.php" class="action-btn text-center">
                            <i class="fas fa-chart-bar text-info"></i>
                            <strong>View Reports</strong>
                            <small class="d-block text-muted">Sales & inventory reports</small>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="list_category.php" class="action-btn text-center">
                            <i class="fas fa-tags text-warning"></i>
                            <strong>Categories</strong>
                            <small class="d-block text-muted">Manage categories</small>
                        </a>
                    </div>
                    <div class="col-md-4 mb-3">
                        <a href="admin_feedback.php" class="action-btn text-center">
                            <i class="fas fa-comments text-secondary"></i>
                            <strong>Customer Feedback</strong>
                            <small class="d-block text-muted">View customer reviews</small>
                        </a>
                    </div>
                
                </div>
            </div>
        </div>

        <!-- Quick Reports & Recent Activity -->
        <div class="col-md-4">
            <!-- Quick Reports -->
            <div class="quick-actions mb-4">
                <h5 class="mb-3"><i class="fas fa-chart-pie text-info"></i> Quick Reports</h5>
                <div class="d-grid gap-2">
                    <a href="admin_report.php?report_type=daily" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-calendar-day"></i> Today's Report
                    </a>
                    <a href="admin_report.php?report_type=inventory" class="btn btn-outline-warning btn-sm">
                        <i class="fas fa-boxes"></i> Inventory Status
                    </a>
                    <a href="admin_report.php?report_type=order_details&start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-d') ?>" 
                       class="btn btn-outline-info btn-sm">
                        <i class="fas fa-file-invoice"></i> Monthly Orders
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="recent-activity">
                <h5 class="mb-3"><i class="fas fa-history text-primary"></i> Recent Activity</h5>
                <div class="activity-item">
                    <div class="activity-icon order">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div>
                        <strong>New Orders</strong>
                        <p class="mb-0 text-muted"><?= $today_orders ?> orders today</p>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon stock">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div>
                        <strong>Stock Alert</strong>
                        <p class="mb-0 text-muted"><?= $low_stock ?> items low in stock</p>
                    </div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon revenue">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div>
                        <strong>Revenue</strong>
                        <p class="mb-0 text-muted">₹<?= number_format($total_revenue, 2) ?> this month</p>
                    </div>
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