<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$name = trim($_POST['name'] ?? '');
$cat  = (int)($_POST['category_id'] ?? 0);

if ($name === '' || $cat <= 0) {
    echo json_encode(['status'=>'error','message'=>'Brand name and category are required']); exit;
}

$id = add_brand_ctr((int)$_SESSION['user_id'], $cat, $name);
echo json_encode($id ? ['status'=>'success','id'=>$id,'message'=>'Brand added']
                     : ['status'=>'error','message'=>'Duplicate or add failed']);
