<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Delete all items from the cart
$conn->query("DELETE FROM cart WHERE user_id=$user_id");

header("Location: browse_products.php");
exit();
?>
