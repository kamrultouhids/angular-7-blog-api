<?php

use Illuminate\Http\Request;

Route::post('login','ApiLoginController@login')->middleware('cors');
Route::post('registration','ApiUserController@store');

Route::group(['middleware' => ['jwt.auth','cors']], function(){

    Route::group(['prefix' => 'user'], function () {
        Route::get('/findAuthUser','ApiUserController@findAuthUser');
        Route::get('/','ApiUserController@index');
        Route::post('/','ApiUserController@store');
        Route::get('/{id}','ApiUserController@edit');
        Route::put('/{id}','ApiUserController@update');
        Route::delete('/{id}','ApiUserController@destroy');
    });

    Route::group(['prefix' => 'category'], function () {
        Route::get('/','ApiCategoryController@index');
        Route::post('/','ApiCategoryController@store');
        Route::get('/{id}','ApiCategoryController@edit');
        Route::put('/{id}','ApiCategoryController@update');
        Route::delete('/{id}','ApiCategoryController@destroy');
    });

    Route::group(['prefix' => 'post'], function () {
        Route::get('/','ApiPostController@index');
        Route::post('/','ApiPostController@store');
        Route::get('/{id}','ApiPostController@edit');
        Route::put('/{id}','ApiPostController@update');
        Route::delete('/{id}','ApiPostController@destroy');
    });

});

Route::group(['middleware' => ['cors']], function(){

    Route::group(['prefix' => 'frontend'], function () {
        Route::get('/findCategoryList','ApiFrontendController@findCategoryList');
        Route::get('/findAndFilterPosts','ApiFrontendController@findAndFilterPosts');
    });

});