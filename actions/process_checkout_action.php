<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/cart_controller.php';
require_once __DIR__ . '/../controllers/order_controller.php';
require_once __DIR__ . '/../controllers/product_controller.php';

if (!isLoggedIn()) {
  echo json_encode(['status' => 'error', 'message' => 'Login required']);
  exit;
}

$user_id = $_SESSION['user_id'];
$currency = 'GHS';

// Step 1: Get cart items
$cart_items = get_user_cart_ctr($user_id);
if (empty($cart_items)) {
  echo json_encode(['status' => 'error', 'message' => 'Your cart is empty']);
  exit;
}

// Step 2: Generate order reference
$order_ref = 'INV-' . strtoupper(uniqid());

// Step 3: Calculate total
$total = 0;
foreach ($cart_items as $item) {
  $total += ($item['qty'] * $item['product_price']);
}

// Step 4: Create order
$order_id = create_order_ctr([
  'customer_id' => $user_id,
  'invoice_no' => $order_ref,
  'total_amount' => $total
]);

if (!$order_id) {
  echo json_encode(['status' => 'error', 'message' => 'Failed to create order']);
  exit;
}

// Step 5: Add order details
foreach ($cart_items as $item) {
  add_order_details_ctr([
    'order_id' => $order_id,
    'product_id' => $item['p_id'],
    'qty' => $item['qty'],
    'price' => $item['product_price']
  ]);
}

// Step 6: Record payment (simulated)
record_payment_ctr([
  'customer_id' => $user_id,
  'order_id' => $order_id,
  'amount' => $total,
  'currency' => $currency,
  'payment_status' => 'Completed'
]);

// Step 7: Empty cart
empty_cart_ctr($user_id);

echo json_encode([
  'status' => 'success',
  'message' => 'Checkout completed successfully',
  'order_id' => $order_id,
  'reference' => $order_ref
]);
?>
