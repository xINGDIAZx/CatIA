<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Todas las demás rutas van a welcome
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '^(?!api).*$');