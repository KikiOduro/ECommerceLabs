<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

if (!isLoggedIn()) {
  echo json_encode(['status' => 'error', 'message' => 'Login required']);
  exit;
}

$cart_id = (int)($_POST['cart_id'] ?? 0);

if ($cart_id <= 0) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid cart item']);
  exit;
}

$result = remove_from_cart_ctr($cart_id);

if ($result) {
  echo json_encode(['status' => 'success', 'message' => 'Item removed']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to remove item']);
}
?>
