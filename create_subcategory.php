<?php
require_once "config.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

// Fetch all categories for dropdown
$categories = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = intval($_POST['category_id']);
    $subcategory_name = trim($_POST['subcategory_name']);

    if (!empty($subcategory_name) && $category_id > 0) {
        $stmt = $conn->prepare("INSERT INTO subcategories (category_id, subcategory_name) VALUES (?, ?)");
        $stmt->bind_param("is", $category_id, $subcategory_name);

        if ($stmt->execute()) {
            $message = "Subcategory created successfully!";
        } else {
            $message = "Error: Could not create subcategory.";
        }
    } else {
        $message = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Subcategory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Create Subcategory</h2>

<?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="POST">

    <div class="mb-3">
        <label class="form-label">Select Category</label>
        <select name="category_id" class="form-control" required>
            <option value="">-- Select Category --</option>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['category_id'] ?>">
                    <?= $cat['category_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Subcategory Name</label>
        <input type="text" name="subcategory_name" class="form-control" required>
    </div>

    <button class="btn btn-success">Create</button>
</form>

<a href="list_subcategories.php" class="btn btn-secondary mt-3">Back</a>

</body>
</html>
