<?php
session_start();
include "config.php";

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch feedback
$sql = "SELECT f.*, u.username, o.order_id FROM feedback f 
        JOIN users u ON f.user_id=u.id
        JOIN orders o ON f.order_id=o.order_id
        ORDER BY f.created_at DESC";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Feedbacks</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>All Feedbacks</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Feedback ID</th>
            <th>Buyer</th>
            <th>Order ID</th>
            <th>Comment</th>
            <th>Rating</th>
            <th>Submitted At</th>
        </tr>
    </thead>
    <tbody>
        <?php while($fb = $res->fetch_assoc()): ?>
            <tr>
                <td><?= $fb['feedback_id'] ?></td>
                <td><?= htmlspecialchars($fb['username']) ?></td>
                <td><?= $fb['order_id'] ?></td>
                <td><?= htmlspecialchars($fb['comment']) ?></td>
                <td><?= $fb['rating'] ?></td>
                <td><?= $fb['created_at'] ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
