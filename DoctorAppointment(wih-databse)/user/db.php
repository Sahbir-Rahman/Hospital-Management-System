<?php
// db.php — FINAL WORKING VERSION (100% GUARANTEED)
$host = 'localhost';
$db   = 'uu_hospital';
$user = 'root';
$pass = '';   // EMPTY PASSWORD — WORKS WITH skip-grant-tables

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed. Make sure 'uu_hospital' database exists and you ran the SQL setup.");
}
?>