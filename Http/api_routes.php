<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where all API routes are defined.
|
*/

Route::get('admin/carts', 'Admin\CartAPIController@index');
Route::post('admin/carts', 'Admin\CartAPIController@store');
Route::get('admin/carts/{carts}', 'Admin\CartAPIController@show');
Route::put('admin/carts/{carts}', 'Admin\CartAPIController@update');
Route::patch('admin/carts/{carts}', 'Admin\CartAPIController@update');
Route::delete('admin/carts{carts}', 'Admin\CartAPIController@destroy');

Route::get('admin/users', 'Admin\UserAPIController@index');
Route::post('admin/users', 'Admin\UserAPIController@store');
Route::get('admin/users/{users}', 'Admin\UserAPIController@show');
Route::put('admin/users/{users}', 'Admin\UserAPIController@update');
Route::patch('admin/users/{users}', 'Admin\UserAPIController@update');
Route::delete('admin/users{users}', 'Admin\UserAPIController@destroy');

Route::get('admin/permission_roles', 'Admin\PermissionRoleAPIController@index');
Route::post('admin/permission_roles', 'Admin\PermissionRoleAPIController@store');
Route::get('admin/permission_roles/{permission_roles}', 'Admin\PermissionRoleAPIController@show');
Route::put('admin/permission_roles/{permission_roles}', 'Admin\PermissionRoleAPIController@update');
Route::patch('admin/permission_roles/{permission_roles}', 'Admin\PermissionRoleAPIController@update');
Route::delete('admin/permission_roles{permission_roles}', 'Admin\PermissionRoleAPIController@destroy');

Route::get('admin/permissions', 'Admin\PermissionAPIController@index');
Route::post('admin/permissions', 'Admin\PermissionAPIController@store');
Route::get('admin/permissions/{permissions}', 'Admin\PermissionAPIController@show');
Route::put('admin/permissions/{permissions}', 'Admin\PermissionAPIController@update');
Route::patch('admin/permissions/{permissions}', 'Admin\PermissionAPIController@update');
Route::delete('admin/permissions{permissions}', 'Admin\PermissionAPIController@destroy');

Route::get('admin/roles', 'Admin\RoleAPIController@index');
Route::post('admin/roles', 'Admin\RoleAPIController@store');
Route::get('admin/roles/{roles}', 'Admin\RoleAPIController@show');
Route::put('admin/roles/{roles}', 'Admin\RoleAPIController@update');
Route::patch('admin/roles/{roles}', 'Admin\RoleAPIController@update');
Route::delete('admin/roles{roles}', 'Admin\RoleAPIController@destroy');

Route::get('admin/role_users', 'Admin\RoleUserAPIController@index');
Route::post('admin/role_users', 'Admin\RoleUserAPIController@store');
Route::get('admin/role_users/{role_users}', 'Admin\RoleUserAPIController@show');
Route::put('admin/role_users/{role_users}', 'Admin\RoleUserAPIController@update');
Route::patch('admin/role_users/{role_users}', 'Admin\RoleUserAPIController@update');
Route::delete('admin/role_users{role_users}', 'Admin\RoleUserAPIController@destroy');

Route::get('admin/user_sessions', 'Admin\UserSessionAPIController@index');
Route::post('admin/user_sessions', 'Admin\UserSessionAPIController@store');
Route::get('admin/user_sessions/{user_sessions}', 'Admin\UserSessionAPIController@show');
Route::put('admin/user_sessions/{user_sessions}', 'Admin\UserSessionAPIController@update');
Route::patch('admin/user_sessions/{user_sessions}', 'Admin\UserSessionAPIController@update');
Route::delete('admin/user_sessions{user_sessions}', 'Admin\UserSessionAPIController@destroy');

Route::get('admin/accounts', 'Admin\AccountAPIController@index');
Route::post('admin/accounts', 'Admin\AccountAPIController@store');
Route::get('admin/accounts/{accounts}', 'Admin\AccountAPIController@show');
Route::put('admin/accounts/{accounts}', 'Admin\AccountAPIController@update');
Route::patch('admin/accounts/{accounts}', 'Admin\AccountAPIController@update');
Route::delete('admin/accounts{accounts}', 'Admin\AccountAPIController@destroy');

Route::get('admin/logs', 'Admin\LogsAPIController@index');
Route::post('admin/logs', 'Admin\LogsAPIController@store');
Route::get('admin/logs/{logs}', 'Admin\LogsAPIController@show');
Route::put('admin/logs/{logs}', 'Admin\LogsAPIController@update');
Route::patch('admin/logs/{logs}', 'Admin\LogsAPIController@update');
Route::delete('admin/logs{logs}', 'Admin\LogsAPIController@destroy');

Route::get('admin/insurances', 'Admin\InsuranceAPIController@index');
Route::post('admin/insurances', 'Admin\InsuranceAPIController@store');
Route::get('admin/insurances/{insurances}', 'Admin\InsuranceAPIController@show');
Route::put('admin/insurances/{insurances}', 'Admin\InsuranceAPIController@update');
Route::patch('admin/insurances/{insurances}', 'Admin\InsuranceAPIController@update');
Route::delete('admin/insurances{insurances}', 'Admin\InsuranceAPIController@destroy');

Route::get('admin/manage_carts', 'Admin\ManageCartAPIController@index');
Route::post('admin/manage_carts', 'Admin\ManageCartAPIController@store');
Route::get('admin/manage_carts/{manage_carts}', 'Admin\ManageCartAPIController@show');
Route::put('admin/manage_carts/{manage_carts}', 'Admin\ManageCartAPIController@update');
Route::patch('admin/manage_carts/{manage_carts}', 'Admin\ManageCartAPIController@update');
Route::delete('admin/manage_carts{manage_carts}', 'Admin\ManageCartAPIController@destroy');