<?php
require_once __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$action = $_POST['action'] ?? '';
$productId = (int) ($_POST['product_id'] ?? 0);

switch ($action) {
    case 'add':
        $qty = (int) ($_POST['qty'] ?? 1);
        addToCart($productId, $qty > 0 ? $qty : 1);
        break;

    case 'buy_now':
        addToCart($productId, 1);
        header('Location: /cart.php');
        exit;

    case 'update':
        $qty = (int) ($_POST['qty'] ?? 1);
        setCartQty($productId, $qty);
        break;

    case 'remove':
        removeFromCart($productId);
        break;
}

$redirect = $_POST['redirect'] ?? '/cart.php';
// Basic safety: only allow local redirects
if (!str_starts_with($redirect, '/')) {
    $redirect = '/cart.php';
}
header('Location: ' . $redirect);
exit;
