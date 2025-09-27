<?php
require_once __DIR__ . '/../models/category_model.php';

function add_category_ctr($name, $user_id){
    $m = new Category();
    if ($m->getByName($name)) return false;              
    return $m->addCategory($name, (int)$user_id);         
}

function fetch_categories_ctr($user_id){
    $m = new Category();
    return $m->getCategoriesByUser((int)$user_id);         
}

function update_category_ctr($cat_id, $user_id, $name){
    $m = new Category();
    $exists = $m->getByName($name);
    if ($exists && (int)$exists['cat_id'] !== (int)$cat_id) return false;
    return $m->updateCategory((int)$cat_id, (int)$user_id, $name);
}

function delete_category_ctr($cat_id, $user_id){
    $m = new Category();
    return $m->deleteCategory((int)$cat_id, (int)$user_id);
}
