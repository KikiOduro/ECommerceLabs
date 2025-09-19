<?php

require_once __DIR__ . '/../settings/db_class.php';


class Customer extends db_connection
{
    private $customer_id;
    private $name;
    private $email;
    private $role;
    private $date_created;
    private $contact;
    private $country;
    private $city;
    private $image;

    public function __construct($customer_id = null)
    {
        parent::db_connect();
        if ($customer_id) {
            $this->customer_id = $customer_id;
            $this->loadCustomer();
        }
    }

    private function loadCustomer($customer_id = null)
    {
        if ($customer_id) {
            $this->customer_id = $customer_id;
        }
        if (!$this->customer_id) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->customer_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row) {
            $this->name         = $row['customer_name'];
            $this->email        = $row['customer_email'];
            $this->role         = $row['user_role'];
            $this->date_created = isset($row['date_created']) ? $row['date_created'] : null;
            $this->contact      = $row['customer_contact'];
            $this->country      = $row['customer_country'] ?? null;
            $this->city         = $row['customer_city'] ?? null;
            $this->image        = $row['customer_image'] ?? null;
            return true;
        }

        return false;
    }

   
    public function getCustomerByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        return $row ?: null;
    }


    public function loginCustomer($email, $password)
    {
        $row = $this->getCustomerByEmail($email);
        if (!$row) {
            return false;
        }

        if (!isset($row['customer_pass']) || !password_verify($password, $row['customer_pass'])) {
            return false; 
        }

        return $row; 
    }

}
