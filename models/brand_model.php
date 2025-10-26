<?php
require_once __DIR__ . '/../settings/db_class.php';

class Brand extends db_connection
{
    public function __construct(){ parent::db_connect(); }

    public function addBrand(int $user_id, int $category_id, string $name){
        $sql = "INSERT INTO brands (brand_name, category_id, created_by) VALUES (?, ?, ?)";
        $st  = $this->db->prepare($sql);
        $st->bind_param("sii", $name, $category_id, $user_id);
        return $st->execute() ? $this->db->insert_id : false;
    }

    public function getBrandsByUser(int $user_id): array {
        $sql = "SELECT b.brand_id, b.brand_name, b.created_at,
                       c.cat_id, c.cat_name
                  FROM brands b
                  JOIN categories c ON c.cat_id = b.category_id
                 WHERE b.created_by = ?
              ORDER BY c.cat_name ASC, b.brand_name ASC";
        $st = $this->db->prepare($sql);
        $st->bind_param("i", $user_id); $st->execute();
        return $st->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getBrandsGroupedByCategory(int $user_id): array {
        $rows = $this->getBrandsByUser($user_id);
        $out  = [];
        foreach ($rows as $r) {
            $key = $r['cat_id'];
            if (!isset($out[$key])) {
                $out[$key] = [
                    'cat_id'   => $r['cat_id'],
                    'cat_name' => $r['cat_name'],
                    'brands'   => []
                ];
            }
            $out[$key]['brands'][] = [
                'brand_id'   => $r['brand_id'],
                'brand_name' => $r['brand_name'],
                'created_at' => $r['created_at']
            ];
        }
        // return as indexed array for easy JSON
        return array_values($out);
    }

    public function updateBrand(int $user_id, int $brand_id, string $name): bool {
        $sql = "UPDATE brands SET brand_name = ?
                WHERE brand_id = ? AND created_by = ?";
        $st = $this->db->prepare($sql);
        $st->bind_param("sii", $name, $brand_id, $user_id);
        return $st->execute() && $st->affected_rows > 0;
    }

    public function deleteBrand(int $user_id, int $brand_id): bool {
        $sql = "DELETE FROM brands WHERE brand_id = ? AND created_by = ?";
        $st  = $this->db->prepare($sql);
        $st->bind_param("ii", $brand_id, $user_id);
        return $st->execute() && $st->affected_rows > 0;
    }

    public function getByNameForUserCategory(int $user_id, int $category_id, string $name): ?array {
        $sql = "SELECT * FROM brands
                 WHERE created_by = ? AND category_id = ? AND brand_name = ?";
        $st = $this->db->prepare($sql);
        $st->bind_param("iis", $user_id, $category_id, $name);
        $st->execute();
        $row = $st->get_result()->fetch_assoc();
        return $row ?: null;
    }
}
