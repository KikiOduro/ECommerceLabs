<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once __DIR__ . '/../settings/core.php';
require_once __DIR__ . '/../controllers/product_controller.php';

// Ensure only admins can upload
if (!isLoggedIn() || !isAdmin()) {
  echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
  exit;
}

$user_id = (int)$_SESSION['user_id'];
$pid     = (int)($_POST['product_id'] ?? 0);

if ($pid <= 0 || empty($_FILES['image']['tmp_name'])) {
  echo json_encode(['status' => 'error', 'message' => 'Product ID and image are required']);
  exit;
}

// Confirm uploads directory exists
$uploads_root = realpath(__DIR__ . '/../uploads');
if (!$uploads_root) {
  echo json_encode(['status' => 'error', 'message' => 'Uploads folder missing at /uploads (project root)']);
  exit;
}

// File validation rules
$max_bytes   = 5 * 1024 * 1024; // 5MB
$allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Check file size
if ($_FILES['image']['size'] > $max_bytes) {
  echo json_encode(['status' => 'error', 'message' => 'Image too large (max 5MB)']);
  exit;
}

// Validate file extension
$ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowed_ext, true)) {
  echo json_encode(['status' => 'error', 'message' => 'Unsupported file type']);
  exit;
}

// Optional MIME validation
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

// Prepare upload path
$target_dir = $uploads_root . "/u{$user_id}/p{$pid}";
if (!is_dir($target_dir)) {
  mkdir($target_dir, 0775, true);
}

// Verify directory is inside uploads (basic safety check)
if (strpos($target_dir, $uploads_root) !== 0) {
  echo json_encode(['status' => 'error', 'message' => 'Unsafe upload directory']);
  exit;
}

// Build file name
$basename   = 'img_' . uniqid('', true) . '.' . $ext;
$target_abs = $target_dir . '/' . $basename;

// Move uploaded file
if (!move_uploaded_file($_FILES['image']['tmp_name'], $target_abs)) {
  echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file']);
  exit;
}

// Store relative path (for DB)
$relative = '../uploads/u' . $user_id . '/p' . $pid . '/' . $basename;

// Update database
$ok = update_product_image_ctr($user_id, $pid, $relative);

if ($ok) {
  echo json_encode([
    'status' => 'success',
    'path' => $relative,
    'message' => 'Image uploaded successfully'
  ]);
} else {
  echo json_encode([
    'status' => 'error',
    'message' => 'File saved but database update failed'
  ]);
}