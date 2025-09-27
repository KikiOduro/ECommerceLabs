<?php
require_once __DIR__ . '/../settings/db_class.php';

class Category extends db_connection
{
    public function __construct(){ parent::db_connect(); }

    public function addCategory(string $name, int $user_id){
        $sql = "INSERT INTO categories (cat_name, created_by) VALUES (?, ?)";
        $st  = $this->db->prepare($sql);
        $st->bind_param("si", $name, $user_id);
        return $st->execute() ? $this->db->insert_id : false;
    }

    public function getCategoriesByUser(int $user_id): array {
        $sql = "SELECT cat_id, cat_name, created_at FROM categories
                WHERE created_by = ? ORDER BY cat_name ASC";
        $st = $this->db->prepare($sql);
        $st->bind_param("i", $user_id); $st->execute();
        return $st->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function updateCategory(int $cat_id, int $user_id, string $name): bool {
        $sql = "UPDATE categories SET cat_name = ?
                WHERE cat_id = ? AND created_by = ?";
        $st = $this->db->prepare($sql);
        $st->bind_param("sii", $name, $cat_id, $user_id);
        return $st->execute() && $st->affected_rows > 0;
    }

    public function deleteCategory(int $cat_id, int $user_id): bool {
        $sql = "DELETE FROM categories WHERE cat_id = ? AND created_by = ?";
        $st = $this->db->prepare($sql);
        $st->bind_param("ii", $cat_id, $user_id);
        return $st->execute() && $st->affected_rows > 0;
    }

    public function getByName(string $name): ?array {
        $st = $this->db->prepare("SELECT * FROM categories WHERE cat_name = ?");
        $st->bind_param("s", $name); $st->execute();
        $row = $st->get_result()->fetch_assoc();
        return $row ?: null;
    }
}
