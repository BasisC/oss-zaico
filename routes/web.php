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
    Route::post('/warehouse/delete/{id}/', 'WarehouseController@delete');

    //グループ管理
    Route::get('/group','GroupController@index');
    Route::get('/group/detail/{id}','GroupController@detail');
    Route::get('/group/detail/{id}/belong','GroupController@belong');
    Route::post('/group/detail/{id}/belong','GroupController@belonging');
    Route::get('/group/edit/{id}','GroupController@edit');
    Route::post('/group/edit/{id}','GroupController@update');

    //ユーザ管理
    Route::get('/user','UserController@index');
    Route::post('/user','UserController@index');
    Route::get('/user/edit/{id}','UserController@edit');
    Route::post('/user/edit/{id}','UserController@update');
    Route::post('/user/delete/{id}','UserController@delete');
    Route::get('/user/delete/{id}','UserController@delete');

    //部署管理
    Route::get('/department','DepartmentController@index');
    Route::get('/department/detail/{id}','DepartmentController@detail');
    Route::get('/department/detail/{id}/belong','DepartmentController@belong');
    Route::post('/department/detail/{id}/belong','DepartmentController@belonging');
    Route::get('/department/add','DepartmentController@add');
    Route::post('/department/add','DepartmentController@create');

});