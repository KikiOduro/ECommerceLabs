<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$brand_id = (int)($_POST['brand_id'] ?? 0);
if ($brand_id <= 0) { echo json_encode(['status'=>'error','message'=>'Brand ID is required']); exit; }

$ok = delete_brand_ctr((int)$_SESSION['user_id'], $brand_id);
echo json_encode($ok ? ['status'=>'success','message'=>'Brand deleted']
                     : ['status'=>'error','message'=>'Delete failed']);
