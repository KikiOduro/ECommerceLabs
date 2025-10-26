<?php
require_once __DIR__ . '/../models/brand_model.php';

function add_brand_ctr(int $user_id, int $category_id, string $name){
    $m = new Brand();
    // prevent duplicates in same category for this user
    if ($m->getByNameForUserCategory($user_id, $category_id, $name)) return false;
    return $m->addBrand($user_id, $category_id, $name);
}

function fetch_brands_grouped_ctr(int $user_id){
    $m = new Brand();
    return $m->getBrandsGroupedByCategory($user_id);
}

function update_brand_ctr(int $user_id, int $brand_id, string $name){
    $m = new Brand();
    return $m->updateBrand($user_id, $brand_id, $name);
}

function delete_brand_ctr(int $user_id, int $brand_id){
    $m = new Brand();
    return $m->deleteBrand($user_id, $brand_id);
}
