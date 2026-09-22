<?php
session_start();
include "config.php";

// Check if user is logged in and is a buyer
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$part_id = intval($_GET['part_id'] ?? 0);

if (!$part_id) {
    header("Location: browse_products.php");
    exit();
}

// Check if the item is already in the cart
$check = $conn->prepare("SELECT quantity FROM cart WHERE user_id=? AND part_id=?");
$check->bind_param("ii", $user_id, $part_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Increase quantity
    $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id=? AND part_id=?");
    $update->bind_param("ii", $user_id, $part_id);
    $update->execute();
    $update->close();
} else {
    // Insert first time
    $insert = $conn->prepare("INSERT INTO cart (user_id, part_id, quantity) VALUES (?, ?, 1)");
    $insert->bind_param("ii", $user_id, $part_id);
    $insert->execute();
    $insert->close();
}

$check->close();

header("Location: view_cart.php");
exit();
?>
