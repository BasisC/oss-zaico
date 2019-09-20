<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get ('test','TestController@index');

Route::group(['middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');

    //倉庫管理
    Route::get('/warehouse','WarehouseController@index');
    Route::get('/warehouse/add','WarehouseController@add');
    Route::post('warehouse/add','WarehouseController@create');
    Route::get('/warehouse/edit/{id}','WarehouseController@edit');
    Route::post('/warehouse/edit/{id}','WarehouseController@update');
    Route::get('/warehouse/delete/{id}','GroupController@return');
    Route::post('/warehouse/delete/{id}/', 'WarehouseController@delete');

    //ユーザ管理
    Route::get('/user','UserController@index');
    Route::post('/user','UserController@index');
    Route::get('/user/edit/{id}','UserController@edit');
    Route::post('/user/edit/{id}','UserController@update');
    Route::post('/user/delete/{id}','UserController@delete');
    Route::get('/user/delete/{id}','UserController@return');

    //部署管理
    Route::get('/department','DepartmentController@index');
    Route::get('/department/detail/{id}','DepartmentController@detail');
    Route::post('/department/detail/{id}','DepartmentController@detail');
    Route::get('/department/detail/{id}/belong','DepartmentController@belong');
    Route::post('/department/detail/{id}/belong','DepartmentController@belonging');
    Route::get('/department/add','DepartmentController@add');
    Route::post('/department/add','DepartmentController@create');
    Route::get('/department/edit/{id}','DepartmentController@edit');
    Route::post('/department/edit/{id}','DepartmentController@update');
    Route::get('/department/delete/{id}','DepartmentController@return');
    Route::post('/department/delete/{id}','DepartmentController@delete');
    Route::get('/department/target_list/{id}','DepartmentController@targetList');
    Route::get('/department/target_list/{id}/target','DepartmentController@target');
    Route::post('/department/target_list/{id}/target','DepartmentController@targeting');

    //分類管理
    Route::get('/classification','ClassificationController@index');
    Route::post('/classification','ClassificationController@index');
    Route::get('/classification/add','ClassificationController@add');
    Route::post('/classification/add','ClassificationController@create');
    Route::get('/classification/edit/{id}','ClassificationController@edit');
    Route::post('/classification/edit/{id}','ClassificationController@update');
    Route::get('/classification/delete/{id}','ClassificationController@return');
    Route::post('/classification/delete/{id}','ClassificationController@delete');

    //機器管理
    Route::get('/stock','StockController@index');
    Route::post('/stock','StockController@index');
    Route::get('/stock/warehouse/{id}','StockController@warehouse');
    Route::post('/stock/warehouse/{id}','StockController@warehouse');
    Route::get('/stock/warehouse/{id}/add','StockController@add');
    Route::post('/stock/warehouse/{id}/add','StockController@create');
    Route::get('/stock/warehouse/{id}/edit/{stock_id}','StockController@edit');
    Route::post('/stock/warehouse/{id}/edit/{stock_id}','StockController@update');
    Route::get('/stock/warehouse/{id}/status/{stock_id}','StockController@status');
    Route::get('/stock/warehouse/{id}/status/{stock_id}/change','StockController@changeStatus');
    Route::post('/stock/warehouse/{id}/status/{stock_id}/change','StockController@updateStatus');

    //Route::get('/stock/warehouse/{id}/delete/{stock_id}','StockController@return');
    Route::post('/stock/warehouse/{id}/delete/{stock_id}','StockController@delete');



    //練習用のページ
    Route::get('/test',"TestController@index");
});