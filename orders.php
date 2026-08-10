<?php
require_once __DIR__ . '/includes/functions.php';
$db = getDB();

$orders = $db->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 50')->fetchAll();

$itemsByOrder = [];
if (!empty($orders)) {
    $ids = array_column($orders, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("
        SELECT oi.order_id, oi.quantity, oi.price, p.name, p.image_seed, p.slug
        FROM order_items oi JOIN products p ON p.id = oi.product_id
        WHERE oi.order_id IN ($placeholders)
    ");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $row) {
        $itemsByOrder[$row['order_id']][] = $row;
    }
}

$pageTitle = 'Your Orders';
require __DIR__ . '/includes/header.php';
?>

<div class="section-title"><span>Your Orders</span></div>
<p style="color:#565959;font-size:13px;margin-top:-6px;">
  Demo note: this shows every order placed on this server (no login system in this sample project).
</p>

<?php if (empty($orders)): ?>
  <div class="no-results">
    <h3>No orders yet</h3>
    <p>Orders you place will show up here.</p>
    <a href="/index.php" class="btn" style="margin-top:10px;display:inline-block;">Start Shopping</a>
  </div>
<?php else: ?>
  <?php foreach ($orders as $order): ?>
    <div class="cart-box" style="margin-bottom:16px;">
      <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:8px;border-bottom:1px solid #eee;padding-bottom:10px;margin-bottom:10px;">
        <div>
          <div style="font-size:12px;color:#565959;">ORDER PLACED</div>
          <div><?= e(date('d M Y', strtotime($order['created_at']))) ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:#565959;">TOTAL</div>
          <div><?= formatINR($order['total']) ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:#565959;">STATUS</div>
          <div style="color:#067D62;font-weight:700;"><?= e($order['status']) ?></div>
        </div>
        <div>
          <div style="font-size:12px;color:#565959;">ORDER #</div>
          <div><?= e($order['order_number']) ?></div>
        </div>
      </div>
      <?php foreach ($itemsByOrder[$order['id']] ?? [] as $item): ?>
        <div style="display:flex;gap:12px;align-items:center;padding:6px 0;">
          <img src="<?= e(productImage($item['image_seed'], 60)) ?>" style="width:50px;height:50px;object-fit:cover;border-radius:4px;">
          <a href="/product.php?slug=<?= e($item['slug']) ?>" style="flex:1;"><?= e($item['name']) ?> × <?= (int)$item['quantity'] ?></a>
          <span><?= formatINR($item['price'] * $item['quantity']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
