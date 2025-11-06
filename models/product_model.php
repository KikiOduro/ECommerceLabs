<?php
require_once __DIR__ . '/../settings/db_class.php';

class Product extends db_connection
{
    public function __construct()
    {
        parent::db_connect();
    }

    // INSERT — uses product_cat/product_brand columns
    public function addProduct(
        int $user_id,
        int $cat_id,
        int $brand_id,
        string $title,
        float $price,
        ?string $desc,
        ?string $keywords
    ) {
        $sql = "INSERT INTO products
                   (product_cat, product_brand, product_title, product_price,
                    product_desc, product_keywords, created_by)
                VALUES (?,?,?,?,?,?,?)";
        $st = $this->db->prepare($sql);
        $st->bind_param("iisdssi", $cat_id, $brand_id, $title, $price, $desc, $keywords, $user_id);
        return $st->execute() ? $this->db->insert_id : false;
    }

    // UPDATE
    public function updateProduct(
        int $user_id,
        int $pid,
        int $cat_id,
        int $brand_id,
        string $title,
        float $price,
        ?string $desc,
        ?string $keywords
    ): bool {
        $sql = "UPDATE products
                   SET product_cat=?, product_brand=?, product_title=?, product_price=?,
                       product_desc=?, product_keywords=?
                 WHERE product_id=? AND created_by=?";
        $st = $this->db->prepare($sql);
        $st->bind_param("iisdssii", $cat_id, $brand_id, $title, $price, $desc, $keywords, $pid, $user_id);
        return $st->execute() && $st->affected_rows >= 0;
    }

    // Update image (relative path like uploads/uX/pY/...)
    public function updateImagePath(int $user_id, int $pid, string $path): bool
    {
        $sql = "UPDATE products SET product_image=? WHERE product_id=? AND created_by=?";
        $st  = $this->db->prepare($sql);
        $st->bind_param("sii", $path, $pid, $user_id);
        return $st->execute() && $st->affected_rows >= 0;
    }

    // Admin listing by owner (used in admin/product.php)
    public function getByUser(int $user_id): array
    {
        $sql = "
        SELECT
            p.product_id,
            p.product_cat   AS cat_id,
            p.product_brand AS brand_id,
            p.product_title,
            p.product_price,
            p.product_desc,
            p.product_image,
            p.product_keywords,
            c.cat_name,
            b.brand_name
        FROM products p
        JOIN categories c ON c.cat_id  = p.product_cat
        JOIN brands     b ON b.brand_id = p.product_brand
        WHERE c.created_by = ?
        ORDER BY p.product_id DESC
    ";
        $st = $this->db->prepare($sql);
        $st->bind_param('i', $user_id);
        $st->execute();
        return $st->get_result()->fetch_all(MYSQLI_ASSOC);
    }


    /* ========= STORE FRONT (NEW) ========= */

    public function view_all_products(int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image,
                       p.product_desc, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                  FROM products p
                  JOIN categories c ON c.cat_id = p.product_cat
                  JOIN brands b     ON b.brand_id = p.product_brand
              ORDER BY p.product_id DESC
                 LIMIT ? OFFSET ?";
        $st = $this->db->prepare($sql);
        $st->bind_param("ii", $limit, $offset);
        $st->execute();
        return $st->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function count_all_products(): int
    {
        $res = $this->db->query("SELECT COUNT(*) AS n FROM products");
        return (int)$res->fetch_assoc()['n'];
    }

    public function search_products(string $q, int $limit = 10, int $offset = 0): array
    {
        $qLike = '%' . $q . '%';
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image,
                       p.product_desc, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                  FROM products p
                  JOIN categories c ON c.cat_id = p.product_cat
                  JOIN brands b     ON b.brand_id = p.product_brand
                 WHERE p.product_title LIKE ? OR p.product_keywords LIKE ?
              ORDER BY p.product_id DESC
                 LIMIT ? OFFSET ?";
        $st = $this->db->prepare($sql);
        $st->bind_param("ssii", $qLike, $qLike, $limit, $offset);
        $st->execute();
        return $st->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function count_search_products(string $q): int
    {
        $qLike = '%' . $q . '%';
        $st = $this->db->prepare("SELECT COUNT(*) AS n FROM products WHERE product_title LIKE ? OR product_keywords LIKE ?");
        $st->bind_param("ss", $qLike, $qLike);
        $st->execute();
        return (int)$st->get_result()->fetch_assoc()['n'];
    }

    public function filter_products_by_category(int $cat_id, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image,
                       p.product_desc, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                  FROM products p
                  JOIN categories c ON c.cat_id = p.product_cat
                  JOIN brands b     ON b.brand_id = p.product_brand
                 WHERE p.product_cat = ?
              ORDER BY p.product_id DESC
                 LIMIT ? OFFSET ?";
        $st = $this->db->prepare($sql);
        $st->bind_param("iii", $cat_id, $limit, $offset);
        $st->execute();
        return $st->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function count_filter_category(int $cat_id): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) AS n FROM products WHERE product_cat=?");
        $st->bind_param("i", $cat_id);
        $st->execute();
        return (int)$st->get_result()->fetch_assoc()['n'];
    }

    public function filter_products_by_brand(int $brand_id, int $limit = 10, int $offset = 0): array
    {
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image,
                       p.product_desc, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                  FROM products p
                  JOIN categories c ON c.cat_id = p.product_cat
                  JOIN brands b     ON b.brand_id = p.product_brand
                 WHERE p.product_brand = ?
              ORDER BY p.product_id DESC
                 LIMIT ? OFFSET ?";
        $st = $this->db->prepare($sql);
        $st->bind_param("iii", $brand_id, $limit, $offset);
        $st->execute();
        return $st->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function count_filter_brand(int $brand_id): int
    {
        $st = $this->db->prepare("SELECT COUNT(*) AS n FROM products WHERE product_brand=?");
        $st->bind_param("i", $brand_id);
        $st->execute();
        return (int)$st->get_result()->fetch_assoc()['n'];
    }

    public function view_single_product(int $id): ?array
    {
        $sql = "SELECT p.product_id, p.product_title, p.product_price, p.product_image,
                       p.product_desc, p.product_keywords,
                       c.cat_id, c.cat_name, b.brand_id, b.brand_name
                  FROM products p
                  JOIN categories c ON c.cat_id = p.product_cat
                  JOIN brands b     ON b.brand_id = p.product_brand
                 WHERE p.product_id = ?
                 LIMIT 1";
        $st = $this->db->prepare($sql);
        $st->bind_param("i", $id);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        return $row ?: null;
    }
}
