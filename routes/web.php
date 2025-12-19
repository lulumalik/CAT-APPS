<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('role:admin')->group(function () {
    Route::get('/admin/question-banks', function () {
        return response()->json(['ok' => true]);
    });
    Route::get('/admin/tests', function () {
        return response()->json(['ok' => true]);
    });
});

Route::view('/{any}', 'welcome')->where('any', '.*');
