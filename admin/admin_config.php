<?php
session_start();
$DB_HOST = 'localhost';
$DB_NAME = 'bat_cafe';
$DB_USER = 'root';
$DB_PASS = '';
$DB_PORT = 3307;

try {
    $pdo = new PDO(
        "mysql:host=$DB_HOST;dbname=$DB_NAME;port=$DB_PORT;charset=utf8mb4",
        $DB_USER,
        $DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>
