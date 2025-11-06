<?php

// declare(strict_types=1);

// // ---- errors to log (not screen) ----
// ini_set('display_errors', '0');
// ini_set('log_errors', '1');
// error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

// ---- auth ----
if (!isLoggedIn() || !isAdmin()) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
$pid     = (int)($_POST['product_id'] ?? 0);

if ($pid <= 0 || empty($_FILES['image']['tmp_name'])) {
  echo json_encode(['status' => 'error', 'message' => 'Product ID and image are required']);
  exit;
}

// ---- uploads root (must already exist & be writable) ----
$uploads_root = '../uploads'; // /public_html/uploads
if (!$uploads_root || !is_dir($uploads_root) || !is_writable($uploads_root)) {
  echo json_encode(['status' => 'error', 'message' => 'Uploads folder missing or not writable']);
  exit;
}

// ---- validate file ----
$max_bytes   = 5 * 1024 * 1024; // 5MB
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

if ($_FILES['image']['size'] > $max_bytes) {
  echo json_encode(['status' => 'error', 'message' => 'Image too large (max 5MB)']);
  exit;
}

$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed_ext, true)) {
  echo json_encode(['status' => 'error', 'message' => 'Unsupported file type']);
  exit;
}

// MIME check
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
  echo json_encode(['status' => 'error', 'message' => 'Invalid image content']);
  exit;
}

// ---- build a unique filename and save directly in /uploads ----
$basename   = 'img_' . uniqid('', true) . '.' . $ext;
$target_abs = $uploads_root . '/' . $basename;    // absolute FS path
$relative   = '../uploads/' . $basename;         // what we store in DB

if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_abs)) {
  echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
  exit;
}

// ---- update product record with relative path ----
$ok = update_product_image_ctr($user_id, $pid, $relative);

if ($ok) {
  echo json_encode(['status' => 'success', 'path' => $relative, 'message' => 'Image uploaded successfully']);
} else {
  echo json_encode(['status' => 'error', 'message' => 'File saved but database update failed']);
}