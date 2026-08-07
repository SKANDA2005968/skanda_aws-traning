<?php
/**
 * Database connection
 * Uses SQLite so the project runs with zero setup.
 * To use MySQL instead, see the commented block below.
 */

function getDB() {
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $dbFile = __DIR__ . '/../data/store.db';
    $needsSeed = !file_exists($dbFile);

    try {
        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->exec('PRAGMA foreign_keys = ON');
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }

    if ($needsSeed) {
        require_once __DIR__ . '/../database/seed.php';
        seedDatabase($pdo);
    }

    return $pdo;
}

/* --------------------------------------------------------------
   MYSQL VERSION (optional) — replace getDB() above with this if
   you prefer MySQL. Create the schema first using
   database/schema_mysql.sql, then run database/seed_mysql.php once.

function getDB() {
    static $pdo = null;
    if ($pdo !== null) return $pdo;
    $host = 'localhost';
    $db   = 'amazon_clone';
    $user = 'root';
    $pass = '';
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}
-------------------------------------------------------------- */
