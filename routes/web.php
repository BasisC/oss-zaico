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

    //グループ管理
    Route::get('/group','GroupController@index');
    Route::get('/group/detail/{id}','GroupController@detail');
    Route::get('/group/detail/{id}/belong','GroupController@belong');
    Route::post('/group/detail/{id}/belong','GroupController@belonging');
    Route::get('/group/edit/{id}','GroupController@edit');
    Route::post('/group/edit/{id}','GroupController@update');
    Route::get('/group/add','GroupController@add');
    Route::post('/group/add','GroupController@create');
    Route::get('/group/delete/{id}','GroupController@return');
    Route::post('/group/delete/{id}','GroupController@delete');

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

});