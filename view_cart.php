<?php
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Update quantities
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantity'])) {
    foreach ($_POST['quantity'] as $cart_id => $qty) {
        $qty = max(1, intval($qty));
        $stmt = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=? AND user_id=?");
        $stmt->bind_param("iii", $qty, $cart_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Remove single item
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch cart items
$sql = "SELECT c.cart_id, c.quantity, p.part_id, p.part_name, p.price, p.image_url
        FROM cart c
        JOIN auto_parts p ON c.part_id = p.part_id
        WHERE c.user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate grand total
$grand_total = 0;
$cart_items = [];
while ($item = $result->fetch_assoc()) {
    $item['total'] = $item['price'] * $item['quantity'];
    $grand_total += $item['total'];
    $cart_items[] = $item;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Your Shopping Cart</h2>

<form method="POST">
<table class="table table-bordered align-middle">
    <thead>
        <tr>
            <th>Image</th>
            <th>Part Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($cart_items)): ?>
            <tr>
                <td colspan="6" class="text-center">Your cart is empty.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($cart_items as $item): ?>
            <tr>
                <td>
                    <?php if (!empty($item['image_url'])): ?>
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" width="60" alt="">
                    <?php else: ?>
                        <img src="uploads/default.jpg" width="60" alt="No Image">
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($item['part_name']) ?></td>
                <td><?= number_format($item['price'],2) ?></td>
                <td>
                    <input type="number" name="quantity[<?= $item['cart_id'] ?>]" value="<?= $item['quantity'] ?>" min="1" class="form-control" style="width:70px;">
                </td>
                <td><?= number_format($item['total'],2) ?></td>
                <td>
                    <a href="?remove=<?= $item['cart_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Remove this item?')">Remove</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($cart_items)): ?>
        <tr>
            <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
            <td colspan="2"><strong><?= number_format($grand_total,2) ?></strong></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($cart_items)): ?>
<div class="d-flex justify-content-end mt-3">
    <button type="submit" class="btn btn-primary me-2">Update Cart</button>
    <a href="cancel_cart.php" class="btn btn-danger me-2" onclick="return confirm('Are you sure you want to cancel all items in your cart?')">Cancel Cart</a>
    <a href="checkout.php" class="btn btn-success">Proceed to Checkout</a>
</div>
<?php endif; ?>

<a href="browse_products.php" class="btn btn-secondary mt-3">Continue Shopping</a>
</form>

</body>
</html>
