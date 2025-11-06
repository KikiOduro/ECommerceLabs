<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

if (!isLoggedIn() || !isAdmin()) {
  echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit;
}

$title  = trim($_POST['title'] ?? '');
$cat_id = (int)($_POST['category_id'] ?? 0);
$brand  = (int)($_POST['brand_id'] ?? 0);
$price  = (float)($_POST['price'] ?? 0);
$desc   = trim($_POST['description'] ?? '');
$kw     = trim($_POST['keyword'] ?? '');

if ($title==='' || $cat_id<=0 || $brand<=0) {
  echo json_encode(['status'=>'error','message'=>'Title, category, and brand are required']); exit;
}

$id = add_product_ctr((int)$_SESSION['user_id'], $cat_id, $brand, $title, $price, $desc, $kw);

if ($id) {
  echo json_encode(['status'=>'success','id'=>$id,'message'=>'Product added']); 
} else {
  // DEBUG: show last MySQL error so we know exactly why it failed
  $err = 'Unknown DB error';
  if (class_exists('mysqli')) {
    // Try to read from a new connection so we can surface the error
    include_once __DIR__ . '/../settings/db_class.php';
    $tmp = new db_connection();
    if ($tmp->db_conn()) { $err = mysqli_error($tmp->db); }
  }
  echo json_encode(['status'=>'error','message'=>'Add failed','debug'=>$err]);
}
