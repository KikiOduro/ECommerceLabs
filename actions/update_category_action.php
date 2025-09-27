<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$cat_id = (int)($_POST['category_id'] ?? 0);
$name   = trim($_POST['name'] ?? '');
if ($cat_id<=0 || $name===''){ echo json_encode(['status'=>'error','message'=>'Category ID and name are required']); exit; }

$ok = update_category_ctr($cat_id, (int)$_SESSION['user_id'], $name);
echo json_encode($ok ? ['status'=>'success','message'=>'Category updated']
                     : ['status'=>'error','message'=>'Name exists or update failed']);
