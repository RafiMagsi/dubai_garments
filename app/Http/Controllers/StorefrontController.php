<?php

namespace App\Http\Controllers;

class StorefrontController extends Controller
{
    public function index()
    {
        return view('storefront.home');
    }
}
