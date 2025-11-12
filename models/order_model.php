<?php
require_once __DIR__ . '/../settings/db_class.php';

class OrderModel extends db_connection {

  /** Create order; returns new order_id or false */
  public function create_order(int $customer_id, string $invoice_no, string $status = 'Pending') {
    $sql = "INSERT INTO orders (customer_id, invoice_no, order_date, order_status)
            VALUES (?, ?, NOW(), ?)";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('iss', $customer_id, $invoice_no, $status);
    if ($stmt->execute()) return $stmt->insert_id;
    return false;
  }

  /** Add product line to orderdetails (no price column in your schema) */
  public function add_order_detail(int $order_id, int $product_id, int $qty): bool {
    $sql = "INSERT INTO orderdetails (order_id, product_id, qty)
            VALUES (?, ?, ?)";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('iii', $order_id, $product_id, $qty);
    return $stmt->execute();
  }

  /** Record payment (uses column name 'amt') */
  public function record_payment(int $customer_id, int $order_id, float $amount, string $currency = 'GHS'): bool {
    $sql = "INSERT INTO payment (customer_id, order_id, amt, currency, payment_date)
            VALUES (?, ?, ?, ?, NOW())";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('iids', $customer_id, $order_id, $amount, $currency);
    return $stmt->execute();
  }

  /** Update order status */
  public function set_order_status(int $order_id, string $status): bool {
    $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('si', $status, $order_id);
    return $stmt->execute();
  }

  /** Get a user's orders (latest first) */
  public function get_user_orders(int $customer_id): array {
    $sql = "SELECT * FROM orders WHERE customer_id = ? ORDER BY order_date DESC";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }

  /** Get details for one order, joined with product info (handy for receipts) */
  public function get_order_items(int $order_id): array {
    $sql = "SELECT od.product_id, od.qty, p.product_title, p.product_price, p.product_image
            FROM orderdetails od
            JOIN products p ON p.product_id = od.product_id
            WHERE od.order_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('i', $order_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }
}
