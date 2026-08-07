<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Online Shopping Site';
$db = getDB();

$categories = $db->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$bestsellers = $db->query('SELECT p.*, c.name AS category_name, c.slug AS category_slug
                            FROM products p JOIN categories c ON c.id = p.category_id
                            WHERE p.is_bestseller = 1 ORDER BY RANDOM() LIMIT 8')->fetchAll();
$deals = $db->query('SELECT p.*, c.name AS category_name, c.slug AS category_slug
                      FROM products p JOIN categories c ON c.id = p.category_id
                      WHERE p.discount_percent >= 30 ORDER BY p.discount_percent DESC LIMIT 8')->fetchAll();
$fresh = $db->query('SELECT p.*, c.name AS category_name, c.slug AS category_slug
                      FROM products p JOIN categories c ON c.id = p.category_id
                      ORDER BY RANDOM() LIMIT 8')->fetchAll();

require __DIR__ . '/includes/header.php';
?>

<section class="hero-banner" id="top">
  <div>
    <h1>Big Savings Days are here 🎉</h1>
    <p>Top deals on mobiles, electronics, fashion and more. New offers added every day across every category.</p>
  </div>
  <div class="hero-badge">Up to 70% OFF</div>
</section>

<div class="category-strip">
  <?php foreach ($categories as $c): ?>
    <a href="/category.php?slug=<?= e($c['slug']) ?>" class="category-card">
      <div class="icon"><?= e($c['icon']) ?></div>
      <div class="name"><?= e($c['name']) ?></div>
    </a>
  <?php endforeach; ?>
</div>

<?php include __DIR__ . '/includes/product_section.php';
render_product_section('🔥 Today\'s Deals', $deals); ?>

<?php render_product_section('⭐ Bestsellers', $bestsellers); ?>

<?php render_product_section('Just for You', $fresh); ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
