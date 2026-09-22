<?php
require_once "config.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Validate ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request! Category ID missing.");
}

$id = intval($_GET['id']);
$message = "";

// Fetch category
$stmt = $conn->prepare("SELECT category_name FROM categories WHERE category_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($category_name);
$stmt->fetch();
$stmt->close();

// If category NOT found
if (!$category_name) {
    die("Category not found.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['category_name']);

    $stmt = $conn->prepare("UPDATE categories SET category_name = ? WHERE category_id = ?");
    $stmt->bind_param("si", $new_name, $id);

    if ($stmt->execute()) {
        $message = "Category updated successfully!";
    } else {
        $message = "Error updating category.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Category</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Edit Category</h2>

<?php if ($message): ?>
    <div class="alert alert-info"> <?= $message ?> </div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" name="category_name" class="form-control"
               value="<?= htmlspecialchars($category_name) ?>" required>
    </div>
    <button class="btn btn-success">Update</button>
</form>

<a href="list_categories.php" class="btn btn-secondary mt-3">Back</a>

</body>
</html>
