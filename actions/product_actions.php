<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../controllers/product_controller.php';

// Simple router: fn=list|search|filter_cat|filter_brand|single
$fn     = $_GET['fn'] ?? $_POST['fn'] ?? 'list';
$page   = max(1, (int)($_GET['page'] ?? $_POST['page'] ?? 1));
$limit  = max(1, min(20, (int)($_GET['limit'] ?? $_POST['limit'] ?? 10)));
$offset = ($page - 1) * $limit;

try {
    switch ($fn) {
        case 'list': {
                $rows  = view_all_products_ctr($limit, $offset);
                $total = count_all_products_ctr();
                echo json_encode(['status' => 'success', 'data' => $rows, 'total' => $total]);
                break;
            }
        case 'search': {
                $q     = trim($_GET['q'] ?? $_POST['q'] ?? '');
                $rows  = search_products_ctr($q, $limit, $offset);
                $total = count_search_products_ctr($q);
                echo json_encode(['status' => 'success', 'data' => $rows, 'total' => $total]);
                break;
            }
        case 'filter_cat': {
                $cat_id = (int)($_GET['cat_id'] ?? $_POST['cat_id'] ?? 0);
                $rows  = filter_products_by_category_ctr($cat_id, $limit, $offset);
                $total = count_filter_category_ctr($cat_id);
                echo json_encode(['status' => 'success', 'data' => $rows, 'total' => $total]);
                break;
            }
        case 'filter_brand': {
                $brand_id = (int)($_GET['brand_id'] ?? $_POST['brand_id'] ?? 0);
                $rows  = filter_products_by_brand_ctr($brand_id, $limit, $offset);
                $total = count_filter_brand_ctr($brand_id);
                echo json_encode(['status' => 'success', 'data' => $rows, 'total' => $total]);
                break;
            }
        case 'single': {
                $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
                $row = view_single_product_ctr($id);
                echo json_encode(['status' => 'success', 'data' => $row]);
                break;
            }
        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown fn']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error']);
}
