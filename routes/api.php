<?php

use GuzzleHttp\Middleware;

Route::group(['namespace' => 'apiController'], function () {
// Route::post('categories', 'categorycontroller@getcategories');
Route::get('categories', 'categorycontroller@getcategories')->middleware('cors');
//Route::post('category', 'categoryController@getcategories');
Route::get('products', 'productsController@getproducts')->middleware('cors');
Route::get('product', 'productController@getproduct')->middleware('cors');

});
