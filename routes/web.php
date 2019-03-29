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
    return redirect('login');
});


Auth::routes();


Route::get('/home', 'HomeController@index');

    Route::get('booking', 'HomeController@booking');

//    ******************************************************Booking*****************************************

    Route::get('booking/column', 'HomeController@addColumn');

    Route::post('new/booking', 'HomeController@newColumn');

    Route::get('deleteColumn/{id}', 'HomeController@deleteColumn');

    Route::get('changeBookingStatus/{id}', 'HomeController@changeStatus');

    Route::post('edit/Bookingcolumn', 'HomeController@editColumn');

    Route::get('edit/booking/{id}', 'HomeController@editBooking');

    Route::post('booking/change', 'HomeController@bookingChange');

    Route::get('cancelBooking/{id}', 'HomeController@cancelBooking');

//    ***********************************************************All users Permission *****************************
    Route::get('allDrivers', 'HomeController@showUsers');

    Route::get('allCustomers', 'HomeController@showCustomers');

    Route::get('edit/customers/{id}/{status}', 'HomeController@editCustomer');

    Route::get('edit/drivers/{id}/{status}', 'HomeController@editUser');

    Route::post('save/changes', 'HomeController@editUsers');

    Route::post('save/customer/changes', 'HomeController@editCustomers');

    Route::get('allBookings', 'HomeController@allBooking');

//  ****************************************************************broadcast message************************************
    Route::get('broadcast/message', 'HomeController@broadcastMessage');

    Route::post('broadcast/send', 'HomeController@sendMessage');
    Route::get('broadcast/push', 'HomeController@broadcastPush');

    Route::post('broadcast/sendPush', 'HomeController@sendPush');

//*************************************************************User Profile**********************************
//if (Auth::check()) {
    Route::get('add/column', 'user_profileController@addColumn');

    Route::post('new/column', 'user_profileController@newColumn');

    Route::get('show/columns', 'user_profileController@showColumn');

    Route::get('deleteDColumn/{id}', 'user_profileController@deleteDColumn');
    Route::get('deleteCColumn/{id}', 'user_profileController@deleteCColumn');

    Route::get('changeStatus/{id}', 'user_profileController@changeStatus');

    Route::get('driverStatus/{id}/{status}', 'user_profileController@changeDriverStatus');

    Route::get('changeCustomerStatus/{id}', 'user_profileController@changeCustomerStatus');

    Route::get('show/customer/columns', 'user_profileController@showCustomer');

    Route::get('add/customer/column', 'user_profileController@addCustomerColumn');
    Route::post('edit/customer/column', 'user_profileController@editCustomerColumn');

    Route::post('new/customer/column', 'user_profileController@newCustomerColumn');

    Route::post('edit/column', 'user_profileController@editColumn');

    Route::resource('userProfiles', 'user_profileController');

    Route::get('kmPrice', 'HomeController@kmPrice');

    Route::post('kmPrice', 'HomeController@kmPriceSave');
    Route::get('parkingFees', 'HomeController@parkingFees');

    Route::post('parkingFees', 'HomeController@parkingFeesSave');

//    *************************************************************Dynamic Pages **********************************************
    Route::get('dynamic/page', 'HomeController@showPageDetails');

    Route::post('Dynamic/Pages/Save', 'HomeController@savePage');
    Route::post('fare/Pages/Save', 'HomeController@saveFare');
    Route::post('how/works/Save', 'HomeController@saveWork');
    Route::post('other/services/Save', 'HomeController@saveOther');

    Route::get('fare/charts', 'HomeController@fareCharts');
    Route::get('how/works', 'HomeController@howWorks');
    Route::get('other/services', 'HomeController@otherService');

//    ******************************************************************Fixed Routes* ********************************************

    Route::get('fixed/route', 'HomeController@fixedRoute');
    Route::get('fixed/price', 'HomeController@fixedPrice');
    Route::get('fixed/route/edit/{id}', 'HomeController@fixedEdit');
    Route::get('fixed/route/show/{id}', 'HomeController@fixedShow');
    Route::get('deleteFixed/{id}', 'HomeController@deleteFixed');
    Route::post('fixedRoute', 'HomeController@faxedRoutee');
    Route::post('fixed/routeEdit/{id}', 'HomeController@faxedRouteEdit');


//    **********************************************************************Feedbacks************************************************
    Route::get('feedbacks', 'HomeController@feedbacks');
    Route::get('deleteFeedback/{id}', 'HomeController@feedbacksDelete');

//    ************************************************************************Minimum trip price ******************************************
    Route::get('minimum/price', 'HomeController@minPrice');
    Route::get('minimum/price/edit', 'HomeController@minPriceEdit');
    Route::post('minimum/price/save', 'HomeController@minPriceSave');
    Route::get('customer/booking/{id}', 'HomeController@bookCust');
    Route::get('allBookings/show/{id}', 'HomeController@bookShowAll');

//    ****************************************************************Booking Status***************************************************
    Route::get('statusOfBooking', 'HomeController@bookStatus');
    Route::get('bookStatusDelete/{id}', 'HomeController@bookStatusDelete');
    Route::post('bookingStatus/edit', 'HomeController@bookStatusEdit');
    Route::post('bookingStatus/add', 'HomeController@bookStatusAdd');
    Route::get('changeBookStatus/{id}/{status}', 'HomeController@changeBookStatus');
    Route::get('saveTripPrice/{id}/{price}', 'HomeController@saveTripPrice');
    Route::get('assignDriver/{id}/{driver}', 'HomeController@assignDriver');


    Route::get('cron-notification', 'API\UserController@sendNotification');

