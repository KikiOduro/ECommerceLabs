<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$cat_id = (int)($_POST['category_id'] ?? 0);
if ($cat_id<=0){ echo json_encode(['status'=>'error','message'=>'Category ID is required']); exit; }

$ok = delete_category_ctr($cat_id, (int)$_SESSION['user_id']);
echo json_encode($ok ? ['status'=>'success','message'=>'Category deleted']
                     : ['status'=>'error','message'=>'Delete failed']);
