<?php include "config.php";  
// Get search parameters 
$search_query = isset($_GET['query']) ? trim($_GET['query']) : ''; 
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;  

// Build search query 
$where_conditions = ["p.stock > 0"]; 
$params = []; 
$types = "";  

if (!empty($search_query)) {     
    $where_conditions[] = "(p.part_name LIKE ? OR p.description LIKE ?)";     
    $search_term = "%$search_query%";     
    $params[] = $_term;     
    $params[] = $search_term;     
    $types .= "ss"; 
}  

if ($category_id > 0) {     
    $where_conditions[] = "c.category_id = ?";     
    $params[] = $category_id;     
    $types .= "i"; 
}  

$where_clause = implode(" AND ", $where_conditions);  

// Fetch products based on search 
$search_sql = "     
    SELECT p.*, s.subcategory_name, c.category_name      
    FROM auto_parts p      
    LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id      
    LEFT JOIN categories c ON s.category_id = c.category_id      
    WHERE $where_clause      
    ORDER BY p.created_at DESC ";  
$search_stmt = $conn->prepare($search_sql); 
if (!empty($params)) {     
    $search_stmt->bind_param($types, ...$params); 
} 
$search_stmt->execute(); 
$search_result = $search_stmt->get_result();  

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
    <title>Search Results - PartsLo.pk</title>     
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">     
    <style>         
        body { background-color: #f5efefff; }         
        .product-card img { height:180px; object-fit:cover; }         
        .search-box input { height:48px; }         
        .search-box select { height:48px; }         
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

        <!-- RIGHT: SEARCH RESULTS -->         
        <div class="col-md-12">                          
            <!-- Breadcrumb -->             
            <nav aria-label="breadcrumb">                 
                <ol class="breadcrumb">                     
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>                     
                    <li class="breadcrumb-item active" aria-current="page">Search Results</li>                 
                </ol>             
            </nav>             

            <!-- Page Header -->             
            <div class="d-flex justify-content-between align-items-center mb-4">                 
                <div>                     
                    <h2 class="fw-bold mb-1">Search Results</h2>                     
                    <p class="text-muted mb-0">                         
                        <?php if (!empty($search_query)): ?>                             
                            Search for "<?= htmlspecialchars($search_query) ?>"                         
                        <?php else: ?>                             
                            All Products                         
                        <?php endif; ?>                     
                    </p>                 
                </div>                 
                <div class="text-muted">                     
                    <?= $search_result->num_rows ?> product(s) found                 
                </div>             
            </div>             

            <!-- Search Results Grid -->             
            <?php if ($search_result->num_rows > 0): ?>                 
                <div class="row g-4">                     
                    <?php while ($product = $search_result->fetch_assoc()): ?>                         
                        <div class="col-md-4">                             
                            <div class="card product-card shadow-sm h-100">                                 
                                <img src="<?= htmlspecialchars($product['image_url'] ?? 'https://via.placeholder.com/300x180?text=No+Image') ?>" class="card-img-top" alt="<?= htmlspecialchars($product['part_name']) ?>">                                 
                                <div class="card-body d-flex flex-column">                                     
                                    <h6 class="fw-semibold"><?= htmlspecialchars($product['part_name']) ?></h6>                                     
                                    <p class="text-muted mb-2">₹<?= number_format($product['price'], 2) ?></p>                                     
                                    <p class="text-muted small">                                         
                                        <strong>Category:</strong> <?= htmlspecialchars($product['category_name']) ?><br>                                         
                                        <strong>Type:</strong> <?= htmlspecialchars($product['subcategory_name']) ?>                                     
                                    </p>                                                                         

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
                                            <a href="product_details.php?id=<?= $product['part_id'] ?>" class="btn btn-outline-dark">View Details</a>  
                                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_type'] == 'buyer' && $product['stock'] > 0): ?>                                                 
                                                <form method="POST" action="add_to_cart.php" class="d-inline">                                                     
                                                    <input type="hidden" name="part_id" value="<?= $product['part_id'] ?>">                                                     
                                                    <input type="hidden" name="quantity" value="1">                                                     
                                                    <button type="submit" class="btn btn-danger w-100">Add to Cart</button>                                                 
                                                </form>                                            
                                            <?php elseif (!isset($_SESSION['user_id']) && $product['stock'] > 0): ?>                                                 
                                                <a href="login.php" class="btn btn-danger w-100">Add to Cart</a>                                            
                                            <?php endif; ?>                                         
                                        </div>                                     </div>                                 </div>                             </div>                         
                        </div>                     
                    <?php endwhile; ?>                 
                </div>             
            <?php else: ?>                 
                <div class="text-center py-5">                     
                    <div class="mb-4">                         
                        <i class="fas fa-search fa-3x text-muted"></i>                     
                    </div>                     
                    <h4 class="text-muted">No Products Found</h4>                     
                    <p class="text-muted">                         
                        <?php if (!empty($search_query)): ?>                             
                            No products found for "<?= htmlspecialchars($search_query) ?>"                         
                        <?php else: ?>                             
                            No products found in the selected category.                         
                        <?php endif; ?>                     
                    </p>                     
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