<?php
require_once "config.php";
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php"); exit;
}

// Fetch categories for dropdown
$cats = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");

$message = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subcategory_id = intval($_POST['subcategory_id'] ?? 0);
    $part_name = trim($_POST['part_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] === '' ? null : (float)$_POST['price'];
    $stock = intval($_POST['stock'] ?? 0);

    // basic validation
    if ($subcategory_id <= 0) $errors[] = "Please select a subcategory.";
    if ($part_name === '') $errors[] = "Part name is required.";

    // Image upload
    $image_db = null;
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','webp','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) $errors[] = "Invalid image type. Allowed: jpg,jpeg,png,webp,gif.";
        if ($_FILES['image']['size'] > 2 * 1024 * 1024) $errors[] = "Image too large (max 2MB).";
        if (empty($errors)) {
            $uploads = __DIR__ . '/uploads/';
            if (!is_dir($uploads)) mkdir($uploads, 0755, true);
            $fname = 'part_'.time().'_'.bin2hex(random_bytes(4)).'.'.$ext;
            $target = $uploads . $fname;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $image_db = 'uploads/' . $fname;
            } else {
                $errors[] = "Failed to move uploaded image.";
            }
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO auto_parts (subcategory_id, part_name, description, price, stock, image_url) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdis", $subcategory_id, $part_name, $description, $price, $stock, $image_db);
        if ($stmt->execute()) {
            $message = "Part added successfully.";
        } else {
            $errors[] = "Database error: " . $stmt->error;
            // remove uploaded file on error
            if ($image_db) @unlink(__DIR__ . '/' . $image_db);
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Add Part - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h2>Add Auto Part</h2>

  <?php if (!empty($message)): ?>
    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>
  <?php if (!empty($errors)): foreach ($errors as $e): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($e) ?></div>
  <?php endforeach; endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="row g-2">
      <div class="col-md-6">
        <label class="form-label">Category</label>
        <select id="category_select" class="form-select">
          <option value="">-- Select Category --</option>
          <?php while ($c = $cats->fetch_assoc()): ?>
            <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Subcategory</label>
        <select name="subcategory_id" id="subcategory_select" class="form-select" required>
          <option value="">-- Select Subcategory --</option>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Part Name</label>
        <input name="part_name" class="form-control" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Price (PKR)</label>
        <input name="price" type="number" step="0.01" class="form-control">
      </div>

      <div class="col-md-3">
        <label class="form-label">Stock</label>
        <input name="stock" type="number" class="form-control" value="0">
      </div>

      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4"></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Image (max 2MB)</label>
        <input name="image" type="file" accept="image/*" class="form-control">
      </div>

      <div class="col-12 text-end">
        <a href="list_parts.php" class="btn btn-secondary">Back to parts</a>
        <button class="btn btn-success">Add Part</button>
      </div>
    </div>
  </form>
</div>

<script>
document.getElementById('category_select').addEventListener('change', function(){
  const catId = this.value;
  const subSel = document.getElementById('subcategory_select');
  subSel.innerHTML = '<option>Loading...</option>';
  if (!catId) { subSel.innerHTML = '<option value=\"\">-- Select Subcategory --</option>'; return; }
  fetch('get_subcategories.php?category_id=' + encodeURIComponent(catId))
    .then(r => r.json())
    .then(data => {
      subSel.innerHTML = '<option value=\"\">-- Select Subcategory --</option>';
      data.forEach(s => {
        const opt = document.createElement('option');
        opt.value = s.subcategory_id;
        opt.textContent = s.subcategory_name;
        subSel.appendChild(opt);
      });
    })
    .catch(()=>{ subSel.innerHTML = '<option value=\"\">-- Select Subcategory --</option>'; });
});
</script>
</body>
</html>
