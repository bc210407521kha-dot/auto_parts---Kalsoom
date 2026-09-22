<?php
require_once "config.php";
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') { header("Location: login.php"); exit; }

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: list_parts.php"); exit; }

// Fetch part
$stmt = $conn->prepare("SELECT part_id, subcategory_id, part_name, description, price, stock, image_url FROM auto_parts WHERE part_id=?");
$stmt->bind_param("i",$id); $stmt->execute(); $res = $stmt->get_result();
if ($res->num_rows !== 1) { header("Location: list_parts.php"); exit; }
$part = $res->fetch_assoc();

// Get categories and subcategories
$cats = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
// For initially selected subcategory, find its category_id
$catId = null;
if ($part['subcategory_id']) {
    $r = $conn->query("SELECT category_id FROM subcategories WHERE subcategory_id = ".intval($part['subcategory_id']));
    if ($r && $rr = $r->fetch_assoc()) $catId = $rr['category_id'];
}
$subcategories_initial = $conn->query("SELECT subcategory_id, subcategory_name FROM subcategories WHERE category_id = ".($catId ?: 0)." ORDER BY subcategory_name ASC");

$message=''; $errors=[];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_subcategory_id = intval($_POST['subcategory_id'] ?? 0);
    $part_name = trim($_POST['part_name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = $_POST['price'] === '' ? null : (float)$_POST['price'];
    $stock = intval($_POST['stock'] ?? 0);

    if ($new_subcategory_id <= 0) $errors[] = "Please select subcategory.";
    if ($part_name === '') $errors[] = "Part name required.";

    // handle image replace
    $new_image_db = $part['image_url'];
    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','webp','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) $errors[] = "Invalid image type.";
        if ($_FILES['image']['size'] > 2*1024*1024) $errors[] = "Image too large (2MB).";
        if (empty($errors)) {
            $uploads = __DIR__ . '/uploads/';
            if (!is_dir($uploads)) mkdir($uploads,0755,true);
            $fname = 'part_'.time().'_'.bin2hex(random_bytes(4)).'.'.$ext;
            $target = $uploads . $fname;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                $new_image_db = 'uploads/'.$fname;
            } else $errors[] = "Failed to save uploaded image.";
        }
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE auto_parts SET subcategory_id=?, part_name=?, description=?, price=?, stock=?, image_url=? WHERE part_id=?");
        $stmt->bind_param("issdisi", $new_subcategory_id, $part_name, $description, $price, $stock, $new_image_db, $id);
        if ($stmt->execute()) {
            // remove old image if replaced
            if ($new_image_db !== $part['image_url'] && !empty($part['image_url']) && file_exists(__DIR__ . '/' . $part['image_url'])) {
                @unlink(__DIR__ . '/' . $part['image_url']);
            }
            $message = "Part updated successfully.";
            // refresh part data
            $part['subcategory_id']=$new_subcategory_id; $part['part_name']=$part_name; $part['description']=$description;
            $part['price']=$price; $part['stock']=$stock; $part['image_url']=$new_image_db;
        } else {
            $errors[] = "DB error: " . $stmt->error;
            if ($new_image_db !== $part['image_url']) @unlink(__DIR__ . '/' . $new_image_db);
        }
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Edit Part</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<div class="container">
  <h2>Edit Part #<?= $part['part_id'] ?></h2>

  <?php if (!empty($message)): ?><div class="alert alert-success"><?= htmlspecialchars($message) ?></div><?php endif; ?>
  <?php if (!empty($errors)): foreach($errors as $e): ?><div class="alert alert-danger"><?= htmlspecialchars($e) ?></div><?php endforeach; endif; ?>

  <form method="post" enctype="multipart/form-data">
    <div class="row g-2">
      <div class="col-md-6">
        <label class="form-label">Category</label>
        <select id="category_select" class="form-select">
          <option value="">-- Select Category --</option>
          <?php while ($c = $cats->fetch_assoc()): ?>
            <option value="<?= $c['category_id'] ?>" <?= ($c['category_id'] == $catId) ? 'selected':'' ?>><?= htmlspecialchars($c['category_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Subcategory</label>
        <select name="subcategory_id" id="subcategory_select" class="form-select" required>
          <option value="">-- Select Subcategory --</option>
          <?php while ($s = $subcategories_initial->fetch_assoc()): ?>
            <option value="<?= $s['subcategory_id'] ?>" <?= ($s['subcategory_id']==$part['subcategory_id'])?'selected':'' ?>><?= htmlspecialchars($s['subcategory_name']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

      <div class="col-md-6">
        <label class="form-label">Part Name</label>
        <input name="part_name" class="form-control" value="<?= htmlspecialchars($part['part_name']) ?>" required>
      </div>

      <div class="col-md-3">
        <label class="form-label">Price</label>
        <input name="price" type="number" step="0.01" class="form-control" value="<?= $part['price'] ?>">
      </div>

      <div class="col-md-3">
        <label class="form-label">Stock</label>
        <input name="stock" type="number" class="form-control" value="<?= $part['stock'] ?>">
      </div>

      <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($part['description']) ?></textarea>
      </div>

      <div class="col-md-6">
        <label class="form-label">Replace Image (optional)</label>
        <input name="image" type="file" accept="image/*" class="form-control">
        <?php if (!empty($part['image_url'])): ?>
          <div class="mt-2"><img src="<?= htmlspecialchars($part['image_url']) ?>" style="height:80px"></div>
        <?php endif; ?>
      </div>

      <div class="col-12 text-end">
        <a href="list_parts.php" class="btn btn-secondary">Back</a>
        <button class="btn btn-success">Update Part</button>
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
    }).catch(()=>{ subSel.innerHTML = '<option value=\"\">-- Select Subcategory --</option>'; });
});
</script>
</body>
</html>
  