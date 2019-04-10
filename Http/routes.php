<?php


/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Application Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('errors.404');
});

// Route::get('/media/{file?}', function($file) {
//     echo public_path() . '/media/'.$file;exit;
//   return File::get(public_path() . '/media/'.$file);
// });

/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Test Booking Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/

Route::get('/test/book', 'TestsController@TestBooking')->name('index');
Route::get('/test/manage', 'TestsController@TestManageBooking')->name('index');
Route::get('/test/ship/{shipCode?}', 'DrupalController@getDrupalShip')->name('shipInfo');
Route::get('/test/repriceTest', 'ReservationController@repriceItemRequest');
Route::get('/test/associtems/{componentId?}/{sessionId?}', 'ReservationController@getAssocItemsListRequestMessageTest');
Route::get('/test/apply/{SessionID?}', 'ReservationController@getApplyPaymentRequestMessage');
Route::get('/test/getcruise', 'DrupalController@getDrupalCruise');
Route::get('/test/getprices', 'DrupalController@getPkgsPricesInfo');
Route::get('/test/marketInfo', 'DrupalController@getMarketInfoJson');
Route::get('/test/arraykeys', 'ApplicationCheckoutController@stringarraykeys');
Route::get('/test/validiter/{itercode}', 'DrupalController@getValidIterCode');
Route::get('/test/milespin/{pin}', 'MilesAndBonusController@checkFlyerId');
Route::get('/test/getInclusiveInfo', 'DrupalController@getInclusiveInfo');
//getInclusiveInfo($itercode = "LO03170630", $marketcode = "GRC")




/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Booking Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/booknow/{itercode?}', 'ApplicationCheckoutController@index')->name('index');

/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Angular Ajax Routes GET
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/cruisedetails', 'ApplicationCheckoutController@getCruisedetails');
Route::get('/guestinfo', 'ApplicationCheckoutController@getGuestinfo');
Route::get('/preferences', 'ApplicationCheckoutController@getPreferences');
Route::get('/payment', 'ApplicationCheckoutController@getPayment');
Route::get('/confirmation', 'ApplicationCheckoutController@getConfirmation');

/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Angular Ajax Routes POST
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/

Route::post('/cruisedetails/postGuestNumber', 'ApplicationCheckoutController@postGuestNumber')->name('postGuestNumber');
Route::post('/cruisedetails/postStateroom', 'ApplicationCheckoutController@postStateroom')->name('postStateroom');
Route::post('/guestinfo/postGuests', 'ApplicationCheckoutController@postGuests');
Route::post('/preferences/postReprice', 'ApplicationCheckoutController@postReprice');
Route::post('/preferences/postRepriceDrink', 'ApplicationCheckoutController@postRepriceDrink');
Route::post('/preferences/postPreferences', 'ApplicationCheckoutController@postPreferences');
Route::post('/payment/postBooking', 'ApplicationCheckoutController@postBooking');





/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Manage Booking Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/manage/login', 'ApplicationCheckoutController@getManageBookingLogin')->name('getManageBookingLogin');
Route::get('/manage/dashboard', 'ApplicationCheckoutController@getManageBookingDashboard')->name('getManageBookingDashboard');
Route::post('/manage/dashboard/postManageCart', 'ApplicationCheckoutController@postManageCart')->name('postManageCart');
Route::get('/manage/post/pay', 'ApplicationCheckoutController@getPaymentResponse')->name('getPaymentResponse');

Route::get('/manage/logout', 'ApplicationCheckoutController@getManageBookingLogout')->name('getManageBookingLogout');
Route::post('/manage/post', 'ApplicationCheckoutController@postManageBookingLogin')->name('postManageBookingLogin');


Route::get('/manage/post/pay', 'ApplicationCheckoutController@getPaymentResponse')->name('getPaymentResponse');
Route::get('/manage/post/rebook', 'ApplicationCheckoutController@getPaymentRebook')->name('getPaymentRebook');


/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Drupal Information Routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/getMarketInfo', 'DrupalController@getMarketInfoJson')->name('getMarketInfoJson');
Route::get('/getCruise/{cruiseCode?}', 'DrupalController@getDrupalCruise')->name('getDrupalCruise');
Route::get('/getDrupalShip', 'DrupalController@getDrupalShip')->name('getDrupalShip');


/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Cart Routes GET | POST
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::get('/activecart', 'ActiveCartController@getActiveCart');
Route::post('/updatecart', 'ActiveCartController@updateCart')->name('updateCart');


