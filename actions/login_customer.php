<?php

ini_set('display_errors', '1');
error_reporting(E_ALL);

header('Content-Type: application/json');
session_start();

require_once __DIR__ . '/../controllers/customer_controller.php';

$email    = $_POST['email']    ?? '';
$password = $_POST['password'] ?? '';

if ($email === '' || $password === '') {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required']);
    exit;
}

$result = login_customer_ctr($email, $password);

if ($result) {
    $_SESSION['user_id']    = $result['customer_id'];
    $_SESSION['user_name']  = $result['customer_name'];
    $_SESSION['user_email'] = $result['customer_email'];
    $_SESSION['user_role']  = $result['user_role'] ?? 'user';

    echo json_encode(['status' => 'success', 'message' => 'Login successful']);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid login credentials']);
exit;
