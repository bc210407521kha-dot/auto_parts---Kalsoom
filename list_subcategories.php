<?php
require_once "config.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Join to show category name with subcategory
$query = "
    SELECT s.subcategory_id, s.subcategory_name, c.category_name
    FROM subcategories s
    LEFT JOIN categories c ON s.category_id = c.category_id
    ORDER BY s.subcategory_id DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Subcategories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>All Subcategories</h2>
<a href="create_subcategory.php" class="btn btn-primary mb-3">+ Add Subcategory</a>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Category</th>
        <th>Subcategory</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['subcategory_id'] ?></td>
        <td><?= $row['category_name'] ?></td>
        <td><?= $row['subcategory_name'] ?></td>
        <td>
            <a href="update_subcategory.php?id=<?= $row['subcategory_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="delete_subcategory.php?id=<?= $row['subcategory_id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this subcategory?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>

</table>

</body>
</html>
