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

    Route::view('/reports', 'reports')->name('reports');

    //Users
    Route::prefix('/users/')->group(function ()
    {
        Route::get('/company/fetch', 'UserController@fetch_company')->name('users.company.fetch');
        Route::get('/destroy/{id}', 'UserController@destroy')->name('users.destroy');
        Route::get('/fetch', 'UserController@fetch');
        Route::get('/fetch_researchers', 'UserController@fetch_researchers')->name('users.fetch_researchers');
        Route::get('/log/{id}', 'UserController@log')->name('users.log');
    });
    Route::resource('users', 'UserController');

    Route::resource('roles', 'RoleController');


    Route::get('/home', 'HomeController@index')->name('home');

    //Quotation
    Route::prefix('/quotations/')->group(function ()
    {
        Route::get('/', 'QuotationController@index')->name('quotations.index');
        Route::get('/create', 'QuotationController@create')->name('quotations.create');
        Route::get('/{quotation_id}/edit', 'QuotationController@edit')->name('quotations.edit');
        Route::get('{quotation_id}/remove', 'QuotationController@destroy')->name('quotations.remove');
        //IMPORT
        Route::get('/import', 'QuotationController@import');
        Route::get('/export', 'QuotationController@export')->name('quotations.export');
        //

        Route::post('/report', 'QuotationController@report');
        Route::post('{quotation_id}/update', 'QuotationController@update')->name('quotations.update');
        Route::post('/save', 'QuotationController@store')->name('quotations.store');

        //Status
        Route::prefix('/status/')->group(function ()
        {
            Route::get('/', 'StatusController@index')->name('quotations_status.index');
            Route::get('/create', 'StatusController@create')->name('quotations_status.create');
            Route::get('/{status_id}/edit', 'StatusController@edit')->name('quotations_status.edit');
            Route::get('{status_id}/remove', 'StatusController@destroy')->name('quotations_status.remove');

            Route::post('{status_id}/update', 'StatusController@update')->name('quotations_status.update');
            Route::post('/save', 'StatusController@store')->name('quotations_status.store');
        });

        //Typology
        Route::prefix('/typology/')->group(function ()
        {
            Route::get('/', 'TypologyController@index')->name('quotations_typology.index');
            Route::get('/create', 'TypologyController@create')->name('quotations_typology.create');
            Route::get('/{status_id}/edit', 'TypologyController@edit')->name('quotations_typology.edit');
            Route::get('{status_id}/remove', 'TypologyController@destroy')->name('quotations_typology.remove');

            Route::post('{status_id}/update', 'TypologyController@update')->name('quotations_typology.update');
            Route::post('/save', 'TypologyController@store')->name('quotations_typology.store');
        });

        //Methodology
        Route::prefix('/methodology/')->group(function ()
        {
            Route::get('/', 'MethodologyController@index')->name('quotations_methodology.index');
            Route::get('/create', 'MethodologyController@create')->name('quotations_methodology.create');
            Route::get('/{methodology_id}/edit', 'MethodologyController@edit')->name('quotations_methodology.edit');
            Route::get('{methodology_id}/remove', 'MethodologyController@destroy')->name('quotations_methodology.remove');

            Route::post('{methodology_id}/update', 'MethodologyController@update')->name('quotations_methodology.update');
            Route::post('/save', 'MethodologyController@store')->name('quotations_methodology.store');
        });

    });

    //Company
    Route::prefix('/company/')->group(function ()
    {
        Route::get('/', 'CompanyController@index')->name('companies.index');
        Route::get('/{company_id}/edit', 'CompanyController@edit')->name('companies.edit');
        Route::get('/{company_id}/remove', 'CompanyController@destroy')->name('companies.remove');
        Route::get('/create', 'CompanyController@create')->name('companies.create');
        Route::get('/fetch', 'CompanyController@fetch')->name('companies.fetch');
        Route::get('/export', 'CompanyController@export')->name('companies.export');
        Route::get('/get_contacts', 'CompanyController@get_contacts')->name('companies.get_contacts');

        Route::post('{company_id}/update', 'CompanyController@update')->name('companies.update');
        Route::post('/save', 'CompanyController@store')->name('companies.store');
    });
});
