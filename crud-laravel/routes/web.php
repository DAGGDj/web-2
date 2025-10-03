<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstrumentController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('instruments', InstrumentController::class);