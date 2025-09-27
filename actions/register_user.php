<?php
header('Content-Type: application/json');
session_start();


ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

$response = [];

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'status'  => 'error',
        'message' => 'You are already logged in'
    ]);
    exit;
}

require_once '../controllers/user_controller.php';

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$country = $_POST['country'];
$city = $_POST['city'];
$phone_number = $_POST['phone_number'];
$role = $_POST['role']  ?? '2';

$imagePath = null;
if (!empty($_FILES['image']['name'])) {
    $imagePath = $_FILES['image']['name'];
}

$user_id = register_user_ctr(
    $name,
    $email,
    $password,
    $country,
    $city,
    $phone_number,
    $role,
    $imagePath
);

if ($user_id) {
    echo json_encode([
        'status'  => 'success',
        'message' => 'Registered successfully',
        'user_id' => $user_id
    ]);
} else {
    echo json_encode([
        'status'  => 'error',
        'message' => 'Failed to register'
    ]);
}
