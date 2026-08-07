<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/product_section.php';
$db = getDB();

$q = trim($_GET['q'] ?? '');
$catSlug = trim($_GET['cat'] ?? '');

$sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p JOIN categories c ON c.id = p.category_id
        WHERE 1=1";
$params = [];

if ($q !== '') {
    $sql .= " AND (p.name LIKE ? OR p.brand LIKE ? OR p.description LIKE ?)";
    $like = '%' . $q . '%';
    $params = [$like, $like, $like];
}
if ($catSlug !== '') {
    $sql .= " AND c.slug = ?";
    $params[] = $catSlug;
}
$sql .= " ORDER BY p.rating_count DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$pageTitle = 'Search: ' . $q;
require __DIR__ . '/includes/header.php';
?>

<div class="results-count">
  <?= count($products) ?> results <?= $q !== '' ? 'for "' . e($q) . '"' : '' ?>
</div>

<?php if (empty($products)): ?>
  <div class="no-results">
    <h3>No results found<?= $q !== '' ? ' for "' . e($q) . '"' : '' ?></h3>
    <p>Try checking your spelling or use more general terms.</p>
  </div>
<?php else: ?>
  <div class="product-grid">
    <?php foreach ($products as $p) render_product_card($p); ?>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
