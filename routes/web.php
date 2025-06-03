<?php

use Illuminate\Support\Facades\Route;
use App\Library\DatabaseGenerate;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return DatabaseGenerate::sales();
});
