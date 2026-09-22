<?php
require_once "config.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM categories ORDER BY category_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>All Categories</h2>
<a href="create_category.php" class="btn btn-primary mb-3">+ Add Category</a>

<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>Category Name</th>
        <th>Actions</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['category_id'] ?></td>
        <td><?= $row['category_name'] ?></td>
        <td>
            <a href="update_category.php?id=<?= $row['category_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="delete_category.php?id=<?= $row['category_id'] ?>" class="btn btn-danger btn-sm"
               onclick="return confirm('Delete this category?');">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
 