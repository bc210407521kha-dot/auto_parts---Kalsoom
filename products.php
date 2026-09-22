<?php
include "config.php";

// Get subcategory ID from URL
$subcategory_id = isset($_GET['subcategory']) ? intval($_GET['subcategory']) : 0;

// Fetch subcategory details
if ($subcategory_id > 0) {
    $subcategory_stmt = $conn->prepare("
        SELECT s.*, c.category_name 
        FROM subcategories s 
        JOIN categories c ON s.category_id = c.category_id 
        WHERE s.subcategory_id = ?
    ");
    $subcategory_stmt->bind_param("i", $subcategory_id);
    $subcategory_stmt->execute();
    $subcategory_result = $subcategory_stmt->get_result();
    $subcategory = $subcategory_result->fetch_assoc();
    
    if (!$subcategory) {
        header("Location: index.php");
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

// Fetch products for this subcategory
$products_stmt = $conn->prepare("
    SELECT p.*, s.subcategory_name, c.category_name 
    FROM auto_parts p 
    LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id 
    LEFT JOIN categories c ON s.category_id = c.category_id 
    WHERE p.subcategory_id = ? AND p.stock > 0 
    ORDER BY p.created_at DESC
");
$products_stmt->bind_param("i", $subcategory_id);
$products_stmt->execute();
$products_result = $products_stmt->get_result();

// Fetch categories for sidebar
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
$subcategories_result = $conn->query("SELECT s.*, c.category_name FROM subcategories s JOIN categories c ON s.category_id = c.category_id ORDER BY c.category_name, s.subcategory_name");

// Cart count
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $cart_result = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id = $user_id");
    $cart_data = $cart_result->fetch_assoc();
    $cart_count = $cart_data['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($subcategory['subcategory_name']) ?> - PartsLo.pk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5efefff; }
        .category-sidebar {
            background:#fff; border:1px solid #ddd; padding:15px; height:100%;
        }
        .category-sidebar ul li { padding:6px 0; font-size:15px; }
        .category-sidebar ul li a:hover { color: #dc3545 !important; }
        .product-card img { height:180px; object-fit:cover; }
        .product-item { 
            background: white; 
            border: 1px solid #ddd; 
            border-radius: 8px; 
            padding: 15px; 
            margin-bottom: 15px; 
        }
        .product-item img { width: 80px; height: 80px; object-fit: cover; }
        .price-original { text-decoration: line-through; color: #999; }
        .price-sale { color: #dc3545; font-weight: bold; }
        .active-subcategory { color: #dc3545 !important; font-weight: bold; }
    </style>
</head>
<body>

<!-- Include Common Navbar -->
<?php include 'navbar.php'; ?>

<!-- Main Content -->
<div class="container my-4">
    <div class="row">

        <!-- LEFT: SIDEBAR -->
        <div class="col-md-3">
            <div class="category-sidebar">
                <h5 class="fw-bold mb-3">Product Categories</h5>
                
                <?php
                // Reset categories pointer
                $categories_result->data_seek(0);
                $subcategories_result->data_seek(0);
                
                $subcategories_by_category = [];
                while ($subcat = $subcategories_result->fetch_assoc()) {
                    $subcategories_by_category[$subcat['category_name']][] = $subcat;
                }
                
                $category_icons = [
                    'Motorbike' => '🏍️',
                    'Car' => '🚗',
                    'SUV' => '🚙'
                ];
                
                while ($category = $categories_result->fetch_assoc()): 
                    $category_name = $category['category_name'];
                    $icon = $category_icons[$category_name] ?? '🔧';
                ?>
                    <div class="mb-3">
                        <h6 class="text-danger fw-semibold mb-2"><?= $icon ?> <?= htmlspecialchars($category_name) ?> Parts</h6>
                        <ul class="list-unstyled ms-3">
                            <?php if (isset($subcategories_by_category[$category_name])): ?>
                                <?php foreach ($subcategories_by_category[$category_name] as $subcat): ?>
                                    <li>
                                        <a href="products.php?subcategory=<?= $subcat['subcategory_id'] ?>" 
                                           class="text-decoration-none text-dark <?= $subcat['subcategory_id'] == $subcategory_id ? 'active-subcategory' : '' ?>">
                                            <?= htmlspecialchars($subcat['subcategory_name']) ?>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- RIGHT: PRODUCTS -->
        <div class="col-md-9">
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="#"><?= htmlspecialchars($subcategory['category_name']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($subcategory['subcategory_name']) ?></li>
                </ol>
            </nav>

            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><?= htmlspecialchars($subcategory['subcategory_name']) ?></h2>
                    <p class="text-muted mb-0"><?= htmlspecialchars($subcategory['category_name']) ?> Parts</p>
                </div>
                <div class="text-muted">
                    <?= $products_result->num_rows ?> product(s) found
                </div>
            </div>

            <!-- Products Grid -->
            <?php if ($products_result->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while ($product = $products_result->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card product-card shadow-sm h-100">
                                <img src="<?= htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/300x180?text=No+Image') ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($product['part_name']) ?>">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="fw-semibold"><?= htmlspecialchars($product['part_name']) ?></h6>
                                    <p class="text-muted mb-2">₹<?= number_format($product['price'], 2) ?></p>
                                    
                                    <?php if ($product['description']): ?>
                                        <p class="text-muted small flex-grow-1">
                                            <?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...
                                        </p>
                                    <?php else: ?>
                                        <div class="flex-grow-1"></div>
                                    <?php endif; ?>
                                    
                                    <div class="mt-auto">
                                        <div class="mb-2">
                                            <?php if ($product['stock'] > 10): ?>
                                                <span class="badge bg-success">In Stock</span>
                                            <?php elseif ($product['stock'] > 0): ?>
                                                <span class="badge bg-warning">Only <?= $product['stock'] ?> left</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Out of Stock</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <a href="product_details.php?id=<?= $product['part_id'] ?>" 
                                               class="btn btn-outline-dark">View Details</a>
                                            
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'buyer' && $product['stock'] > 0): ?>
                                                <form method="POST" action="add_to_cart.php" class="d-inline">
                                                    <input type="hidden" name="part_id" value="<?= $product['part_id'] ?>">
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button type="submit" class="btn btn-danger w-100">Add to Cart</button>
                                                </form>
                                            <?php elseif (!isset($_SESSION['user_id']) && $product['stock'] > 0): ?>
                                                <a href="login.php" class="btn btn-danger w-100">Add to Cart</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-box-open fa-3x text-muted"></i>
                    </div>
                    <h4 class="text-muted">No Products Found</h4>
                    <p class="text-muted">There are no products available in this category at the moment.</p>
                    <a href="index.php" class="btn btn-danger">Back to Home</a>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">© 2025 PartsLo.pk</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>