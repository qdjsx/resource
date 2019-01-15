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


Route::get('auth/login', 'LoginController@login');
Route::post('admin/login','LoginController@check');
Route::post('admin/needcode','LoginController@sendcode');
Route::get('auth/logout','LoginController@logout');

Route::group(['namespace' => 'Admin', 'middleware' => ['admin_login']], function()
{
    Route::get('/','AdminController@index');  //首页
    Route::get('admin/main.html','AdminController@test');
    Route::get('admin/panel', 'AdminController@index');


    //话费
    Route::get('goods/directPhone', 'GoodsDirectPhoneController@index');


    Route::get('order/directPhone', 'OrderGoodsDirectPhoneController@index');




    ///权限控制的路由
    Route::get('role/index','RoleController@index');
    Route::post('role/index','RoleController@index');
    //Route::get('role/create','RoleController@create');
    Route::post('role/upload','RoleController@upload');
    Route::post('role/store','RoleController@store');
    Route::get('role/edit/{id}/{checkId}','RoleController@edit');
    Route::post('role/update/{id}','RoleController@update');
    Route::get('role/permission/{id}','RoleController@permission');
//    Route::get('role/menu/{id}','RoleController@menu');
    //最新的菜单栏
    Route::get('role/menus','RoleController@menus');
    Route::post('roleMenu/ajaxList','RoleController@ajaxListMenus');

    Route::get('permission/index','PermissionController@index');
    Route::post('permission/index','PermissionController@index');
    Route::get('permission/create','PermissionController@create');
    Route::post('permission/upload','PermissionController@upload');
    Route::post('permission/store','PermissionController@store');
    Route::get('permission/edit/{id}/{checkId}','PermissionController@edit');
    Route::post('permission/update/{id}','PermissionController@update');

    Route::get('menu/index','MenuController@index');
    Route::post('menu/index','MenuController@index');
    Route::get('menu/create','MenuController@create');
    Route::post('menu/store','MenuController@store');
    Route::get('menu/edit/{id}/{checkId}','MenuController@edit');
    Route::post('menu/update/{id}','MenuController@update');
    //管理员管理
    Route::get('admin/user','AdminController@user');
    Route::post('admin/user','AdminController@user');
    Route::get('admin/createuser','AdminController@createuser');
    Route::post('admin/save','AdminController@save');
    Route::get('admin/edituser/{id}/{checkId}','AdminController@edituser');
    Route::post('admin/updateuser/{id}/{checkId}','AdminController@updateuser');
    Route::get('admin/permission/{id}/{checkId}','AdminController@permission');
    Route::post('admin/updatePermission/{id}','AdminController@updatePermission');
    //权限管理换样式
    Route::post('menu/ajaxList','MenuController@ajaxList');
    Route::post('role/ajaxList','RoleController@ajaxList');
    Route::post('permission/ajaxList','PermissionController@ajaxList');
    Route::post('user/ajaxList','AdminController@ajaxList');



    //角色添加(新)
    Route::get('role/create','MenuRoleController@create');
    Route::post('role/save','MenuRoleController@save');
    Route::get('role/edited/{id}/{checkId}','MenuRoleController@edited');
    Route::post('role/updated/{id}','MenuRoleController@update');
    Route::post('role/del/{id}/{checkId}','MenuRoleController@del');
    //Route::get('admin/user/{id}','AdminController@roleuser');

    //员工管理
    Route::get('user/status/{id}/{checkId}','AdminController@status');
    Route::post('user/save','AdminController@saves');
    Route::post('user/del/{id}/{checkId}','AdminController@del');


    //管理员日志
    Route::get('admin/log','AdminLogController@log');
    Route::post('admin/ajaxList','AdminLogController@ajaxList');

    //个人中心
    Route::get('personal/index','PersonalController@index');
    Route::post('personal/update/{id}/{checkId}','PersonalController@update');


});
Route::group(['middleware' => ['admin_login']], function()
{
    Route::post('upload','UploadController@upload');

});
Route::get('test/index','TestController@index');