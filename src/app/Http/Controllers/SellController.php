<?php

namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;

class SellController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('sell', compact('categories'));
    }
}
