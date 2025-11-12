<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

if (!isLoggedIn()) {
  echo json_encode(['status' => 'error', 'message' => 'Login required']); exit;
}

$user_id = (int)$_SESSION['user_id'];
$rows = get_user_cart_ctr($user_id);

// Ensure each row includes cart_id and product_id (tweak your CartModel::get_user_cart SELECT if needed)
echo json_encode(['status' => 'success', 'data' => $rows]);
