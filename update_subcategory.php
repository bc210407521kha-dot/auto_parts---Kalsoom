<?php
require_once "config.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id']);
$message = "";

// Fetch subcategory
$stmt = $conn->prepare("SELECT category_id, subcategory_name FROM subcategories WHERE subcategory_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($category_id, $subcategory_name);
$stmt->fetch();
$stmt->close();

// Fetch categories for dropdown
$categories = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_category_id = intval($_POST['category_id']);
    $new_name = trim($_POST['subcategory_name']);

    $stmt = $conn->prepare("UPDATE subcategories SET category_id=?, subcategory_name=? WHERE subcategory_id=?");
    $stmt->bind_param("isi", $new_category_id, $new_name, $id);

    if ($stmt->execute()) {
        $message = "Subcategory updated successfully!";
    } else {
        $message = "Error updating subcategory.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Subcategory</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Edit Subcategory</h2>

<?php if ($message): ?>
    <div class="alert alert-info"><?= $message ?></div>
<?php endif; ?>

<form method="POST">

    <div class="mb-3">
        <label class="form-label">Select Category</label>
        <select name="category_id" class="form-control" required>
            <?php while ($cat = $categories->fetch_assoc()): ?>
                <option value="<?= $cat['category_id'] ?>"
                    <?= ($cat['category_id'] == $category_id) ? "selected" : "" ?>>
                    <?= $cat['category_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Subcategory Name</label>
        <input type="text" name="subcategory_name" class="form-control" value="<?= $subcategory_name ?>" required>
    </div>

    <button class="btn btn-success">Update</button>

</form>

<a href="list_subcategories.php" class="btn btn-secondary mt-3">Back</a>

</body>
</html>
