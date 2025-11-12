<?php
require_once __DIR__ . '/../models/order_model.php';
require_once __DIR__ . '/../controllers/cart_controller.php'; // to reuse cart + totals

/**
 * ORDER CONTROLLER
 * Wraps OrderModel for actions and checkout orchestration.
 */

function create_order_ctr(array $params) {
  $order = new OrderModel();
  $customer_id  = (int)($params['customer_id'] ?? 0);
  $invoice_no   = (string)($params['invoice_no'] ?? '');
  $status       = (string)($params['status'] ?? 'Pending');

  if ($customer_id <= 0 || $invoice_no === '') return false;
  return $order->create_order($customer_id, $invoice_no, $status);
}

function add_order_details_ctr(array $params): bool {
  $order = new OrderModel();
  $order_id   = (int)($params['order_id'] ?? 0);
  $product_id = (int)($params['product_id'] ?? 0);
  $qty        = (int)($params['qty'] ?? 0);

  if ($order_id <= 0 || $product_id <= 0 || $qty <= 0) return false;
  return $order->add_order_detail($order_id, $product_id, $qty);
}

function record_payment_ctr(array $params): bool {
  $order = new OrderModel();
  $customer_id = (int)($params['customer_id'] ?? 0);
  $order_id    = (int)($params['order_id'] ?? 0);
  $amount      = (float)($params['amount'] ?? 0.0);
  $currency    = (string)($params['currency'] ?? 'GHS');

  if ($customer_id <= 0 || $order_id <= 0 || $amount <= 0) return false;
  return $order->record_payment($customer_id, $order_id, $amount, $currency);
}

function set_order_status_ctr(int $order_id, string $status): bool {
  $order = new OrderModel();
  return $order->set_order_status($order_id, $status);
}

function get_user_orders_ctr(int $customer_id): array {
  $order = new OrderModel();
  return $order->get_user_orders($customer_id);
}

/**
 * Optional: one-call checkout orchestrator if you ever want to use it.
 * - Calculates totals from the cart
 * - Creates order
 * - Inserts orderdetails
 * - Records simulated payment
 * - Empties cart
 * Returns ['ok'=>bool,'order_id'=>?,'ref'=>?,'msg'=>string]
 */
function checkout_orchestrate_ctr(int $customer_id, string $currency = 'GHS'): array {
  // 1) gather cart + totals
  $acc = cart_totals_ctr($customer_id);
  $items = $acc['items'] ?? [];
  $total = (float)($acc['total'] ?? 0.0);
  if (!$items || $total <= 0) {
    return ['ok' => false, 'msg' => 'Cart is empty'];
  }

  // 2) create order
  $ref = 'INV-' . strtoupper(uniqid());
  $order_id = create_order_ctr([
    'customer_id' => $customer_id,
    'invoice_no'  => $ref,
    'status'      => 'Pending',
  ]);
  if (!$order_id) {
    return ['ok' => false, 'msg' => 'Failed to create order'];
  }

  // 3) add details
  foreach ($items as $it) {
    $ok = add_order_details_ctr([
      'order_id'   => $order_id,
      'product_id' => (int)$it['product_id'],
      'qty'        => (int)$it['qty'],
    ]);
    if (!$ok) {
      return ['ok' => false, 'msg' => 'Failed to add order line'];
    }
  }

  // 4) simulated payment
  $payed = record_payment_ctr([
    'customer_id' => $customer_id,
    'order_id'    => $order_id,
    'amount'      => $total,
    'currency'    => $currency,
  ]);
  if (!$payed) {
    return ['ok' => false, 'msg' => 'Payment record failed'];
  }

  // 5) mark order status + empty cart
  set_order_status_ctr($order_id, 'Completed');
  empty_cart_ctr($customer_id);

  return ['ok' => true, 'order_id' => $order_id, 'ref' => $ref, 'msg' => 'Checkout complete'];
}
