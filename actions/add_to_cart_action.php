<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

if (!isLoggedIn()) {
  echo json_encode(['status' => 'error', 'message' => 'Login required']);
  exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)($_POST['product_id'] ?? 0);
$qty = (int)($_POST['qty'] ?? 1);

if ($product_id <= 0) {
  echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
  exit;
}

$result = add_to_cart_ctr($user_id, $product_id, $qty);

if ($result) {
  echo json_encode(['status' => 'success', 'message' => 'Item added to cart']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to add item']);
}
?>
