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

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;


Route::get('/', function () {
    return redirect('login');
});

Route::post('/locale', function () {
    App::setLocale($_POST['lang']);
    Session::put('locale', $_POST['lang']);
});

Auth::routes();

Route::middleware('auth')->group(function ()
{
    Route::get('logout', '\App\Http\Controllers\Auth\LoginController@logout');

    Route::get('/users/fetch', 'UserController@fetch');
    Route::get('/users/company/fetch', 'UserController@fetch_company')->name('users.company.fetch');
    Route::get('/users/log/{id}', 'UserController@log')->name('users.log');
    Route::resource('users', 'UserController');
    Route::get('users/destroy/{id}', 'UserController@destroy')->name('users.destroy');
    Route::resource('roles', 'RoleController');


    Route::get('/home', 'HomeController@index')->name('home');

    //Quotation
    Route::prefix('/quotations/')->group(function ()
    {
        Route::get('/', 'QuotationController@index')->name('quotations.index');
    });
});
