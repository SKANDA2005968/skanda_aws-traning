<?php
require_once __DIR__ . '/includes/functions.php';
$db = getDB();

$cart = getCart();
$items = [];
$subtotal = 0;
$totalMrp = 0;

if (!empty($cart)) {
    $ids = array_keys($cart);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
    $stmt->execute($ids);
    $productsById = [];
    foreach ($stmt->fetchAll() as $row) {
        $productsById[$row['id']] = $row;
    }

    foreach ($cart as $pid => $qty) {
        if (!isset($productsById[$pid])) continue;
        $p = $productsById[$pid];
        $qty = min($qty, max(1, $p['stock']));
        $lineTotal = $p['price'] * $qty;
        $lineMrp = $p['mrp'] * $qty;
        $subtotal += $lineTotal;
        $totalMrp += $lineMrp;
        $items[] = ['product' => $p, 'qty' => $qty, 'line_total' => $lineTotal];
    }
}

$savings = $totalMrp - $subtotal;
$deliveryFee = ($subtotal > 0 && $subtotal < 499) ? 40 : 0;
$grandTotal = $subtotal + $deliveryFee;

$pageTitle = 'Cart';
require __DIR__ . '/includes/header.php';
?>

<div class="section-title"><span>Shopping Cart</span></div>

<?php if (empty($items)): ?>
  <div class="empty-cart">
    <div class="icon">🛒</div>
    <h2>Your cart is empty</h2>
    <p>Looks like you haven't added anything yet.</p>
    <a href="/index.php" class="btn">Continue Shopping</a>
  </div>
<?php else: ?>
  <div class="cart-layout">
    <div class="cart-box">
      <?php foreach ($items as $item): $p = $item['product']; ?>
        <div class="cart-item">
          <a href="/product.php?slug=<?= e($p['slug']) ?>">
            <img src="<?= e(productImage($p['image_seed'])) ?>" alt="<?= e($p['name']) ?>">
          </a>
          <div class="cart-item-info">
            <a href="/product.php?slug=<?= e($p['slug']) ?>"><div class="cart-item-name"><?= e($p['name']) ?></div></a>
            <div class="product-brand"><?= e($p['brand']) ?></div>
            <?php if ($p['stock'] <= 5): ?><div class="stock-low">Only <?= (int)$p['stock'] ?> left in stock</div><?php endif; ?>
            <div class="cart-item-actions">
              <form method="post" action="/cart_action.php" style="display:flex;align-items:center;gap:6px;">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="redirect" value="/cart.php">
                Qty:
                <input type="number" name="qty" value="<?= (int)$item['qty'] ?>" min="1" max="<?= (int)$p['stock'] ?>" class="qty-input">
                <button type="submit" class="btn btn-outline" style="padding:4px 10px;">Update</button>
              </form>
              <form method="post" action="/cart_action.php">
                <input type="hidden" name="action" value="remove">
                <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
                <input type="hidden" name="redirect" value="/cart.php">
                <button type="submit" style="background:none;border:none;color:#007185;text-decoration:underline;padding:0;">Delete</button>
              </form>
            </div>
          </div>
          <div class="cart-item-price"><?= formatINR($item['line_total']) ?></div>
        </div>
      <?php endforeach; ?>
      <p style="text-align:right;font-size:16px;padding-top:12px;">
        Subtotal (<?= array_sum(array_column($items, 'qty')) ?> items): <strong><?= formatINR($subtotal) ?></strong>
      </p>
    </div>

    <div class="summary-box">
      <?php if ($savings > 0): ?>
        <div class="alert alert-info" style="font-size:13px;">You will save <?= formatINR($savings) ?> on this order</div>
      <?php endif; ?>
      <div class="summary-row"><span>Items:</span><span><?= formatINR($subtotal) ?></span></div>
      <div class="summary-row"><span>Delivery:</span><span><?= $deliveryFee > 0 ? formatINR($deliveryFee) : 'FREE' ?></span></div>
      <hr class="pd-divider">
      <div class="summary-row summary-total"><span>Order Total:</span><span><?= formatINR($grandTotal) ?></span></div>
      <a href="/checkout.php" class="btn btn-secondary btn-block" style="margin-top:12px;text-align:center;display:block;">Proceed to Buy</a>
    </div>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
