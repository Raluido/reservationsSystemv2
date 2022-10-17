<?php

use Illuminate\Support\Facades\Route;


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

Route::group(['namespace' => 'App\Http\Controllers'], function () {
    /**
     * Home Routes
     */
    Route::get('/', 'HomeController@index')->name('home.index');

    Route::group(['middleware' => ['guest']], function () {

        /**
         * Login Routes
         */
        Route::get('/login', 'LoginController@show')->name('login.show');
        Route::post('/login', 'LoginController@login')->name('login.perform');

        /**
         * Reset password routes
         */

        Route::group(['prefix' => 'forget-password'], function () {
            Route::get('/', 'ForgotPasswordController@showForgetPasswordForm')->name('forget.password.get');
            Route::post('/', 'ForgotPasswordController@submitForgetPasswordForm')->name('forget.password.post');
            Route::get('/{token}', 'ForgotPasswordController@showResetPasswordForm')->name('reset.password.get');
            Route::post('reset-password', 'ForgotPasswordController@submitResetPasswordForm')->name('reset.password.post');
        });
    });

    Route::group(['middleware' => ['auth', 'permission']], function () {
        /**
         * Logout Routes
         */
        Route::get('/logout', 'LogoutController@perform')->name('logout.perform');
        /**
         * User Routes
         */
        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'UsersController@index')->name('users.index');
            Route::get('/create', 'UsersController@create')->name('users.create');
            Route::post('/create', 'UsersController@store')->name('users.store');
            Route::get('/{user}/show', 'UsersController@show')->name('users.show');
            Route::get('/{user}/edit', 'UsersController@edit')->name('users.edit');
            Route::patch('/{user}/update', 'UsersController@update')->name('users.update');
            Route::delete('/{user}/delete', 'UsersController@destroy')->name('users.destroy');
        });

        /**
         * Admin Routes
         */
        Route::group(['prefix' => 'admin'], function () {
            Route::get('/', 'AdminReservationsController@usersReservations')->name('reservations.adminReservations');
            Route::delete('/{idPadelReservation}', 'AdminReservationsController@destroyPadel')->name('reservations.destroyPadel');
            Route::delete('/{idYogaReservation}', 'AdminReservationsController@destroyYoga')->name('reservations.destroyYoga');
        });

        /**
         * User Routes
         */
        Route::group(['prefix' => 'panel'], function () {
            Route::get('/editData', 'UserController@editData')->name('user.editData');
            Route::post('/updateData', 'UserController@updateData')->name('user.updateData');
            Route::get('/editPassword', 'UserController@editPassword')->name('user.editPassword');
            Route::post('/updatePassword', 'UserController@updatePassword')->name('user.updatePassword');
        });

        Route::group(['prefix' => 'padelReservations'], function () {
            Route::get('/', 'PadelReservationsController@index')->name('reservations.indexPadel');
            Route::get('/getcheckdate/{checkdate}', 'PadelReservationsController@getcheckdate')->name('reservations.getPadelDate');
            Route::post('/', 'PadelReservationsController@creatematch')->name('reservations.createPadelMatch');
            Route::get('/deletematch/{matchdate}', 'PadelReservationsController@deletematch')->name('reservations.deletePadelMatch');
        });

        Route::group(['prefix' => 'yogaReservations'], function () {
            Route::get('/', 'YogaReservationsController@index')->name('reservations.indexYoga');
            Route::get('/getcheckdate/{checkdate}', 'YogaReservationsController@getcheckdate')->name('reservations.getYogaDate');
            Route::post('/', 'YogaReservationsController@bookclasses')->name('reservations.bookYogaClasses');
            Route::get('/cancelclasses/{bookdate}', 'YogaReservationsController@cancelclasses')->name('reservations.cancelYogaClasses');
        });

        Route::group(['prefix' => 'userReservations'], function () {
            Route::get('/', 'ReservationsController@userReservations')->name('reservations.userReservations');
            Route::get('/deleteYoga/{bookdate}', 'ReservationsController@deleteYoga')->name('reservations.deleteYoga');
            Route::get('/deletePadel/{bookdate}', 'ReservationsController@deletePadel')->name('reservations.deletePadel');
        });


        Route::resource('roles', RolesController::class);
        Route::resource('permissions', PermissionsController::class);
    });
});
