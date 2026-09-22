<?php
include "config.php";

// Check if buyer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$sql = "SELECT c.cart_id, c.quantity, p.part_id, p.part_name, p.price, p.image_url
        FROM cart c
        JOIN auto_parts p ON c.part_id = p.part_id
        WHERE c.user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// If cart empty, redirect
if ($result->num_rows === 0) {
    header("Location: browse_products.php");
    exit();
}

// Calculate total
$total_amount = 0;
while ($item = $result->fetch_assoc()) {
    $total_amount += $item['price'] * $item['quantity'];
}
$result->data_seek(0); // Reset pointer for later
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Checkout</h2>

<form action="place_order.php" method="POST">
    <div class="mb-3">
        <label>Full Name</label>
        <input type="text" name="full_name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Phone</label>
        <input type="text" name="phone" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Address</label>
        <textarea name="address" class="form-control" required></textarea>
    </div>

    <h4>Order Summary</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Part Name</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($item = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($item['part_name']) ?></td>
                    <td><?= number_format($item['price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Grand Total:</strong></td>
                <td><strong><?= number_format($total_amount, 2) ?></strong></td>
            </tr>
        </tbody>
    </table>

    <input type="hidden" name="total_amount" value="<?= $total_amount ?>">
    <button type="submit" class="btn btn-success">Place Order (Cash on Delivery)</button>
    <a href="view_cart.php" class="btn btn-secondary">Back to Cart</a>
</form>

</body>
</html>
