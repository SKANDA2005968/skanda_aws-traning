<?php
require_once __DIR__ . '/includes/functions.php';
$db = getDB();

$cart = getCart();
if (empty($cart)) {
    header('Location: /cart.php');
    exit;
}

$ids = array_keys($cart);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$productsById = [];
foreach ($stmt->fetchAll() as $row) {
    $productsById[$row['id']] = $row;
}

$items = [];
$subtotal = 0;
foreach ($cart as $pid => $qty) {
    if (!isset($productsById[$pid])) continue;
    $p = $productsById[$pid];
    $qty = min($qty, max(1, $p['stock']));
    $lineTotal = $p['price'] * $qty;
    $subtotal += $lineTotal;
    $items[] = ['product' => $p, 'qty' => $qty, 'line_total' => $lineTotal];
}
$deliveryFee = ($subtotal > 0 && $subtotal < 499) ? 40 : 0;
$grandTotal = $subtotal + $deliveryFee;

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '') $errors[] = 'Please enter your full name.';
    if ($address === '') $errors[] = 'Please enter a delivery address.';
    if ($phone === '' || !preg_match('/^[0-9]{10}$/', $phone)) $errors[] = 'Please enter a valid 10-digit phone number.';

    if (empty($errors)) {
        $orderNumber = 'AMZ' . date('Ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $fullAddress = $address . ' | Phone: ' . $phone;

        $db->beginTransaction();
        $insOrder = $db->prepare('INSERT INTO orders (order_number, customer_name, address, total, status) VALUES (?, ?, ?, ?, ?)');
        $insOrder->execute([$orderNumber, $name, $fullAddress, $grandTotal, 'Confirmed']);
        $orderId = $db->lastInsertId();

        $insItem = $db->prepare('INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)');
        foreach ($items as $item) {
            $insItem->execute([$orderId, $item['product']['id'], $item['qty'], $item['product']['price']]);
        }
        $db->commit();

        $_SESSION['cart'] = [];
        $_SESSION['last_order'] = $orderNumber;

        header('Location: /order_success.php?order=' . urlencode($orderNumber));
        exit;
    }
}

$pageTitle = 'Checkout';
require __DIR__ . '/includes/header.php';
?>

<div class="section-title"><span>Checkout</span></div>

<?php if (!empty($errors)): ?>
  <div class="alert" style="background:#FDECEA;color:#611A15;border:1px solid #F5C6CB;">
    <?php foreach ($errors as $err): ?><div>⚠ <?= e($err) ?></div><?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="cart-layout">
  <div class="cart-box">
    <h3 style="margin-top:0;">Delivery Address</h3>
    <form method="post" action="/checkout.php" style="display:flex;flex-direction:column;gap:12px;max-width:480px;">
      <label>Full Name
        <input type="text" name="name" required style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"
               value="<?= e($_POST['name'] ?? '') ?>">
      </label>
      <label>Delivery Address
        <textarea name="address" required rows="3" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"><?= e($_POST['address'] ?? '') ?></textarea>
      </label>
      <label>Phone Number
        <input type="text" name="phone" required maxlength="10" placeholder="10-digit mobile number"
               style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"
               value="<?= e($_POST['phone'] ?? '') ?>">
      </label>

      <h3>Payment Method</h3>
      <label><input type="radio" name="payment" value="cod" checked> Cash on Delivery</label>
      <label><input type="radio" name="payment" value="card"> Credit / Debit Card (demo only)</label>
      <label><input type="radio" name="payment" value="upi"> UPI (demo only)</label>

      <button type="submit" class="btn btn-secondary" style="margin-top:10px;">Place Order — <?= formatINR($grandTotal) ?></button>
    </form>
  </div>

  <div class="summary-box">
    <h3 style="margin-top:0;">Order Summary</h3>
    <?php foreach ($items as $item): $p = $item['product']; ?>
      <div class="summary-row">
        <span><?= e($p['name']) ?> × <?= (int)$item['qty'] ?></span>
        <span><?= formatINR($item['line_total']) ?></span>
      </div>
    <?php endforeach; ?>
    <hr class="pd-divider">
    <div class="summary-row"><span>Delivery:</span><span><?= $deliveryFee > 0 ? formatINR($deliveryFee) : 'FREE' ?></span></div>
    <div class="summary-row summary-total"><span>Total:</span><span><?= formatINR($grandTotal) ?></span></div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
