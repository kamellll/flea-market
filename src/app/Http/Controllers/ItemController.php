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
    public function index(Request $request)
    {
        // どのタブか（all / mylist など）
        $tab = $request->query('tab', 'all');

        // ★ GET でも POST でも取れる input() を使う
        $inputKeyword = $request->input('keyword'); // null or '' or 文字列

        // ---------- ① keyword とセッションの整理 ----------

        if ($request->has('keyword')) {
            // パラメータ自体は来ている

            if (strlen(trim($inputKeyword)) > 0) {
                // 文字が入っている → 保存
                session(['mylist_keyword' => $inputKeyword]);
            } else {
                // 空文字（クリアされた）→ セッション削除
                session()->forget('mylist_keyword');
            }
        }

        // 実際に検索に使うキーワード（セッション基準）
        $keyword = session('mylist_keyword');

        // ---------- ② ベースクエリ作成 ----------

        if ($tab === 'mylist') {
            if (!Auth::check()) {
                // 未ログイン時の mylist は何も表示しない（0件）
                $query = Product::query()->whereRaw('1 = 0');
            } else {
                // マイリスト：ログインユーザーが goods に登録した商品だけ
                $userId = Auth::id();

                $query = Product::whereHas('goods', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            }
        } else {
            // 通常タブ
            $query = Product::query();
        }
        if (Auth::check()) {
            $userId = Auth::id();
            $query->where(function ($q) use ($userId) {
                $q->where('user_id', '!=', $userId)
                    ->orWhereNull('user_id');
            });
        }
        // ---------- ③ keyword があれば商品名であいまい検索 ----------

        if (!empty($keyword)) {
            $query->where('product_name', 'LIKE', '%' . $keyword . '%');
        }

        $products = $query->get();

        return view('index', [
            'products' => $products,
            'tab' => $tab,
            'keyword' => $keyword,
        ]);
    }

    public function search(Request $request)
    {
        $keyword = $request->input('keyword'); // or $request->query('keyword')

        $query = Product::query();

        // 空文字のときは条件をつけない
        if (!empty($keyword)) {
            $query->where('product_name', 'LIKE', '%' . $keyword . '%');
            session(['mylist_keyword' => $keyword]);
        } else {
            session()->forget('mylist_keyword');
        }

        $products = $query->get();

        return view('index', compact('products', 'keyword'));
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
