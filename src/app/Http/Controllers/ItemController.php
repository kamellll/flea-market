<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
class ItemController extends Controller
{
    public function index()
    {
        $products = Product::with('purchase')->get();
        return view('index', compact('products'));
    }

}
