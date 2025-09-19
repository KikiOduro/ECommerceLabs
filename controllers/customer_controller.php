<?php
require_once __DIR__ . '/../models/customer_model.php';

function login_customer_ctr($email, $password)
{
    $customer = new Customer();
    $row = $customer->loginCustomer($email, $password);
    if ($row) {
        return $row;
    }
    return false;
}

function get_customer_by_email_ctr($email)
{
    $customer = new Customer();
    return $customer->getCustomerByEmail($email);
}
