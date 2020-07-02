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

    Route::get('/file/extension/fetch', 'DataCurationElementController@fetch_extension')->name('dc.extension.fetch');
    Route::get('/file/tags/fetch', 'DataCurationElementController@fetch_tags')->name('dc.tags.fetch');

    Route::post('/file/get-rooms', 'DataCurationElementController@get_rooms')->name('dce.rooms');

    Route::post('/dc/sort', 'DataCurationController@sort')->name('dc.sort');

    //Projects
    Route::prefix('projects')->group(function ()
    {

        Route::get('{id}/users', 'ProjectController@assign_users')->name('projects.users');
        Route::get('/fetch', 'ProjectController@fetch')->name('projects.fetch');
        Route::get('/remove/{id}', 'ProjectController@destroy')->name('projects.remove');

        //Search Function
        Route::post('/search', 'DataCurationElementController@search')->name('dce.search');
        Route::post('{id}/assign', 'ProjectController@save_users')->name('project.save_users');

        Route::prefix('{id}/dc')->group(function ()
        {
            //DataCuration
            Route::get('/', 'DataCurationController@index')->name('dc.index');

            Route::get('/create/', 'DataCurationController@create')->name('dc.create_index');
            Route::get('{dc_id?}/create/', 'DataCurationController@create')->name('dc.create');
            Route::get('/{dc_id}', 'DataCurationController@get')->name('dc.get');
            Route::get('/{dc_id}/edit', 'DataCurationController@edit')->name('dc.edit');
            Route::get('/{dc_id}/users', 'DataCurationController@assign_users')->name('dc.users');
            Route::get('/remove/{dc_id}', 'DataCurationController@destroy')->name('dc.remove');

            Route::post('{dc_id}/assign', 'DataCurationController@save_users')->name('dc.save_users');
            Route::post('/add-folder/', 'DataCurationController@store')->name('dc.store');
            //

            //DataCuration Elements
            Route::get('file/add', 'DataCurationElementController@create')->name('dce.create_index');
            Route::get('{dc_id}/file/add', 'DataCurationElementController@create')->name('dce.create');
            Route::prefix('/file/{file_id}')->group(function ()
            {
                Route::get('/', 'DataCurationElementController@show')->name('dce.show');
                Route::get('/edit', 'DataCurationElementController@edit')->name('dce.edit');
                Route::get('/remove', 'DataCurationElementController@destroy')->name('dce.remove');

                Route::post('/update', 'DataCurationElementController@update')->name('dce.update');

                //Comments
                Route::get('/comment', 'CommentController@create')->name('comment.add');

                Route::post('/comment/remove', 'CommentController@destroy')->name('comment.remove');
                Route::post('/comment/save', 'CommentController@store')->name('comment.store');
                Route::post('/comment/edit', 'CommentController@update')->name('comment.update');
                //
            });

            Route::post('/file/save', 'DataCurationElementController@store')->name('dce.store');
            //

        });
        Route::post('dc/update/{id}', 'DataCurationController@update')->name('dc.update');


        //Task
        Route::prefix('{id}/task')->group(function ()
        {
            Route::get('/', 'TaskController@index')->name('tasks.index');
            Route::get('/create', 'TaskController@create')->name('tasks.create');
            Route::get('/{task_id}/edit', 'TaskController@edit')->name('tasks.edit');
            Route::get('{task_id}/remove', 'TaskController@destroy')->name('tasks.remove');

            Route::post('{task_id}/update', 'TaskController@update')->name('tasks.update');
            Route::post('/save', 'TaskController@store')->name('tasks.store');

            //Task Comment
            Route::post('{task_id}/comment/remove', 'TaskCommentController@destroy');
            Route::post('{task_id}/comment/save', 'TaskCommentController@store');
            //
        });
        //

    });
    Route::resource('projects', 'ProjectController')->except('destroy');

    Route::get('/home', function ()
    {
        return redirect('projects');
    })->name('home');

});
