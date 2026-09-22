<?php
include "config.php";

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'buyer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = intval($_POST['order_id']);
    $comment = trim($_POST['comment']);
    $rating = intval($_POST['rating']);

    $stmt = $conn->prepare("INSERT INTO feedback (user_id, order_id, comment, rating) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $user_id, $order_id, $comment, $rating);
    $stmt->execute();
    $stmt->close();

    header("Location: feedback.php?submitted=1");
    exit();
}

// Fetch delivered orders without feedback
$sql = "SELECT o.order_id, o.total_amount, o.created_at
        FROM orders o
        LEFT JOIN feedback f ON o.order_id=f.order_id AND f.user_id=?
        WHERE o.user_id=? AND o.status='Delivered' AND f.feedback_id IS NULL
        ORDER BY o.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Submit Feedback</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<h2>Orders Pending Feedback</h2>

<?php if (isset($_GET['submitted'])): ?>
    <div class="alert alert-success">Feedback submitted successfully!</div>
<?php endif; ?>

<?php while ($order = $result->fetch_assoc()): ?>
    <div class="card mb-3">
        <div class="card-header">
            Order #<?= $order['order_id'] ?> | Total: <?= number_format($order['total_amount'], 2) ?>
        </div>
        <div class="card-body">
            <form method="POST">
                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                <div class="mb-2">
                    <textarea name="comment" class="form-control" placeholder="Write your feedback" required></textarea>
                </div>
                <div class="mb-2">
                    <label>Rating:</label>
                    <select name="rating" class="form-select" required>
                        <option value="">Select</option>
                        <?php for ($i=1;$i<=5;$i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </form>
        </div>
    </div>
<?php endwhile; ?>

<?php if ($result->num_rows === 0): ?>
    <p>No orders pending feedback.</p>
<?php endif; ?>

</body>
</html>
