<?php
require_once __DIR__ . '/../models/product_model.php';

function add_product_ctr($user_id, $category_id, $brand_id, $title, $price, $description, $keyword){
    $m = new Product();
    return $m->addProduct((int)$user_id, (int)$category_id, (int)$brand_id, trim($title),
                          (float)$price, $description ?: null, $keyword ?: null);
}

function update_product_ctr($user_id, $product_id, $category_id, $brand_id, $title, $price, $description, $keyword){
    $m = new Product();
    return $m->updateProduct((int)$user_id, (int)$product_id, (int)$category_id, (int)$brand_id, trim($title),
                             (float)$price, $description ?: null, $keyword ?: null);
}

function update_product_image_ctr($user_id, $product_id, $path){
    $m = new Product();
    return $m->updateImagePath((int)$user_id, (int)$product_id, $path);
}

function fetch_products_ctr($user_id){
    $m = new Product();
    return $m->getByUser((int)$user_id);
}
