<?php
include "config.php";

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$part_id = intval($_GET['id']);
$product = $conn->query("
    SELECT p.*, s.subcategory_name, c.category_name 
    FROM auto_parts p 
    LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id 
    LEFT JOIN categories c ON s.category_id = c.category_id 
    WHERE p.part_id = $part_id
")->fetch_assoc();

if (!$product) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['part_name']) ?> - PartsLo.pk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6">
                <img src="<?= htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/500x400?text=No+Image') ?>" 
                     class="img-fluid rounded" alt="<?= htmlspecialchars($product['part_name']) ?>">
            </div>
            <div class="col-md-6">
                <h1><?= htmlspecialchars($product['part_name']) ?></h1>
                <h3 class="text-danger">₹<?= number_format($product['price'], 2) ?></h3>
                <p><strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                <p><strong>Subcategory:</strong> <?= htmlspecialchars($product['subcategory_name']) ?></p>
                <p><strong>Stock:</strong> 
                    <?php if ($product['stock'] > 0): ?>
                        <span class="text-success">In Stock (<?= $product['stock'] ?> available)</span>
                    <?php else: ?>
                        <span class="text-danger">Out of Stock</span>
                    <?php endif; ?>
                </p>
                <?php if ($product['description']): ?>
                    <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'buyer' && $product['stock'] > 0): ?>
                    <form method="POST" action="add_to_cart.php" class="mt-4">
                        <input type="hidden" name="part_id" value="<?= $product['part_id'] ?>">
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product['stock'] ?>" class="form-control" style="width: 100px;">
                        </div>
                        <button type="submit" class="btn btn-danger btn-lg">Add to Cart</button>
                    </form>
                <?php elseif (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="btn btn-danger btn-lg mt-4">Login to Purchase</a>
                <?php endif; ?>
                
                <a href="index.php" class="btn btn-outline-secondary mt-3">← Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>