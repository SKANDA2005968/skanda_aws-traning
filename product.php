<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/product_section.php';
$db = getDB();

$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug
                       FROM products p JOIN categories c ON c.id = p.category_id
                       WHERE p.slug = ?');
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    http_response_code(404);
    $pageTitle = 'Product not found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="no-results"><h2>Product not found</h2><p><a href="/index.php">Go back home</a></p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$relatedStmt = $db->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug
                              FROM products p JOIN categories c ON c.id = p.category_id
                              WHERE p.category_id = ? AND p.id != ?
                              ORDER BY RANDOM() LIMIT 4');
$relatedStmt->execute([$product['category_id'], $product['id']]);
$related = $relatedStmt->fetchAll();

$pageTitle = $product['name'];
$addedFlash = isset($_GET['added']);
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb">
  <a href="/index.php">Home</a> ›
  <a href="/category.php?slug=<?= e($product['category_slug']) ?>"><?= e($product['category_name']) ?></a> ›
  <?= e($product['name']) ?>
</div>

<?php if ($addedFlash): ?>
  <div class="alert alert-success">✅ Added to cart. <a href="/cart.php">View cart</a> or keep browsing below.</div>
<?php endif; ?>

<div class="product-detail">
  <div class="pd-image">
    <img src="<?= e(productImage($product['image_seed'], 500)) ?>" alt="<?= e($product['name']) ?>">
    <?php if ($product['is_assured']): ?>
      <p style="margin-top:10px;font-size:12px;color:#2196C6;font-weight:700;">✔ Assured Quality &amp; Genuine Product</p>
    <?php endif; ?>
  </div>

  <div class="pd-info">
    <h1><?= e($product['name']) ?></h1>
    <div class="pd-brand">Visit the <?= e($product['brand']) ?> Store</div>
    <div class="pd-rating">
      <?= e(starRating($product['rating'])) ?> <?= number_format($product['rating'], 1) ?>
      <span class="count"><?= number_format($product['rating_count']) ?> ratings</span>
    </div>
    <hr class="pd-divider">
    <div class="pd-price-row">
      <span class="pd-price-now"><?= formatINR($product['price']) ?></span>
      <span class="price-mrp"><?= formatINR($product['mrp']) ?></span>
      <?php if ($product['discount_percent'] > 0): ?>
        <span class="price-off"><?= (int)$product['discount_percent'] ?>% off</span>
      <?php endif; ?>
    </div>
    <div class="pd-inclusive">Inclusive of all taxes</div>
    <hr class="pd-divider">
    <div class="pd-desc">
      <h3>About this item</h3>
      <p><?= e($product['description']) ?></p>
    </div>
  </div>

  <div class="buybox">
    <div class="price-now"><?= formatINR($product['price']) ?></div>
    <div class="pd-inclusive">Inclusive of all taxes</div>

    <?php if ($product['stock'] > 0): ?>
      <div class="in-stock">In Stock<?= $product['stock'] <= 5 ? ' — only ' . (int)$product['stock'] . ' left' : '' ?></div>
    <?php else: ?>
      <div class="out-stock">Currently unavailable</div>
    <?php endif; ?>

    <div class="delivery">
      <strong>🚚 FREE delivery</strong> tomorrow if ordered within next 4 hrs.
      <strong style="margin-top:6px;">↩ Free replacement</strong> within 7 days of delivery.
    </div>

    <?php if ($product['stock'] > 0): ?>
      <form method="post" action="/cart_action.php">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <input type="hidden" name="redirect" value="/product.php?slug=<?= e($product['slug']) ?>&added=1">
        <label for="qty">Qty:
          <select name="qty" id="qty" class="qty-select">
            <?php for ($i = 1; $i <= min(10, $product['stock']); $i++): ?>
              <option value="<?= $i ?>"><?= $i ?></option>
            <?php endfor; ?>
          </select>
        </label>
        <button type="submit" class="btn btn-block">Add to Cart</button>
      </form>
      <form method="post" action="/cart_action.php" style="margin-top:8px;">
        <input type="hidden" name="action" value="buy_now">
        <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
        <button type="submit" class="btn btn-secondary btn-block">Buy Now</button>
      </form>
    <?php endif; ?>
  </div>
</div>

<?php render_product_section('Related products', $related); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
