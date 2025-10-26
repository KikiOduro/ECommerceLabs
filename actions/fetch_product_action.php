<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$data = fetch_products_ctr((int)$_SESSION['user_id']);
echo json_encode(['status'=>'success','data'=>$data]);
