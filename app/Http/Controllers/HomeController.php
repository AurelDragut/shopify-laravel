<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index($response)
    {
        $response->header('X-Frame-Options', '\'ALLOW-FROM https://fashiononduty.myshopify.com/');
        return view('welcome');
    }
}
