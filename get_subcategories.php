<?php
require_once "config.php";
header('Content-Type: application/json; charset=utf-8');

$category_id = intval($_GET['category_id'] ?? 0);
$out = [];

if ($category_id > 0) {
    $stmt = $conn->prepare("SELECT subcategory_id, subcategory_name FROM subcategories WHERE category_id=? ORDER BY subcategory_name ASC");
    $stmt->bind_param("i",$category_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $out[] = $r;
}

echo json_encode($out); 