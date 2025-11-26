<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Good;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest;
class ItemController extends Controller
{
    public function index()
    {
        $products = Product::with('purchase')->get();
        return view('index', compact('products'));
    }
    public function detail($product_id)
    {
        $product = Product::with('categories')->findOrFail($product_id);
        $comments = Comment::with(['user.profile'])
            ->where('product_id', $product_id)
            ->orderBy('created_at', 'desc')
            ->get();
        $goods = Good::where('product_id', $product_id)->get();
        // ★ 自分がこの商品をいいね済みかどうか
        $isLikedByMe = false;
        if (Auth::check()) {
            $isLikedByMe = Good::where('product_id', $product_id)
                ->where('user_id', Auth::id())
                ->exists();
        }
        $conditionPrefix = [
            1 => '良好',
            2 => '目立った傷や汚れなし',
            3 => 'やや傷や汚れあり',
            4 => '状態が悪い',
        ];
        return view('detail', compact('product', 'comments', 'goods', 'isLikedByMe', 'conditionPrefix'));
    }
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return back();
        }
        $user = $request->user();

        // 同じユーザーが同じ商品を二重に「いいね」しないように firstOrCreate
        $user = $request->user();
        $productId = $request->input('product_id');

        // すでに「いいね」済みかどうかをチェック
        $good = Good::where('user_id', $user->id)
            ->where('product_id', $productId)
            ->first();

        if ($good) {
            // ★ いいね済み → 取り消し（削除）
            $good->delete();
        } else {
            // ★ まだ → 新規登録
            Good::create([
                'user_id' => $user->id,
                'product_id' => $productId,
            ]);
        }

        // ページリロードで良いならこれでOK（元のページに戻る）
        return back();
    }
    public function comment(CommentRequest $request)
    {
        if (!Auth::check()) {
            return back();
        }
        $user = $request->user();

        // 同じユーザーが同じ商品を二重に「いいね」しないように firstOrCreate
        $user = $request->user();
        $productId = $request->input('product_id');
        $comment = $request->input('comment');
        // ★ まだ → 新規登録
        Comment::create([
            'user_id' => $user->id,
            'product_id' => $productId,
            'comment' => $comment,
        ]);

        // ページリロードで良いならこれでOK（元のページに戻る）
        return back();
    }
}
