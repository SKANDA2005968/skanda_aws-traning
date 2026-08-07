<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/product_section.php';
$db = getDB();

$slug = $_GET['slug'] ?? '';
$stmt = $db->prepare('SELECT * FROM categories WHERE slug = ?');
$stmt->execute([$slug]);
$category = $stmt->fetch();

if (!$category) {
    http_response_code(404);
    $pageTitle = 'Category not found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="no-results"><h2>Category not found</h2><p><a href="/index.php">Go back home</a></p></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$sort = $_GET['sort'] ?? 'popularity';
$orderBy = match ($sort) {
    'price_low'  => 'p.price ASC',
    'price_high' => 'p.price DESC',
    'rating'     => 'p.rating DESC',
    'discount'   => 'p.discount_percent DESC',
    default      => 'p.rating_count DESC',
};

$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (float)$_GET['max_price'] : null;

$sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p JOIN categories c ON c.id = p.category_id
        WHERE p.category_id = ?";
$params = [$category['id']];
if ($maxPrice !== null) {
    $sql .= " AND p.price <= ?";
    $params[] = $maxPrice;
}
$sql .= " ORDER BY $orderBy";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = $category['name'];
require __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb"><a href="/index.php">Home</a> › <?= e($category['name']) ?></div>

<div class="section-title"><span><?= e($category['icon']) ?> <?= e($category['name']) ?></span></div>

<form class="filters-bar" method="get" action="/category.php">
  <input type="hidden" name="slug" value="<?= e($slug) ?>">
  <label>Sort by:
    <select name="sort" onchange="this.form.submit()">
      <option value="popularity" <?= $sort === 'popularity' ? 'selected' : '' ?>>Popularity</option>
      <option value="price_low" <?= $sort === 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
      <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
      <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Customer Rating</option>
      <option value="discount" <?= $sort === 'discount' ? 'selected' : '' ?>>Discount</option>
    </select>
  </label>
  <label>Max price:
    <select name="max_price" onchange="this.form.submit()">
      <option value="">Any</option>
      <?php foreach ([500, 1000, 2000, 5000, 10000, 25000, 50000] as $mp): ?>
        <option value="<?= $mp ?>" <?= $maxPrice == $mp ? 'selected' : '' ?>>Under <?= formatINR($mp) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <noscript><button type="submit" class="btn">Apply</button></noscript>
</form>

<div class="results-count"><?= count($products) ?> results in <?= e($category['name']) ?></div>

<?php if (empty($products)): ?>
  <div class="no-results">No products match these filters. Try a different price range.</div>
<?php else: ?>
  <div class="product-grid">
    <?php foreach ($products as $p) render_product_card($p); ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
