<?php
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Mark feedback as read if clicked
if (isset($_GET['mark_read'])) {
    $feedback_id = intval($_GET['mark_read']);
    $conn->query("UPDATE feedback SET is_read=1 WHERE feedback_id=$feedback_id");
}

// Fetch all feedback, most recent first
$res = $conn->query("SELECT f.feedback_id, f.user_id, f.order_id, f.comment, f.rating, f.created_at, f.is_read, u.full_name
                     FROM feedback f
                     JOIN users u ON f.user_id=u.id
                     ORDER BY f.created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Feedback</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .unread-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background-color: red;
            border-radius: 50%;
            margin-right: 5px;
        }
    </style>
</head>
<body class="p-4">

<h2>User Feedback</h2>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>User</th>
            <th>Order ID</th>
            <th>Comment</th>
            <th>Rating</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($f = $res->fetch_assoc()): ?>
            <tr>
                <td>
                    <?php if ($f['is_read']==0): ?>
                        <span class="unread-dot"></span>
                    <?php endif; ?>
                    <?= htmlspecialchars($f['full_name']) ?>
                </td>
                <td>
                    <a href="admin_feedback.php?mark_read=<?= $f['feedback_id'] ?>">
                        <?= $f['order_id'] ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($f['comment']) ?></td>
                <td><?= $f['rating'] ?></td>
                <td><?= $f['created_at'] ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
