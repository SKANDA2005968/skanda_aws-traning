<?php
function render_product_card($p) {
    $outOfStock = $p['stock'] <= 0;
    ?>
    <div class="product-card">
      <div class="badge-row">
        <?php if ($p['is_bestseller']): ?><span class="badge badge-bestseller">Bestseller</span><?php endif; ?>
        <?php if ($p['discount_percent'] >= 20): ?><span class="badge badge-off"><?= (int)$p['discount_percent'] ?>% off</span><?php endif; ?>
      </div>
      <a href="/product.php?slug=<?= e($p['slug']) ?>">
        <img class="product-thumb" src="<?= e(productImage($p['image_seed'])) ?>" alt="<?= e($p['name']) ?>" loading="lazy">
      </a>
      <a href="/product.php?slug=<?= e($p['slug']) ?>">
        <div class="product-name"><?= e($p['name']) ?></div>
      </a>
      <div class="product-brand"><?= e($p['brand']) ?></div>
      <div class="product-rating">
        <?= e(starRating($p['rating'])) ?> <span class="count">(<?= number_format($p['rating_count']) ?>)</span>
      </div>
      <div class="product-price-row">
        <span class="price-now"><?= formatINR($p['price']) ?></span>
        <span class="price-mrp"><?= formatINR($p['mrp']) ?></span>
        <?php if ($p['discount_percent'] > 0): ?><span class="price-off"><?= (int)$p['discount_percent'] ?>% off</span><?php endif; ?>
      </div>
      <?php if ($outOfStock): ?>
        <div class="stock-out">Currently unavailable</div>
        <button class="btn btn-block" disabled style="opacity:.5;cursor:not-allowed;">Add to Cart</button>
      <?php else: ?>
        <?php if ($p['stock'] <= 5): ?><div class="stock-low">Only <?= (int)$p['stock'] ?> left in stock</div><?php endif; ?>
        <form method="post" action="/cart_action.php">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= (int)$p['id'] ?>">
          <input type="hidden" name="redirect" value="<?= e($_SERVER['REQUEST_URI']) ?>">
          <button type="submit" class="btn btn-block">Add to Cart</button>
        </form>
      <?php endif; ?>
    </div>
    <?php
}

function render_product_section($title, $products, $viewAllUrl = null) {
    if (empty($products)) return;
    ?>
    <div class="section-title">
      <span><?= htmlspecialchars($title) ?></span>
      <?php if ($viewAllUrl): ?><a href="<?= htmlspecialchars($viewAllUrl) ?>">View all</a><?php endif; ?>
    </div>
    <div class="product-grid">
      <?php foreach ($products as $p) render_product_card($p); ?>
    </div>
    <?php
}
