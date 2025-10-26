<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$pid    = (int)($_POST['product_id'] ?? 0);
$title  = trim($_POST['title'] ?? '');
$cat_id = (int)($_POST['category_id'] ?? 0);
$brand  = (int)($_POST['brand_id'] ?? 0);
$price  = (float)($_POST['price'] ?? 0);
$desc   = trim($_POST['description'] ?? '');
$kw     = trim($_POST['keyword'] ?? '');

if ($pid<=0 || $title==='' || $cat_id<=0 || $brand<=0) {
  echo json_encode(['status'=>'error','message'=>'All required fields must be provided']); exit;
}

$ok = update_product_ctr((int)$_SESSION['user_id'], $pid, $cat_id, $brand, $title, $price, $desc, $kw);
echo json_encode($ok ? ['status'=>'success','message'=>'Product updated']
                     : ['status'=>'error','message'=>'Update failed']);
