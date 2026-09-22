<?php
require_once "config.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$id = intval($_GET['id']);

$stmt = $conn->prepare("DELETE FROM subcategories WHERE subcategory_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: list_subcategories.php");
exit();
