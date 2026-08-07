<?php
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function formatINR($amount) {
    return '₹' . number_format($amount, 0);
}

function productImage($seed, $size = 300) {
    // Stable placeholder images per product, no API key needed.
    return "https://picsum.photos/seed/product{$seed}/{$size}/{$size}";
}

function starRating($rating) {
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    return str_repeat('★', $full) . ($half ? '½' : '') . str_repeat('☆', $empty);
}

function getCart() {
    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    return $_SESSION['cart'];
}

function cartCount() {
    $cart = getCart();
    return array_sum($cart);
}

function addToCart($productId, $qty = 1) {
    $cart = getCart();
    $productId = (int) $productId;
    $qty = max(1, (int) $qty);
    $cart[$productId] = ($cart[$productId] ?? 0) + $qty;
    $_SESSION['cart'] = $cart;
}

function setCartQty($productId, $qty) {
    $cart = getCart();
    $productId = (int) $productId;
    $qty = (int) $qty;
    if ($qty <= 0) {
        unset($cart[$productId]);
    } else {
        $cart[$productId] = $qty;
    }
    $_SESSION['cart'] = $cart;
}

function removeFromCart($productId) {
    $cart = getCart();
    unset($cart[(int) $productId]);
    $_SESSION['cart'] = $cart;
}

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
