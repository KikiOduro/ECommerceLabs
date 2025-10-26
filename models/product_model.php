<?php
require_once __DIR__ . '/../settings/db_class.php';

class Product extends db_connection
{
    public function __construct(){ parent::db_connect(); }

    // INSERT — uses product_cat, product_brand (NOT category_id/brand_id)
    public function addProduct(int $user_id, int $cat_id, int $brand_id, string $title,
                               float $price, ?string $desc, ?string $keywords){
        $sql = "INSERT INTO products
                   (product_cat, product_brand, product_title, product_price,
                    product_desc, product_keywords, created_by)
                VALUES (?,?,?,?,?,?,?)";
        $st = $this->db->prepare($sql);
        $st->bind_param("iisdssi", $cat_id, $brand_id, $title, $price, $desc, $keywords, $user_id);
        return $st->execute() ? $this->db->insert_id : false;
    }

    // UPDATE — uses product_cat, product_brand
    public function updateProduct(int $user_id, int $pid, int $cat_id, int $brand_id, string $title,
                                  float $price, ?string $desc, ?string $keywords): bool {
        $sql = "UPDATE products
                   SET product_cat=?, product_brand=?, product_title=?, product_price=?,
                       product_desc=?, product_keywords=?
                 WHERE product_id=? AND created_by=?";
        $st = $this->db->prepare($sql);
        $st->bind_param("iisdssii", $cat_id, $brand_id, $title, $price, $desc, $keywords, $pid, $user_id);
        return $st->execute() && $st->affected_rows >= 0;
    }

    // Update image path — column is product_image
    public function updateImagePath(int $user_id, int $pid, string $path): bool {
        $sql = "UPDATE products SET product_image=? WHERE product_id=? AND created_by=?";
        $st  = $this->db->prepare($sql);
        $st->bind_param("sii", $path, $pid, $user_id);
        return $st->execute() && $st->affected_rows >= 0;
    }

    // Fetch for this user (joins categories/brands)
    public function getByUser(int $user_id): array {
        $sql = "SELECT p.product_id, p.product_title, p.product_price,
                       p.product_desc, p.product_image, p.product_keywords, p.created_at,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                  FROM products p
                  JOIN categories c ON c.cat_id = p.product_cat
                  JOIN brands b     ON b.brand_id = p.product_brand
                 WHERE p.created_by = ?
              ORDER BY c.cat_name, b.brand_name, p.product_title";
        $st = $this->db->prepare($sql);
        $st->bind_param("i", $user_id);
        $st->execute();
        return $st->get_result()->fetch_all(MYSQLI_ASSOC);
    }
}
