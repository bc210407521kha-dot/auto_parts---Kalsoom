<?php
include "config.php";

// Check if buyer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $total_amount = floatval($_POST['total_amount']);

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, full_name, phone, address, total_amount) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssd", $user_id, $full_name, $phone, $address, $total_amount);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Fetch cart items
        $sql = "SELECT c.part_id, c.quantity, p.price FROM cart c JOIN auto_parts p ON c.part_id=p.part_id WHERE c.user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Insert order_items
        $stmt_insert = $conn->prepare("INSERT INTO order_items (order_id, part_id, quantity, price) VALUES (?, ?, ?, ?)");
        while ($item = $result->fetch_assoc()) {
            $stmt_insert->bind_param("iiid", $order_id, $item['part_id'], $item['quantity'], $item['price']);
            $stmt_insert->execute();
        }
        $stmt_insert->close();

        // Clear cart
        $conn->query("DELETE FROM cart WHERE user_id=$user_id");

        $conn->commit();
        header("Location: my_orders.php?msg=Order placed successfully");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: browse_products.php");
    exit();
}
