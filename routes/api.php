<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/driverLogin', 'API\UserController@Driverlogin');
Route::post('/customerLogin', 'API\UserController@Customerlogin');
Route::post('/verify', 'API\UserController@verify');

//***********************************************Driver**********************************************************

Route::get('/driverProfileFields', 'API\UserController@driverProfileFields');
Route::post('/driverProfile', 'API\UserController@driverProfile');

Route::get('/registeredDrivers', 'API\UserController@registeredDrivers');
Route::post('/editDriverProfile', 'API\UserController@EditdriverProfile');
Route::post('/viewDriverProfile', 'API\UserController@viewDriverProfile');

//************************************************Customer*********************************************************

Route::get('/customerProfileFields', 'API\UserController@customerProfileFields');
Route::post('/customerProfile', 'API\UserController@customerProfile');
Route::post('/customerProfileEncodedImage', 'API\UserController@customerProfileEI');
Route::post('/viewCustomerProfile', 'API\UserController@customerProfileData');

Route::get('/registeredCustomers', 'API\UserController@RegisteredCustomers');
Route::post('/editCustomerProfile', 'API\UserController@EditcustomerProfile');
//***************************************************Booking*******************************************************

Route::get('/bookingFields', 'API\UserController@bookingFields');
Route::post('/bookingFiles', 'API\UserController@bookingFiles');
Route::post('/bookingProfile', 'API\UserController@bookingProfile');

Route::post('/bookingStatus', 'API\UserController@bookingStatus');

Route::post('/bookingAccept', 'API\UserController@bookingAccept');

Route::post('/tripPrice', 'API\UserController@tripPrice');
Route::post('/tripTime', 'API\UserController@tripTime');
//Route::post('/allBookings', 'API\UserController@allBookings');
Route::post('/myBookings', 'API\UserController@allBookings');

//*******************************************************feedback*****************************************************
Route::post('/sendFeedback', 'API\UserController@feedback');

//*************************************************************Driver available Time ***********************************
Route::post('/driverAvailable', 'API\UserController@driverAvailable');
Route::post('/driverAvailableStatus', 'API\UserController@driverAvailableStatus');
Route::get('/driverAvailableList', 'API\UserController@driverAvailableList');
Route::post('/assignedBookings', 'API\UserController@assignedBookings');
Route::post('/changeBookingStatus', 'API\UserController@changeBookingStatus');
Route::post('/getmystatus', 'API\UserController@driverOnlineStatus');
Route::post('/getDriverAvailableTime', 'API\UserController@getDriverAvailableTime');
Route::post('/pendingBookings', 'API\UserController@pendingBookings');
Route::post('/bookingDetails', 'API\UserController@bookingDetails');

//*************************************************************Push Notifications***********************************

Route::post('/token_changed', 'API\UserController@token_changed');
Route::get('/pushNotification/{body}/{title}/{token}', 'API\UserController@pushNotification');
Route::get('/daily_parking_fees', 'API\UserController@daily_parking_fees');