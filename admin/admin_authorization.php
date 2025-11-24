<?php
require_once __DIR__ . '/admin_config.php';

function is_logged_in() {
    return isset($_SESSION['admin_id']);
}

function require_login() {
    if (!is_logged_in()) {
        header('Location: admin_login.php');
        exit;
    }
}

function require_admin_role() {
    if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'admin') {
        die("Access denied.");
    }
}
