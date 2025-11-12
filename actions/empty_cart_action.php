<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';

if (!isLoggedIn()) {
  echo json_encode(['status' => 'error', 'message' => 'Login required']);
  exit;
}

$user_id = $_SESSION['user_id'];
$result = empty_cart_ctr($user_id);

if ($result) {
  echo json_encode(['status' => 'success', 'message' => 'Cart emptied']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'Failed to empty cart']);
}
?>
