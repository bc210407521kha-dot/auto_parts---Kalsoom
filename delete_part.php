<?php
require_once "config.php";
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') { header("Location: login.php"); exit; }

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: list_parts.php"); exit; }

// fetch image path
$stmt = $conn->prepare("SELECT image_url FROM auto_parts WHERE part_id=?");
$stmt->bind_param("i",$id); $stmt->execute();
$stmt->bind_result($img); $stmt->fetch(); $stmt->close();

// delete db row
$stmt = $conn->prepare("DELETE FROM auto_parts WHERE part_id=?");
$stmt->bind_param("i",$id);
if ($stmt->execute()) {
    if (!empty($img) && file_exists(__DIR__ . '/' . $img)) @unlink(__DIR__ . '/' . $img);
}
header("Location: list_parts.php");
exit;
