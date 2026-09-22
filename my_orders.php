<?php
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Cancel order
if (isset($_GET['cancel'])) {
    $order_id = intval($_GET['cancel']);
    $conn->query("DELETE FROM orders WHERE order_id=$order_id AND user_id=$user_id AND status='Pending'");
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['feedback'])) {
    $order_id = intval($_POST['order_id']);
    $comment = $_POST['comment'];
    $rating = intval($_POST['rating']);
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, order_id, comment, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $user_id, $order_id, $comment, $rating);
    $stmt->execute();
    $stmt->close();
}

// Fetch orders
$orders_res = $conn->query("SELECT * FROM orders WHERE user_id=$user_id ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>My Orders</h2>

<?php while ($order = $orders_res->fetch_assoc()): ?>
    <div class="card mb-3">
        <div class="card-header">
            Order #<?= $order['order_id'] ?> | Status: <?= $order['status'] ?> | Total: <?= number_format($order['total_amount'], 2) ?>
            <?php if ($order['status'] === 'Pending'): ?>
                <a href="?cancel=<?= $order['order_id'] ?>" class="btn btn-danger btn-sm float-end" onclick="return confirm('Cancel this order?')">Cancel Order</a>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <p><strong>Name:</strong> <?= htmlspecialchars($order['full_name']) ?><br>
               <strong>Phone:</strong> <?= htmlspecialchars($order['phone']) ?><br>
               <strong>Address:</strong> <?= htmlspecialchars($order['address']) ?></p>

            <h5>Items:</h5>
            <ul>
            <?php
            $items_res = $conn->query("SELECT oi.quantity, p.part_name, oi.price FROM order_items oi JOIN auto_parts p ON oi.part_id=p.part_id WHERE oi.order_id=".$order['order_id']);
            while ($item = $items_res->fetch_assoc()) {
                echo "<li>{$item['part_name']} - Qty: {$item['quantity']} - Price: ".number_format($item['price'],2)."</li>";
            }
            ?>
            </ul>

            <?php
            // Show feedback form only if delivered
            if ($order['status'] === 'Delivered') {
                // Check if feedback already given
                $fb_res = $conn->query("SELECT * FROM feedback WHERE order_id={$order['order_id']} AND user_id=$user_id");
                if ($fb_res->num_rows === 0):
            ?>
                <form method="POST" class="mt-3">
                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                    <div class="mb-2">
                        <textarea name="comment" class="form-control" placeholder="Write your feedback" required></textarea>
                    </div>
                    <div class="mb-2">
                        <label>Rating:</label>
                        <select name="rating" class="form-select" required>
                            <option value="">Select</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" name="feedback">Submit Feedback</button>
                </form>
            <?php 
                else:
                    $fb = $fb_res->fetch_assoc();
                    echo "<p><strong>Your Feedback:</strong> {$fb['comment']} | Rating: {$fb['rating']}</p>";
                endif;
            } 
            ?>
        </div>
    </div>
<?php endwhile; ?>

</body>
</html>
