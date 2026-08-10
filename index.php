<<<<<<< HEAD
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
=======
<!DOCTYPE html>
<html>
<head>
    <title>Arithmetic Calculator</title>
</head>
<body>

<h2>PHP Arithmetic Calculator</h2>

<form method="post">

    <label>How many numbers do you want?</label><br>
    <select name="count">
        <option value="2">2 Numbers</option>
        <option value="3">3 Numbers</option>
    </select>

    <br><br>

    <label>Select Operation</label><br>
    <select name="operation">
        <option value="add">Addition (+)</option>
        <option value="sub">Subtraction (-)</option>
        <option value="mul">Multiplication (*)</option>
        <option value="div">Division (/)</option>
    </select>

    <br><br>

    Enter First Number:<br>
    <input type="number" name="num1" required>

    <br><br>

    Enter Second Number:<br>
    <input type="number" name="num2" required>

    <br><br>

    Enter Third Number (Only for 3 Numbers):<br>
    <input type="number" name="num3">

    <br><br>

    <input type="submit" name="submit" value="Calculate">

</form>

<?php

if(isset($_POST['submit']))
{
    $count = $_POST['count'];
    $operation = $_POST['operation'];

    $a = $_POST['num1'];
    $b = $_POST['num2'];
    $c = $_POST['num3'];

    if($count == 2)
    {
        switch($operation)
        {
            case "add":
                $result = $a + $b;
                echo "<h3>Addition = $result</h3>";
                break;

            case "sub":
                $result = $a - $b;
                echo "<h3>Subtraction = $result</h3>";
                break;

            case "mul":
                $result = $a * $b;
                echo "<h3>Multiplication = $result</h3>";
                break;

            case "div":
                if($b != 0)
                    echo "<h3>Division = ".($a/$b)."</h3>";
                else
                    echo "<h3>Division by zero is not allowed.</h3>";
                break;
        }
    }
    else
    {
        switch($operation)
        {
            case "add":
                $result = $a + $b + $c;
                echo "<h3>Addition = $result</h3>";
                break;

            case "sub":
                $result = $a - $b - $c;
                echo "<h3>Subtraction = $result</h3>";
                break;

            case "mul":
                $result = $a * $b * $c;
                echo "<h3>Multiplication = $result</h3>";
                break;

            case "div":
                if($b != 0 && $c != 0)
                    echo "<h3>Division = ".($a/$b/$c)."</h3>";
                else
                    echo "<h3>Division by zero is not allowed.</h3>";
                break;
        }
    }
}

?>

</body>
</html>
>>>>>>> cd1dcffb2ab26d66260814a7571bc5bd54ccb8fc
