<?php

namespace App\Http\Controllers;

abstract class Controller
{
    public function index()    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }
}
