<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*For Website Routing*/
require __DIR__ . '/Website/index.php';

/*For Mobile Routing*/
require __DIR__ . '/Mobile/index.php';
require __DIR__ . '/Mobile/api_v1.php';
