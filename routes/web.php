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

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function ($route, $request, $response) {
    $response->header("X-Frame-Options","nALLOW-FROM https://fashiononduty.myshopify.com/");
    return view('welcome');
})->middleware(['auth.shop','FrameHeadersMiddleware'])->name('home');

Route::get('/feeds/inventory', 'FeedsController@inventoryfeed');
Route::get('/feeds/prices','FeedsController@pricesfeed');
Route::get('/feeds/asin_mapping', 'FeedsController@asinmappingfeed');
Route::get('/updatelist', 'AmazonController@updatelist');

Route::get('/sendFeed', 'FeedsController@sendFeed');

Route::get('/proxy', function () {
    return response('Hello, world!')->withHeaders(['Content-Type' => 'application/liquid']);
})->middleware('auth.proxy');

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');
Route::get('tests','ReportsController@getReport');
Route::get('ranks','ReportsController@getRanks');