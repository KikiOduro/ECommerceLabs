<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

echo json_encode(['status'=>'success','data'=>fetch_brands_grouped_ctr((int)$_SESSION['user_id'])]);
