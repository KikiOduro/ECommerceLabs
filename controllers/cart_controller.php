<?php
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../models/cart_model.php';

function add_to_cart_ctr(int $customer_id, int $product_id, int $qty = 1): bool {
  $cart = new CartModel();
  return $cart->add_or_increment($customer_id, $product_id, $qty);
}

function update_cart_item_ctr(int $cart_id, int $qty): bool {
  if (!isLoggedIn()) return false;
  $cart = new CartModel();
  return $cart->update_quantity_by_cart_id($cart_id, $qty);
}

function remove_from_cart_ctr(int $cart_id): bool {
  if (!isLoggedIn()) return false;
  $cart = new CartModel();
  return $cart->remove_item_by_cart_id($cart_id);
}

function get_user_cart_ctr(int $customer_id): array {
  $cart = new CartModel();
  return $cart->get_user_cart($customer_id);
}

function empty_cart_ctr(int $customer_id): bool {
  $cart = new CartModel();
  return $cart->empty_cart($customer_id);
}

function cart_totals_ctr(int $customer_id): array {
  $items = get_user_cart_ctr($customer_id);
  $subtotal = 0.0;
  foreach ($items as $it) $subtotal += (float)$it['product_price'] * (int)$it['qty'];
  return ['items'=>$items, 'subtotal'=>round($subtotal,2), 'total'=>round($subtotal,2)];
}
