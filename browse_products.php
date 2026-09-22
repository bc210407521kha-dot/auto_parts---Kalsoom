<?php
include "config.php";

$user_id = $_SESSION['user_id'] ?? 0;

// Fetch categories
$categories_res = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $categories_res->fetch_all(MYSQLI_ASSOC);

// Selected filters
$category_id = intval($_GET['category_id'] ?? 0);
$subcategory_id = intval($_GET['subcategory_id'] ?? 0);
$search = trim($_GET['search'] ?? '');

// Build WHERE clause
$where = [];
if ($search) $where[] = "p.part_name LIKE '%" . $conn->real_escape_string($search) . "%'";
if ($category_id) $where[] = "c.category_id = $category_id";
if ($subcategory_id) $where[] = "s.subcategory_id = $subcategory_id";
$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Fetch parts
$sql = "SELECT p.part_id, p.part_name, p.description, p.price, p.stock, p.image_url,
               s.subcategory_name, c.category_name
        FROM auto_parts p
        JOIN subcategories s ON p.subcategory_id = s.subcategory_id
        JOIN categories c ON s.category_id = c.category_id
        $where_sql
        ORDER BY p.part_id DESC";
$result = $conn->query($sql);

// Fetch subcategories
$subcategories = [];
if ($category_id) {
    $sub_res = $conn->query("SELECT * FROM subcategories WHERE category_id=$category_id ORDER BY subcategory_name ASC");
    $subcategories = $sub_res->fetch_all(MYSQLI_ASSOC);
}

// Fetch cart count
$cart_count = 0;
if ($user_id) {
    $res = $conn->query("SELECT SUM(quantity) as cnt FROM cart WHERE user_id=$user_id");
    $row = $res->fetch_assoc();
    $cart_count = $row['cnt'] ?? 0;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Browse Auto Parts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<!-- Navbar with cart -->
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Auto Parts Store</a>
        <div class="d-flex">
            <a href="view_cart.php" class="btn btn-outline-primary">
                Cart (<?= $cart_count ?>)
            </a>
            <?php if ($cart_count > 0): ?>
                <a href="checkout.php" class="btn btn-success ms-2">Proceed to Checkout</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<h2>Browse Auto Parts</h2>

<!-- Filter Form -->
<form class="row g-3 mb-4" method="GET">
    <div class="col-md-3">
        <input type="text" name="search" class="form-control" placeholder="Search by part name" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
        <select name="category_id" class="form-select" id="categorySelect">
            <option value="">Select Category</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['category_id'] ?>" <?= $category_id == $cat['category_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['category_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <select name="subcategory_id" class="form-select" id="subcategorySelect">
            <option value="">Select Subcategory</option>
            <?php foreach ($subcategories as $sub): ?>
                <option value="<?= $sub['subcategory_id'] ?>" <?= $subcategory_id == $sub['subcategory_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sub['subcategory_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary w-100">Filter</button>
    </div>
</form>

<!-- Parts List -->
<div class="row">
    <?php while ($part = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <?php 
                // Correctly display image based on DB path
                $img_path = !empty($part['image_url']) ? $part['image_url'] : 'uploads/default.jpg';
                ?>
                <img src="<?= htmlspecialchars($img_path) ?>" class="card-img-top" alt="<?= htmlspecialchars($part['part_name']) ?>" style="height:200px; object-fit:cover;">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($part['part_name']) ?></h5>
                    <p class="card-text">
                        Category: <?= htmlspecialchars($part['category_name']) ?><br>
                        Subcategory: <?= htmlspecialchars($part['subcategory_name']) ?><br>
                        Price: <?= number_format($part['price'],2) ?><br>
                        Stock: <?= $part['stock'] ?>
                    </p>
                    <?php if ($part['stock'] > 0): ?>
                        <a href="add_to_cart.php?part_id=<?= $part['part_id'] ?>" class="btn btn-success">Add to Cart</a>
                    <?php else: ?>
                        <span class="badge bg-danger">Out of Stock</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Dynamic subcategories
    $('#categorySelect').on('change', function() {
        var category_id = $(this).val();
        $('#subcategorySelect').html('<option value="">Select Subcategory</option>');
        if (category_id) {
            $.getJSON('get_subcategories.php?category_id=' + category_id, function(data) {
                $.each(data, function(i, item) {
                    $('#subcategorySelect').append('<option value="'+item.subcategory_id+'">'+item.subcategory_name+'</option>');
                });
            });
        }
    });
</script>

</body>
</html>
