<?php
include "config.php";
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Get report parameters with defaults
$report_type = $_GET['report_type'] ?? 'daily';
$date = $_GET['date'] ?? date('Y-m-d');
$month = $_GET['month'] ?? date('Y-m');
$year = $_GET['year'] ?? date('Y');
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

$report_data = [];
$report_title = '';

// Generate reports based on type
switch ($report_type) {
    case 'daily':
        $report_title = "Daily Sales Report - " . $date;
        $stmt = $conn->prepare("
            SELECT 
                DATE(o.created_at) as sale_date,
                COUNT(DISTINCT o.order_id) as total_orders,
                SUM(oi.quantity) as total_parts_sold,
                SUM(oi.quantity * oi.price) as total_revenue
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            WHERE o.status = 'Delivered' AND DATE(o.created_at) = ?
            GROUP BY DATE(o.created_at)
        ");
        $stmt->bind_param("s", $date);
        break;
        
    case 'monthly':
        $report_title = "Monthly Sales Report - " . $month;
        $month_year = explode('-', $month);
        $stmt = $conn->prepare("
            SELECT 
                YEAR(o.created_at) as year,
                MONTH(o.created_at) as month,
                COUNT(DISTINCT o.order_id) as total_orders,
                SUM(oi.quantity) as total_parts_sold,
                SUM(oi.quantity * oi.price) as total_revenue
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            WHERE o.status = 'Delivered' AND YEAR(o.created_at) = ? AND MONTH(o.created_at) = ?
            GROUP BY YEAR(o.created_at), MONTH(o.created_at)
        ");
        $stmt->bind_param("ii", $month_year[0], $month_year[1]);
        break;
        
    case 'yearly':
        $report_title = "Yearly Sales Report - " . $year;
        $stmt = $conn->prepare("
            SELECT 
                YEAR(o.created_at) as year,
                COUNT(DISTINCT o.order_id) as total_orders,
                SUM(oi.quantity) as total_parts_sold,
                SUM(oi.quantity * oi.price) as total_revenue
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            WHERE o.status = 'Delivered' AND YEAR(o.created_at) = ?
            GROUP BY YEAR(o.created_at)
        ");
        $stmt->bind_param("i", $year);
        break;
        
    case 'custom':
        $report_title = "Custom Range Report: " . $start_date . " to " . $end_date;
        $stmt = $conn->prepare("
            SELECT 
                DATE(o.created_at) as sale_date,
                COUNT(DISTINCT o.order_id) as total_orders,
                SUM(oi.quantity) as total_parts_sold,
                SUM(oi.quantity * oi.price) as total_revenue
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            WHERE o.status = 'Delivered' AND DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY DATE(o.created_at)
            ORDER BY sale_date
        ");
        $stmt->bind_param("ss", $start_date, $end_date);
        break;
        
    case 'inventory':
        $report_title = "Inventory Report";
        $stmt = $conn->prepare("
            SELECT 
                part_id, part_name, description, stock, price,
                CASE 
                    WHEN stock = 0 THEN 'Out of Stock'
                    WHEN stock <= 5 THEN 'Low Stock'
                    ELSE 'In Stock'
                END as stock_status
            FROM auto_parts
            ORDER BY 
                CASE 
                    WHEN stock = 0 THEN 1
                    WHEN stock <= 5 THEN 2
                    ELSE 3
                END, part_name
        ");
        break;
        
    case 'order_details':
        $report_title = "Order Details Report: " . $start_date . " to " . $end_date;
        $stmt = $conn->prepare("
            SELECT 
                o.order_id, o.full_name, o.phone, o.total_amount, 
                o.status, o.created_at, u.username,
                COUNT(oi.item_id) as item_count,
                SUM(oi.quantity) as total_quantity
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN order_items oi ON o.order_id = oi.order_id
            WHERE DATE(o.created_at) BETWEEN ? AND ?
            GROUP BY o.order_id
            ORDER BY o.created_at DESC
        ");
        $stmt->bind_param("ss", $start_date, $end_date);
        break;
        
    default:
        $report_type = 'daily';
        $report_title = "Daily Sales Report - " . $date;
        $stmt = $conn->prepare("
            SELECT 
                DATE(o.created_at) as sale_date,
                COUNT(DISTINCT o.order_id) as total_orders,
                SUM(oi.quantity) as total_parts_sold,
                SUM(oi.quantity * oi.price) as total_revenue
            FROM orders o
            JOIN order_items oi ON o.order_id = oi.order_id
            WHERE o.status = 'Delivered' AND DATE(o.created_at) = ?
            GROUP BY DATE(o.created_at)
        ");
        $stmt->bind_param("s", $date);
}

// Execute query
if (in_array($report_type, ['inventory'])) {
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $stmt->execute();
    $result = $stmt->get_result();
}

while ($row = $result->fetch_assoc()) {
    $report_data[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Reports - PartsLo.pk</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .report-card { 
            background: #f8f9fa; 
            border-left: 4px solid #007bff; 
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
        .sidebar { background-color: #f8f9fa; height: 100vh; position: sticky; top: 0; }
        .nav-pills .nav-link.active { background-color: #dc3545; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <h5 class="mb-4"><i class="fas fa-chart-bar"></i> Reports</h5>
                <ul class="nav nav-pills flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?= $report_type == 'daily' ? 'active' : '' ?>" 
                           href="?report_type=daily&date=<?= date('Y-m-d') ?>">
                           <i class="fas fa-calendar-day"></i> Daily
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $report_type == 'monthly' ? 'active' : '' ?>" 
                           href="?report_type=monthly&month=<?= date('Y-m') ?>">
                           <i class="fas fa-calendar-alt"></i> Monthly
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $report_type == 'yearly' ? 'active' : '' ?>" 
                           href="?report_type=yearly&year=<?= date('Y') ?>">
                           <i class="fas fa-calendar"></i> Yearly
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $report_type == 'custom' ? 'active' : '' ?>" 
                           href="?report_type=custom&start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-d') ?>">
                           <i class="fas fa-calendar-week"></i> Custom Range
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $report_type == 'inventory' ? 'active' : '' ?>" 
                           href="?report_type=inventory">
                           <i class="fas fa-boxes"></i> Inventory
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $report_type == 'order_details' ? 'active' : '' ?>" 
                           href="?report_type=order_details&start_date=<?= date('Y-m-01') ?>&end_date=<?= date('Y-m-d') ?>">
                           <i class="fas fa-list"></i> Order Details
                        </a>
                    </li>
                </ul>
                
                <hr>
                
                <!-- Filters -->
                <form method="GET" class="mt-3">
                    <input type="hidden" name="report_type" value="<?= $report_type ?>">
                    
                    <?php if ($report_type == 'daily'): ?>
                        <div class="mb-3">
                            <label class="form-label">Date:</label>
                            <input type="date" name="date" value="<?= $date ?>" class="form-control" max="<?= date('Y-m-d') ?>">
                        </div>
                    <?php elseif ($report_type == 'monthly'): ?>
                        <div class="mb-3">
                            <label class="form-label">Month:</label>
                            <input type="month" name="month" value="<?= $month ?>" class="form-control" max="<?= date('Y-m') ?>">
                        </div>
                    <?php elseif ($report_type == 'yearly'): ?>
                        <div class="mb-3">
                            <label class="form-label">Year:</label>
                            <input type="number" name="year" value="<?= $year ?>" class="form-control" min="2020" max="<?= date('Y') ?>">
                        </div>
                    <?php elseif (in_array($report_type, ['custom', 'order_details'])): ?>
                        <div class="mb-3">
                            <label class="form-label">Start Date:</label>
                            <input type="date" name="start_date" value="<?= $start_date ?>" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">End Date:</label>
                            <input type="date" name="end_date" value="<?= $end_date ?>" class="form-control">
                        </div>
                    <?php endif; ?>
                    
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="fas fa-sync-alt"></i> Generate
                    </button>
                </form>
                
                <hr>
                <a href="admin_dashboard.php" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
            
            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><?= $report_title ?></h2>
                    <div>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print
                        </button>
                    </div>
                </div>

                <!-- Sales Reports -->
                <?php if (in_array($report_type, ['daily', 'monthly', 'yearly', 'custom']) && !empty($report_data)): ?>
                    <?php foreach ($report_data as $data): ?>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="report-card">
                                    <h6>Total Orders</h6>
                                    <h3 class="text-primary"><?= $data['total_orders'] ?? 0 ?></h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="report-card">
                                    <h6>Parts Sold</h6>
                                    <h3 class="text-success"><?= $data['total_parts_sold'] ?? 0 ?></h3>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="report-card">
                                    <h6>Total Revenue</h6>
                                    <h3 class="text-danger">₹<?= number_format($data['total_revenue'] ?? 0, 2) ?></h3>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php elseif (in_array($report_type, ['daily', 'monthly', 'yearly', 'custom']) && empty($report_data)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> No sales data found for selected period.
                    </div>
                <?php endif; ?>

                <!-- Inventory Report -->
                <?php if ($report_type == 'inventory'): ?>
                    <?php
                    $low_stock_count = array_filter($report_data, function($item) {
                        return $item['stock_status'] != 'In Stock';
                    });
                    if (count($low_stock_count) > 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i> 
                            <?= count($low_stock_count) ?> items need attention
                        </div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Part ID</th>
                                    <th>Part Name</th>
                                    <th>Stock</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data as $item): ?>
                                    <tr class="<?= $item['stock_status'] == 'Low Stock' ? 'table-warning' : ($item['stock_status'] == 'Out of Stock' ? 'table-danger' : '') ?>">
                                        <td><?= $item['part_id'] ?></td>
                                        <td><?= htmlspecialchars($item['part_name']) ?></td>
                                        <td><strong><?= $item['stock'] ?></strong></td>
                                        <td>₹<?= number_format($item['price'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $item['stock_status'] == 'Out of Stock' ? 'danger' : 
                                                ($item['stock_status'] == 'Low Stock' ? 'warning' : 'success')
                                            ?>">
                                                <?= $item['stock_status'] ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <!-- Order Details Report -->
                <?php if ($report_type == 'order_details'): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report_data as $order): ?>
                                    <tr>
                                        <td>#<?= $order['order_id'] ?></td>
                                        <td><?= htmlspecialchars($order['full_name']) ?></td>
                                        <td><strong>₹<?= number_format($order['total_amount'], 2) ?></strong></td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $order['status'] == 'Delivered' ? 'success' : 
                                                ($order['status'] == 'Pending' ? 'warning' : 
                                                ($order['status'] == 'Rejected' ? 'danger' : 'primary'))
                                            ?>">
                                                <?= $order['status'] ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>