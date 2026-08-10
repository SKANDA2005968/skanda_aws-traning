<?php
require_once __DIR__ . '/includes/functions.php';
$db = getDB();

$orderNumber = $_GET['order'] ?? '';
$stmt = $db->prepare('SELECT * FROM orders WHERE order_number = ?');
$stmt->execute([$orderNumber]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: /index.php');
    exit;
}

$pageTitle = 'Order Confirmed';
require __DIR__ . '/includes/header.php';
?>

<div class="order-confirm">
  <div class="icon">✅</div>
  <h1>Thank you, your order is confirmed!</h1>
  <p>A confirmation would normally be sent to your email/phone.</p>
  <div class="order-number">Order ID: <?= e($order['order_number']) ?></div>
  <p>Total paid: <strong><?= formatINR($order['total']) ?></strong></p>
  <p>Delivery to: <?= e($order['address']) ?></p>
  <div style="margin-top:20px;">
    <a href="/orders.php" class="btn">View Your Orders</a>
    <a href="/index.php" class="btn btn-outline" style="margin-left:10px;">Continue Shopping</a>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
