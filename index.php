<?php
include "config.php";

// Fetch categories and subcategories for sidebar
$categories_result = $conn->query("SELECT * FROM categories ORDER BY category_name");
$subcategories_result = $conn->query("SELECT s.*, c.category_name FROM subcategories s JOIN categories c ON s.category_id = c.category_id ORDER BY c.category_name, s.subcategory_name");

// Fetch recent 5 products for carousel
$carousel_products = $conn->query("
    SELECT p.*, s.subcategory_name, c.category_name 
    FROM auto_parts p 
    LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id 
    LEFT JOIN categories c ON s.category_id = c.category_id 
    WHERE p.image_url IS NOT NULL AND p.image_url != '' 
    ORDER BY p.created_at DESC 
    LIMIT 5
");

// Fetch featured products
$featured_products = $conn->query("
    SELECT p.*, s.subcategory_name, c.category_name 
    FROM auto_parts p 
    LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id 
    LEFT JOIN categories c ON s.category_id = c.category_id 
    WHERE p.stock > 0 
    ORDER BY p.created_at DESC 
    LIMIT 6
");

// Fetch motorcycle parts for the product list section
$motorcycle_parts = $conn->query("
    SELECT p.*, s.subcategory_name, c.category_name 
    FROM auto_parts p 
    LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id 
    LEFT JOIN categories c ON s.category_id = c.category_id 
    WHERE c.category_name = 'Motorbike' AND p.stock > 0 
    ORDER BY p.created_at DESC 
    LIMIT 5
");

// Check if user is logged in and get cart count
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
    <title>PartsLo.pk - Auto Parts Online Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5efefff; }
        .category-sidebar {
            background:#fff; border:1px solid #ddd; padding:15px; height:100%;
        }
        .category-sidebar ul li { padding:6px 0; font-size:15px; }
        .category-sidebar ul li a:hover { color: #dc3545 !important; }
        .product-card img { height:180px; object-fit:cover; }
        .search-box input { height:48px; }
        .search-box select { height:48px; }
        .carousel-item img { height: 250px; object-fit: cover; }
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
                                           class="text-decoration-none text-dark">
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

        <!-- RIGHT: CAROUSEL + PRODUCT DISPLAY GRID -->
        <div class="col-md-9">
            
            <!-- Image Carousel with Real Products -->
            <?php if ($carousel_products->num_rows > 0): ?>
            <div id="partsCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <?php for ($i = 0; $i < $carousel_products->num_rows; $i++): ?>
                        <button type="button" data-bs-target="#partsCarousel" data-bs-slide-to="<?= $i ?>" 
                                class="<?= $i === 0 ? 'active' : '' ?>" aria-current="<?= $i === 0 ? 'true' : 'false' ?>" 
                                aria-label="Slide <?= $i + 1 ?>"></button>
                    <?php endfor; ?>
                </div>
                <div class="carousel-inner">
                    <?php 
                    $carousel_products->data_seek(0);
                    $index = 0;
                    while ($product = $carousel_products->fetch_assoc()): 
                    ?>
                        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                            <img src="<?= htmlspecialchars($product['image_url'] ?? 'https://partslo.pk/wp-content/uploads/2020/08/Partslo-Background-Image.jpg') ?>" 
                                 class="d-block w-100" alt="<?= htmlspecialchars($product['part_name']) ?>">
                            <div class="carousel-caption d-none d-md-block bg-dark bg-opacity-50 rounded">
                                <h5><?= htmlspecialchars($product['part_name']) ?></h5>
                                <p>₹<?= number_format($product['price'], 2) ?> - <?= htmlspecialchars($product['category_name']) ?></p>
                                <a href="product_details.php?id=<?= $product['part_id'] ?>" class="btn btn-danger btn-sm">View Details</a>
                            </div>
                        </div>
                    <?php 
                    $index++;
                    endwhile; 
                    ?>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#partsCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#partsCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                </button>
            </div>
            <?php else: ?>
                <div id="partsCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="https://partslo.pk/wp-content/uploads/2020/08/Partslo-Background-Image.jpg" class="d-block w-100" alt="Auto Parts">
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Featured Products Section -->
            <h4 class="fw-bold mb-3">Featured Auto Parts</h4>
            <div class="row g-4">
                <?php if ($featured_products->num_rows > 0): ?>
                    <?php while ($product = $featured_products->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="card product-card shadow-sm">
                                <img src="<?= htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/300x180?text=No+Image') ?>" 
                                     class="card-img-top" alt="<?= htmlspecialchars($product['part_name']) ?>">
                                <div class="card-body">
                                    <h6 class="fw-semibold"><?= htmlspecialchars($product['part_name']) ?></h6>
                                    <p class="text-muted">₹<?= number_format($product['price'], 2) ?></p>
                                    <small class="text-muted d-block"><?= htmlspecialchars($product['category_name']) ?> • <?= htmlspecialchars($product['subcategory_name']) ?></small>
                                    <div class="mt-2">
                                        <a href="product_details.php?id=<?= $product['part_id'] ?>" class="btn btn-outline-dark w-100 mb-2">View Details</a>
                                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'buyer'): ?>
                                            <form method="POST" action="add_to_cart.php" class="d-inline w-100">
                                                <input type="hidden" name="part_id" value="<?= $product['part_id'] ?>">
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit" class="btn btn-danger w-100">Add to Cart</button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12 text-center py-4">
                        <p class="text-muted">No featured products available at the moment.</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product List Section -->
            <h4 class="fw-bold mb-3 mt-5">Motorcycle Parts</h4>
            
            <?php if ($motorcycle_parts->num_rows > 0): ?>
                <?php while ($product = $motorcycle_parts->fetch_assoc()): ?>
                    <div class="product-item d-flex align-items-center">
                        <img src="<?= htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/80x80?text=No+Image') ?>" 
                             class="me-3 rounded" alt="<?= htmlspecialchars($product['part_name']) ?>">
                        <div class="flex-grow-1">
                            <h6 class="mb-1"><?= htmlspecialchars($product['part_name']) ?></h6>
                            <div class="mb-2">
                                <span class="price-sale">₹<?= number_format($product['price'], 2) ?></span>
                            </div>
                            <small class="text-muted"><?= htmlspecialchars($product['category_name']) ?> • <?= htmlspecialchars($product['subcategory_name']) ?></small>
                            <?php if ($product['stock'] <= 5 && $product['stock'] > 0): ?>
                                <small class="text-warning d-block">Only <?= $product['stock'] ?> left in stock!</small>
                            <?php elseif ($product['stock'] == 0): ?>
                                <small class="text-danger d-block">Out of Stock</small>
                            <?php endif; ?>
                        </div>
                        <div class="text-end">
                            <a href="product_details.php?id=<?= $product['part_id'] ?>" class="btn btn-outline-dark me-2">View Details</a>
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'buyer' && $product['stock'] > 0): ?>
                                <form method="POST" action="add_to_cart.php" class="d-inline">
                                    <input type="hidden" name="part_id" value="<?= $product['part_id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-danger">Add to cart</button>
                                </form>
                            <?php elseif (!isset($_SESSION['user_id'])): ?>
                                <a href="login.php" class="btn btn-danger">Add to cart</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-muted">No motorcycle parts available at the moment.</p>
                </div>
            <?php endif; ?>

        </div>

    </div>
</div>

<footer class="bg-dark text-white text-center py-3 mt-5">© 2025 PartsLo.pk</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto-advance carousel every 3 seconds
    var carousel = new bootstrap.Carousel(document.getElementById('partsCarousel'), {
        interval: 3000
    });
</script>
</body>
</html>