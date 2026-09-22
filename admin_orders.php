<?php
session_start();
include "config.php";

// Only admin access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Update order status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE order_id=?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all orders
$orders_res = $conn->query("SELECT o.*, u.username FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>All Orders</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Buyer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Placed At</th>
            <th>Update Status</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($order = $orders_res->fetch_assoc()): ?>
            <tr>
                <td><?= $order['order_id'] ?></td>
                <td><?= htmlspecialchars($order['username']) ?></td>
                <td><?= number_format($order['total_amount'],2) ?></td>
                <td><?= $order['status'] ?></td>
                <td><?= $order['created_at'] ?></td>
                <td>
                    <form method="POST" class="d-flex">
                        <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                        <select name="status" class="form-select me-2">
                            <?php
                            $statuses = ['Pending','Accepted','Rejected','Packed','Shipped','Out for delivery','Delivered'];
                            foreach ($statuses as $s) {
                                $selected = $order['status']==$s ? 'selected' : '';
                                echo "<option value='$s' $selected>$s</option>";
                            }
                            ?>
                        </select>
                        <button class="btn btn-primary btn-sm">Update</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
