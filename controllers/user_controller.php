<?php

require_once '../models/user_model.php';




function register_user_ctr($name, $email, $password, $country, $city, $phone_number, $role, $imagePath = null)
{
    $user = new User();
    $user_id = $user->createUser($name, $email, $password, $country, $city, $phone_number, $role, $imagePath);
    if ($user_id) {
        return $user_id;
    }
    return false;
}

function get_user_by_email_ctr($email)
{
    $user = new User();
    return $user->getUserByEmail($email);
}
