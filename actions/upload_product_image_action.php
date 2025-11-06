<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

if (!isLoggedIn() || !isAdmin()) { echo json_encode(['status'=>'error','message'=>'Unauthorized']); exit; }

$user_id = (int)$_SESSION['user_id'];
$pid     = (int)($_POST['product_id'] ?? 0);

if ($pid <= 0 || empty($_FILES['image']['tmp_name'])) {
  echo json_encode(['status'=>'error','message'=>'Product ID and image are required']); exit;
}

$uploads_root = __DIR__ . '/../uploads';      // must already exist per assignment
if (!is_dir($uploads_root)) {
  echo json_encode(['status'=>'error','message'=>'Uploads folder missing at /uploads (project root)']); exit;
}

$max_bytes   = 5 * 1024 * 1024;               // 5MB
$allowed_ext = ['jpg','jpeg','png','gif','webp'];

if ($_FILES['image']['size'] > $max_bytes) {
  echo json_encode(['status'=>'error','message'=>'Image too large (max 5MB)']); exit;
}

$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed_ext, true)) {
  echo json_encode(['status'=>'error','message'=>'Unsupported file type']); exit;
}

// MIME check (finfo fallback)
$mime_ok = false;
if (class_exists('finfo')) {
  $f = new finfo(FILEINFO_MIME_TYPE);
  $mime = $f->file($_FILES['image']['tmp_name']) ?: '';
  $mime_ok = (bool)preg_match('#^image/(jpe?g|png|gif|webp)$#i', $mime);
} else {
  $info = @getimagesize($_FILES['image']['tmp_name']);
  $mime_ok = $info && isset($info['mime']) && preg_match('#^image/(jpe?g|png|gif|webp)$#i', $info['mime']);
}
if (!$mime_ok) {
  echo json_encode(['status'=>'error','message'=>'Invalid image content']); exit;
}

// Build safe target: uploads/u{user}/p{pid}/img_xxx.ext
$target_dir = $uploads_root . "/u{$user_id}/p{$pid}";
if (!is_dir($target_dir)) { mkdir($target_dir, 0775, true); }

$basename   = 'img_' . uniqid('', true) . '.' . $ext;
$target_abs = $target_dir . '/' . $basename;

// SECURITY: verify final directory resolves inside /uploads
$uploads_real = realpath($uploads_root);
$final_dir    = realpath($target_dir); // exists because we just mkdir
if ($uploads_real === false || $final_dir === false || strpos($final_dir, $uploads_real) !== 0) {
  echo json_encode(['status'=>'error','message'=>'Unsafe path']); exit;
}

if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_abs)) {
  echo json_encode(['status'=>'error','message'=>'Move upload failed']); exit;
}

// store relative path
$relative = '../uploads/u' . $user_id . '/p' . $pid . '/' . $basename;

$ok = update_product_image_ctr($user_id, $pid, $relative);
echo json_encode($ok ? ['status'=>'success','path'=>$relative,'message'=>'Image uploaded']
                     : ['status'=>'error','message'=>'Saved file, but DB update failed']);
