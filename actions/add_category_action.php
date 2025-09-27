<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/category_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$name = trim($_POST['name'] ?? '');
if ($name===''){ echo json_encode(['status'=>'error','message'=>'Name is required']); exit; }

$id = add_category_ctr($name, (int)$_SESSION['user_id']);
echo json_encode($id ? ['status'=>'success','id'=>$id,'message'=>'Category added']
                     : ['status'=>'error','message'=>'Name exists or add failed']);
