<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/brand_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$brand_id = (int)($_POST['brand_id'] ?? 0);
$name     = trim($_POST['name'] ?? '');

if ($brand_id <= 0 || $name === '') {
    echo json_encode(['status'=>'error','message'=>'Brand ID and name are required']); exit;
}

$ok = update_brand_ctr((int)$_SESSION['user_id'], $brand_id, $name);
echo json_encode($ok ? ['status'=>'success','message'=>'Brand updated']
                     : ['status'=>'error','message'=>'Update failed (maybe duplicate name)']);
