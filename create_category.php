<?php
require_once "config.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['category_name']);

    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
        $stmt->bind_param("s", $name);
        if ($stmt->execute()) {
            $message = "Category created successfully!";
        } else {
            $message = "Error: Category may already exist.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Category</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Create Category</h2>

<?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="POST">
    <div class="mb-3">
        <label class="form-label">Category Name</label>
        <input type="text" name="category_name" class="form-control" required>
    </div>
    <button class="btn btn-success">Create</button>
</form>

<a href="list_categories.php" class="btn btn-secondary mt-3">Back</a>

</body>
</html>