//Basic Error Handler Routes
Route::get('500', function(){ abort(500); });
Route::get('404', function(){ abort(404); });

/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| Admin Basic routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/
Route::auth();
Route::get('/admin', 'Admin\AdminController@index');
Route::get('/admin/documents', 'Admin\DocumentationsController@index');


/*
|----------------------------------------------------------------------------------------------------------------------------------------------------
| API routes
|----------------------------------------------------------------------------------------------------------------------------------------------------
*/

Route::group(['prefix' => 'api', 'namespace' => 'API'], function () {
    Route::group(['prefix' => 'v1'], function () {
        require config('infyom.laravel_generator.path.api_routes');
    });
});


Route::get('admin/carts', ['as'=> 'admin.carts.index', 'uses' => 'Admin\CartController@index']);
Route::post('admin/carts', ['as'=> 'admin.carts.store', 'uses' => 'Admin\CartController@store']);
Route::get('admin/carts/create', ['as'=> 'admin.carts.create', 'uses' => 'Admin\CartController@create']);
Route::put('admin/carts/{carts}', ['as'=> 'admin.carts.update', 'uses' => 'Admin\CartController@update']);
Route::patch('admin/carts/{carts}', ['as'=> 'admin.carts.update', 'uses' => 'Admin\CartController@update']);
Route::delete('admin/carts/{carts}', ['as'=> 'admin.carts.destroy', 'uses' => 'Admin\CartController@destroy']);
Route::get('admin/carts/{carts}', ['as'=> 'admin.carts.show', 'uses' => 'Admin\CartController@show']);
Route::get('admin/carts/{carts}/edit', ['as'=> 'admin.carts.edit', 'uses' => 'Admin\CartController@edit']);


Route::get('admin/users', ['as'=> 'admin.users.index', 'uses' => 'Admin\UserController@index']);
Route::post('admin/users', ['as'=> 'admin.users.store', 'uses' => 'Admin\UserController@store']);
Route::get('admin/users/create', ['as'=> 'admin.users.create', 'uses' => 'Admin\UserController@create']);
Route::put('admin/users/{users}', ['as'=> 'admin.users.update', 'uses' => 'Admin\UserController@update']);
Route::patch('admin/users/{users}', ['as'=> 'admin.users.update', 'uses' => 'Admin\UserController@update']);
Route::delete('admin/users/{users}', ['as'=> 'admin.users.destroy', 'uses' => 'Admin\UserController@destroy']);
Route::get('admin/users/{users}', ['as'=> 'admin.users.show', 'uses' => 'Admin\UserController@show']);
Route::get('admin/users/{users}/edit', ['as'=> 'admin.users.edit', 'uses' => 'Admin\UserController@edit']);


Route::get('admin/permissionRoles', ['as'=> 'admin.permissionRoles.index', 'uses' => 'Admin\PermissionRoleController@index']);
Route::post('admin/permissionRoles', ['as'=> 'admin.permissionRoles.store', 'uses' => 'Admin\PermissionRoleController@store']);
Route::get('admin/permissionRoles/create', ['as'=> 'admin.permissionRoles.create', 'uses' => 'Admin\PermissionRoleController@create']);
Route::put('admin/permissionRoles/{permissionRoles}', ['as'=> 'admin.permissionRoles.update', 'uses' => 'Admin\PermissionRoleController@update']);
Route::patch('admin/permissionRoles/{permissionRoles}', ['as'=> 'admin.permissionRoles.update', 'uses' => 'Admin\PermissionRoleController@update']);
Route::delete('admin/permissionRoles/{permissionRoles}', ['as'=> 'admin.permissionRoles.destroy', 'uses' => 'Admin\PermissionRoleController@destroy']);
Route::get('admin/permissionRoles/{permissionRoles}', ['as'=> 'admin.permissionRoles.show', 'uses' => 'Admin\PermissionRoleController@show']);
Route::get('admin/permissionRoles/{permissionRoles}/edit', ['as'=> 'admin.permissionRoles.edit', 'uses' => 'Admin\PermissionRoleController@edit']);


