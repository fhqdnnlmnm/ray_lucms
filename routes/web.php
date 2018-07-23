<?php


Route::get('/', function () {
    return view('welcome');
});


Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('/dashboard', function () {
    return view('dashboard');
});
