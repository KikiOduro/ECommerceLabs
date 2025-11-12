<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

if (!isLoggedIn()) {
  echo json_encode(['status' => 'error', 'message' => 'Login required']);
  exit;
}

$cart_id = (int)($_POST['cart_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);

if ($cart_id <= 0 || $qty <= 0) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid quantity or item']);
  exit;
}

$result = update_cart_item_ctr($cart_id, $qty);

if ($result) {
  echo json_encode(['status' => 'success', 'message' => 'Quantity updated']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to update quantity']);
}
?>
