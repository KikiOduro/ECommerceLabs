<?php
require_once __DIR__ . '/../models/product_model.php';

/* ===== Admin (you already call these) ===== */
function add_product_ctr($user_id, $category_id, $brand_id, $title, $price, $description, $keyword)
{
    $m = new Product();
    return $m->addProduct(
        (int)$user_id,
        (int)$category_id,
        (int)$brand_id,
        trim($title),
        (float)$price,
        $description ?: null,
        $keyword ?: null
    );
}
function update_product_ctr($user_id, $product_id, $category_id, $brand_id, $title, $price, $description, $keyword)
{
    $m = new Product();
    return $m->updateProduct(
        (int)$user_id,
        (int)$product_id,
        (int)$category_id,
        (int)$brand_id,
        trim($title),
        (float)$price,
        $description ?: null,
        $keyword ?: null
    );
}
function update_product_image_ctr($user_id, $product_id, $path)
{
    $m = new Product();
    return $m->updateImagePath((int)$user_id, (int)$product_id, $path);
}
function fetch_products_ctr($user_id)
{
    $m = new Product();
    return $m->getByUser((int)$user_id);
}

/* ===== Storefront ===== */
function view_all_products_ctr($limit = 10, $offset = 0)
{
    $m = new Product();
    return $m->view_all_products($limit, $offset);
}
function count_all_products_ctr()
{
    $m = new Product();
    return $m->count_all_products();
}
function search_products_ctr($q, $limit = 10, $offset = 0)
{
    $m = new Product();
    return $m->search_products($q, $limit, $offset);
}
function count_search_products_ctr($q)
{
    $m = new Product();
    return $m->count_search_products($q);
}
function filter_products_by_category_ctr($cat_id, $limit = 10, $offset = 0)
{
    $m = new Product();
    return $m->filter_products_by_category($cat_id, $limit, $offset);
}
function count_filter_category_ctr($cat_id)
{
    $m = new Product();
    return $m->count_filter_category($cat_id);
}
function filter_products_by_brand_ctr($brand_id, $limit = 10, $offset = 0)
{
    $m = new Product();
    return $m->filter_products_by_brand($brand_id, $limit, $offset);
}
function count_filter_brand_ctr($brand_id)
{
    $m = new Product();
    return $m->count_filter_brand($brand_id);
}
function view_single_product_ctr($id)
{
    $m = new Product();
    return $m->view_single_product($id);
}