Route::get('admin/permissions', ['as'=> 'admin.permissions.index', 'uses' => 'Admin\PermissionController@index']);
Route::post('admin/permissions', ['as'=> 'admin.permissions.store', 'uses' => 'Admin\PermissionController@store']);
Route::get('admin/permissions/create', ['as'=> 'admin.permissions.create', 'uses' => 'Admin\PermissionController@create']);
Route::put('admin/permissions/{permissions}', ['as'=> 'admin.permissions.update', 'uses' => 'Admin\PermissionController@update']);
Route::patch('admin/permissions/{permissions}', ['as'=> 'admin.permissions.update', 'uses' => 'Admin\PermissionController@update']);
Route::delete('admin/permissions/{permissions}', ['as'=> 'admin.permissions.destroy', 'uses' => 'Admin\PermissionController@destroy']);
Route::get('admin/permissions/{permissions}', ['as'=> 'admin.permissions.show', 'uses' => 'Admin\PermissionController@show']);
Route::get('admin/permissions/{permissions}/edit', ['as'=> 'admin.permissions.edit', 'uses' => 'Admin\PermissionController@edit']);


Route::get('admin/roles', ['as'=> 'admin.roles.index', 'uses' => 'Admin\RoleController@index']);
Route::post('admin/roles', ['as'=> 'admin.roles.store', 'uses' => 'Admin\RoleController@store']);
Route::get('admin/roles/create', ['as'=> 'admin.roles.create', 'uses' => 'Admin\RoleController@create']);
Route::put('admin/roles/{roles}', ['as'=> 'admin.roles.update', 'uses' => 'Admin\RoleController@update']);
Route::patch('admin/roles/{roles}', ['as'=> 'admin.roles.update', 'uses' => 'Admin\RoleController@update']);
Route::delete('admin/roles/{roles}', ['as'=> 'admin.roles.destroy', 'uses' => 'Admin\RoleController@destroy']);
Route::get('admin/roles/{roles}', ['as'=> 'admin.roles.show', 'uses' => 'Admin\RoleController@show']);
Route::get('admin/roles/{roles}/edit', ['as'=> 'admin.roles.edit', 'uses' => 'Admin\RoleController@edit']);


Route::get('admin/roleUsers', ['as'=> 'admin.roleUsers.index', 'uses' => 'Admin\RoleUserController@index']);
Route::post('admin/roleUsers', ['as'=> 'admin.roleUsers.store', 'uses' => 'Admin\RoleUserController@store']);
Route::get('admin/roleUsers/create', ['as'=> 'admin.roleUsers.create', 'uses' => 'Admin\RoleUserController@create']);
Route::put('admin/roleUsers/{roleUsers}', ['as'=> 'admin.roleUsers.update', 'uses' => 'Admin\RoleUserController@update']);
Route::patch('admin/roleUsers/{roleUsers}', ['as'=> 'admin.roleUsers.update', 'uses' => 'Admin\RoleUserController@update']);
Route::delete('admin/roleUsers/{roleUsers}', ['as'=> 'admin.roleUsers.destroy', 'uses' => 'Admin\RoleUserController@destroy']);
Route::get('admin/roleUsers/{roleUsers}', ['as'=> 'admin.roleUsers.show', 'uses' => 'Admin\RoleUserController@show']);
Route::get('admin/roleUsers/{roleUsers}/edit', ['as'=> 'admin.roleUsers.edit', 'uses' => 'Admin\RoleUserController@edit']);


Route::get('admin/userSessions', ['as'=> 'admin.userSessions.index', 'uses' => 'Admin\UserSessionController@index']);
Route::post('admin/userSessions', ['as'=> 'admin.userSessions.store', 'uses' => 'Admin\UserSessionController@store']);
Route::get('admin/userSessions/create', ['as'=> 'admin.userSessions.create', 'uses' => 'Admin\UserSessionController@create']);
Route::put('admin/userSessions/{userSessions}', ['as'=> 'admin.userSessions.update', 'uses' => 'Admin\UserSessionController@update']);
Route::patch('admin/userSessions/{userSessions}', ['as'=> 'admin.userSessions.update', 'uses' => 'Admin\UserSessionController@update']);
Route::delete('admin/userSessions/{userSessions}', ['as'=> 'admin.userSessions.destroy', 'uses' => 'Admin\UserSessionController@destroy']);
Route::get('admin/userSessions/{userSessions}', ['as'=> 'admin.userSessions.show', 'uses' => 'Admin\UserSessionController@show']);
Route::get('admin/userSessions/{userSessions}/edit', ['as'=> 'admin.userSessions.edit', 'uses' => 'Admin\UserSessionController@edit']);


