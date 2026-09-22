<?php
require_once "config.php";

// Only admin access
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Pagination settings
$limit = 10;
$page = intval($_GET['page'] ?? 1);
$offset = ($page - 1) * $limit;

// Search / filter
$search = trim($_GET['search'] ?? '');
$category_id = intval($_GET['category_id'] ?? 0);
$subcategory_id = intval($_GET['subcategory_id'] ?? 0);

// Build WHERE conditions
$where = [];
$params = [];
$types = "";

if ($search) {
    $where[] = "p.part_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if ($category_id) {
    $where[] = "c.category_id = ?";
    $params[] = $category_id;
    $types .= "i";
}

if ($subcategory_id) {
    $where[] = "s.subcategory_id = ?";
    $params[] = $subcategory_id;
    $types .= "i";
}

$where_sql = $where ? "WHERE " . implode(" AND ", $where) : "";

// Count total parts
$count_sql = "SELECT COUNT(*) FROM auto_parts p
              JOIN subcategories s ON p.subcategory_id = s.subcategory_id
              JOIN categories c ON s.category_id = c.category_id
              $where_sql";

$stmt = $conn->prepare($count_sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$stmt->bind_result($total_parts);
$stmt->fetch();
$stmt->close();

$total_pages = ceil($total_parts / $limit);

// Fetch parts
$sql = "SELECT p.part_id, p.part_name, p.description, p.price, p.stock, p.image_url, 
               s.subcategory_name, c.category_name
        FROM auto_parts p
        JOIN subcategories s ON p.subcategory_id = s.subcategory_id
        JOIN categories c ON s.category_id = c.category_id
        $where_sql
        ORDER BY p.part_id DESC
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);

if ($params) {
    $all_params = array_merge($params, [$offset, $limit]);
    $types_limit = $types . "ii";
    $stmt->bind_param($types_limit, ...$all_params);
} else {
    $stmt->bind_param("ii", $offset, $limit);
}

$stmt->execute();
$result = $stmt->get_result();

// Fetch categories for filter dropdown
$categories_res = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
$categories = $categories_res->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Parts List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Parts List</h2>

<!-- Search / filter form -->
<form class="row g-3 mb-4" method="GET">
    <div class="col-md-4">
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
        </select>
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100">Filter</button>
    </div>
</form>

<a href="create_part.php" class="btn btn-success mb-3">+ Add New Part</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Part Name</th>
            <th>Category</th>
            <th>Subcategory</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($part = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $part['part_id'] ?></td>
                <td>
                    <?php if ($part['image_url']): ?>
                        <img src="<?= htmlspecialchars($part['image_url']) ?>" alt="" width="50">
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($part['part_name']) ?></td>
                <td><?= htmlspecialchars($part['category_name']) ?></td>
                <td><?= htmlspecialchars($part['subcategory_name']) ?></td>
                <td><?= number_format($part['price'], 2) ?></td>
                <td><?= $part['stock'] ?></td>
                <td>
                    <a href="update_part.php?id=<?= $part['part_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_part.php?id=<?= $part['part_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this part?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<!-- Pagination -->
<nav>
    <ul class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&category_id=<?= $category_id ?>&subcategory_id=<?= $subcategory_id ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Dynamic subcategories via AJAX
    $('#categorySelect').on('change', function() {
        var category_id = $(this).val();
        $('#subcategorySelect').html('<option value="">Select Subcategory</option>');
        if (category_id) {
            $.getJSON('get_subcategories.php?category_id=' + category_id, function(data) {
                $.each(data, function(i, item) {
                    $('#subcategorySelect').append('<option value="' + item.subcategory_id + '">' + item.subcategory_name + '</option>');
                });
            });
        }
    });

    // Preload subcategories if category selected
    <?php if ($category_id): ?>
    $(document).ready(function() {
        $.getJSON('get_subcategories.php?category_id=<?= $category_id ?>', function(data) {
            var subcategory_id = <?= $subcategory_id ?>;
            $.each(data, function(i, item) {
                var selected = item.subcategory_id == subcategory_id ? 'selected' : '';
                $('#subcategorySelect').append('<option value="' + item.subcategory_id + '" ' + selected + '>' + item.subcategory_name + '</option>');
            });
        });
    });
    <?php endif; ?>
</script>

</body>
</html>
