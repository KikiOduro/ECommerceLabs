<?php
require_once __DIR__ . '/../settings/db_class.php';

class CartModel extends db_connection {

  /** Add new OR increment existing item (by c_id + p_id) */
  public function add_or_increment(int $customer_id, int $product_id, int $qty = 1): bool {
    if (!$this->db_conn()) return false;

    // check existing
    $sql = "SELECT qty FROM cart WHERE c_id = ? AND p_id = ? LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('ii', $customer_id, $product_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
      $newQty = max(1, (int)$row['qty'] + $qty);
      $sql2 = "UPDATE cart SET qty = ? WHERE c_id = ? AND p_id = ?";
      $stmt2 = $this->db->prepare($sql2);
      $stmt2->bind_param('iii', $newQty, $customer_id, $product_id);
      return $stmt2->execute();
    }

    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $sql3 = "INSERT INTO cart (p_id, ip_add, c_id, qty, date_added) VALUES (?, ?, ?, ?, NOW())";
    $stmt3 = $this->db->prepare($sql3);
    $stmt3->bind_param('isii', $product_id, $ip, $customer_id, $qty);
    return $stmt3->execute();
  }

  /** Set a specific quantity using cart_id */
  public function update_quantity_by_cart_id(int $cart_id, int $qty): bool {
    if (!$this->db_conn()) return false;
    $qty = max(1, $qty);
    $sql = "UPDATE cart SET qty = ? WHERE cart_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('ii', $qty, $cart_id);
    return $stmt->execute();
  }

  /** Remove a single item by cart_id */
  public function remove_item_by_cart_id(int $cart_id): bool {
    if (!$this->db_conn()) return false;
    $sql = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('i', $cart_id);
    return $stmt->execute();
  }

  /** Fetch all items for a user (includes real cart_id) */
  public function get_user_cart(int $customer_id): array {
    if (!$this->db_conn()) return [];

    $sql = "SELECT 
              c.cart_id,
              c.p_id               AS product_id,
              c.qty,
              p.product_title,
              p.product_price,
              p.product_image
            FROM cart c
            JOIN products p ON p.product_id = c.p_id
            WHERE c.c_id = ?
            ORDER BY p.product_title";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    foreach ($rows as &$r) {
      $r['subtotal'] = (float)$r['product_price'] * (int)$r['qty'];
    }
    return $rows;
  }

  /** Empty the entire cart by customer */
  public function empty_cart(int $customer_id): bool {
    if (!$this->db_conn()) return false;
    $sql = "DELETE FROM cart WHERE c_id = ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('i', $customer_id);
    return $stmt->execute();
  }

  /** Optional: find by (c_id, p_id) */
  public function find_item(int $customer_id, int $product_id): ?array {
    if (!$this->db_conn()) return null;
    $sql = "SELECT cart_id, qty FROM cart WHERE c_id = ? AND p_id = ? LIMIT 1";
    $stmt = $this->db->prepare($sql);
    $stmt->bind_param('ii', $customer_id, $product_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    return $row ?: null;
  }
}
