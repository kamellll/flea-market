<?php

namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class SellController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('sell', compact('categories'));
    }
    public function store(ExhibitionRequest $request)
    {

        $imagePath = $request->file('img_url')->store('products', 'public');
        $user = Auth::user();
        // ② products テーブルに商品を登録
        $product = Product::create([
            'user_id' => $user->id,
            'img_url' => $imagePath,
            'product_name' => $request->input('product_name'),
            'brand_name' => $request->input('brand_name'),
            'price' => $request->input('price'),
            'explanation' => $request->input('explanation'),
            'condition' => $request->input('condition'),
        ]);

        // ③ category_products（pivot）に複数カテゴリーを登録
        //    name="categories[]" で送られてきたID配列を想定
        $categoryIds = $request->input('categories', []); // 必須なら ExhibitionRequest 側で required|min:1 済み

        // ※ pivot テーブル名が category_products の場合は、
        // Product モデル側で belongsToMany(Category::class, 'category_products') にしておく
        $product->categories()->sync($categoryIds);

        // ④ 完了後のリダイレクト
        return redirect('/')->with('success', '商品を出品しました。');
    }
}