Route::get('admin/accounts', ['as'=> 'admin.accounts.index', 'uses' => 'Admin\AccountController@index']);
Route::post('admin/accounts', ['as'=> 'admin.accounts.store', 'uses' => 'Admin\AccountController@store']);
Route::get('admin/accounts/create', ['as'=> 'admin.accounts.create', 'uses' => 'Admin\AccountController@create']);
Route::put('admin/accounts/{accounts}', ['as'=> 'admin.accounts.update', 'uses' => 'Admin\AccountController@update']);
Route::patch('admin/accounts/{accounts}', ['as'=> 'admin.accounts.update', 'uses' => 'Admin\AccountController@update']);
Route::delete('admin/accounts/{accounts}', ['as'=> 'admin.accounts.destroy', 'uses' => 'Admin\AccountController@destroy']);
Route::get('admin/accounts/{accounts}', ['as'=> 'admin.accounts.show', 'uses' => 'Admin\AccountController@show']);
Route::get('admin/accounts/{accounts}/edit', ['as'=> 'admin.accounts.edit', 'uses' => 'Admin\AccountController@edit']);


Route::get('admin/logs', ['as'=> 'admin.logs.index', 'uses' => 'Admin\LogsController@index']);
Route::post('admin/logs', ['as'=> 'admin.logs.store', 'uses' => 'Admin\LogsController@store']);
Route::get('admin/logs/create', ['as'=> 'admin.logs.create', 'uses' => 'Admin\LogsController@create']);
Route::put('admin/logs/{logs}', ['as'=> 'admin.logs.update', 'uses' => 'Admin\LogsController@update']);
Route::patch('admin/logs/{logs}', ['as'=> 'admin.logs.update', 'uses' => 'Admin\LogsController@update']);
Route::delete('admin/logs/{logs}', ['as'=> 'admin.logs.destroy', 'uses' => 'Admin\LogsController@destroy']);
Route::get('admin/logs/{logs}', ['as'=> 'admin.logs.show', 'uses' => 'Admin\LogsController@show']);
Route::get('admin/logs/{logs}/edit', ['as'=> 'admin.logs.edit', 'uses' => 'Admin\LogsController@edit']);


Route::get('admin/insurances', ['as'=> 'admin.insurances.index', 'uses' => 'Admin\InsuranceController@index']);
Route::post('admin/insurances', ['as'=> 'admin.insurances.store', 'uses' => 'Admin\InsuranceController@store']);
Route::get('admin/insurances/create', ['as'=> 'admin.insurances.create', 'uses' => 'Admin\InsuranceController@create']);
Route::put('admin/insurances/{insurances}', ['as'=> 'admin.insurances.update', 'uses' => 'Admin\InsuranceController@update']);
Route::patch('admin/insurances/{insurances}', ['as'=> 'admin.insurances.update', 'uses' => 'Admin\InsuranceController@update']);
Route::delete('admin/insurances/{insurances}', ['as'=> 'admin.insurances.destroy', 'uses' => 'Admin\InsuranceController@destroy']);
Route::get('admin/insurances/{insurances}', ['as'=> 'admin.insurances.show', 'uses' => 'Admin\InsuranceController@show']);
Route::get('admin/insurances/{insurances}/edit', ['as'=> 'admin.insurances.edit', 'uses' => 'Admin\InsuranceController@edit']);


Route::get('admin/manageCarts', ['as'=> 'admin.manageCarts.index', 'uses' => 'Admin\ManageCartController@index']);
Route::post('admin/manageCarts', ['as'=> 'admin.manageCarts.store', 'uses' => 'Admin\ManageCartController@store']);
Route::get('admin/manageCarts/create', ['as'=> 'admin.manageCarts.create', 'uses' => 'Admin\ManageCartController@create']);
Route::put('admin/manageCarts/{manageCarts}', ['as'=> 'admin.manageCarts.update', 'uses' => 'Admin\ManageCartController@update']);
Route::patch('admin/manageCarts/{manageCarts}', ['as'=> 'admin.manageCarts.update', 'uses' => 'Admin\ManageCartController@update']);
Route::delete('admin/manageCarts/{manageCarts}', ['as'=> 'admin.manageCarts.destroy', 'uses' => 'Admin\ManageCartController@destroy']);
Route::get('admin/manageCarts/{manageCarts}', ['as'=> 'admin.manageCarts.show', 'uses' => 'Admin\ManageCartController@show']);
Route::get('admin/manageCarts/{manageCarts}/edit', ['as'=> 'admin.manageCarts.edit', 'uses' => 'Admin\ManageCartController@edit']);
