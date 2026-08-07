<?php
require_once __DIR__ . '/functions.php';
$db = getDB();
$navCategories = $db->query('SELECT name, slug, icon FROM categories ORDER BY name')->fetchAll();
$searchQuery = trim($_GET['q'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?>Amaazon.in — Sample Store</title>
<link rel="stylesheet" href="/assets/css/style.css">
<link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🛒</text></svg>">
</head>
<body>

<div class="topbar">
  <div class="topbar-inner">
    <span>Deliver to <strong>Bengaluru 560001</strong></span>
    <span class="topbar-note">Demo store — built with PHP for learning purposes. Not affiliated with Amazon.</span>
  </div>
</div>

<header class="site-header">
  <div class="header-row">
    <a href="/index.php" class="logo">
      <span class="logo-mark">amaazon</span><span class="logo-dot">.in</span>
    </a>

    <div class="deliver-to">
      <div class="deliver-label">Deliver to</div>
      <div class="deliver-place">📍 Bengaluru 560001</div>
    </div>

    <form class="search-form" action="/search.php" method="get">
      <select name="cat" class="search-cat">
        <option value="">All</option>
        <?php foreach ($navCategories as $c): ?>
          <option value="<?= e($c['slug']) ?>" <?= ($_GET['cat'] ?? '') === $c['slug'] ? 'selected' : '' ?>>
            <?= e($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <input type="text" name="q" placeholder="Search products, brands and more"
             value="<?= e($searchQuery) ?>" autocomplete="off">
      <button type="submit" aria-label="Search">🔍</button>
    </form>

    <div class="header-actions">
      <a href="/orders.php" class="header-link">
        <span class="small">Returns</span>
        <span>& Orders</span>
      </a>
      <a href="/cart.php" class="header-link cart-link">
        <span class="cart-icon">🛒<span class="cart-badge"><?= cartCount() ?></span></span>
        <span>Cart</span>
      </a>
    </div>
  </div>

  <nav class="category-nav">
    <a href="/index.php" class="cat-all">☰ All Categories</a>
    <?php foreach ($navCategories as $c): ?>
      <a href="/category.php?slug=<?= e($c['slug']) ?>"><?= e($c['icon'] ?? '') ?> <?= e($c['name']) ?></a>
    <?php endforeach; ?>
  </nav>
</header>

<main class="site-main">
