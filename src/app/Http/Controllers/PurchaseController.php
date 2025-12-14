<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use App\Models\Purchase;
use Stripe\Checkout\Session as CheckoutSession;
class PurchaseController extends Controller
{
    public function index($product_id)
    {
        $product = Product::with('categories')->findOrFail($product_id);
        $userId = Auth::id();
        $profile = Profile::query()->where('user_id', $userId)->first();
        return view('purchase', compact('product', 'profile'));
    }
    public function checkout(Request $request)
    {
        $user = $request->user();

        // すでに購入済みならチェック（必要なければ削除）
        $already = Purchase::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->exists();

        if ($already) {
            return redirect()
                ->route('/purchase/' . $request->product_id, $request->product_id)
                ->with('error', 'この商品はすでに購入済みです。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Stripe Checkout 用セッション作成
        $session = CheckoutSession::create([
            'mode' => 'payment',
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $request->product_name,
                        ],
                        // JPY は「そのまま円」でOK（整数）
                        'unit_amount' => $request->price,
                    ],
                    'quantity' => 1,
                ]
            ],
            'success_url' => route('payments.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payments.cancel'),
            'metadata' => [
                'product_id' => $request->product_id,
                'user_id' => $user->id,
            ],
        ]);

        // Stripe が用意した決済画面へ
        return redirect($session->url);
    }

    /**
     * 決済成功後に戻ってくる
     * purchasesテーブルに product_id + user_id を1レコード保存
     */
    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            return redirect('/')->with('error', 'セッション情報が見つかりませんでした。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Checkout Session を取得
        $session = CheckoutSession::retrieve($sessionId);

        // 支払いが完了しているかチェック
        if ($session->payment_status !== 'paid') {
            return redirect('/')->with('error', '支払いが完了していません。');
        }

        // metadata から product_id / user_id を取得
        $productId = $session->metadata->product_id ?? null;
        $userId = $session->metadata->user_id ?? Auth::id();

        if (!$productId || !$userId) {
            return redirect('/')->with('error', '購入情報の取得に失敗しました。');
        }

        // 既に同じ購入が存在する場合は二重登録を防止
        Purchase::firstOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $productId,
            ],
            [] // 既存がなければ新規作成
        );

        return redirect('/mypage?page=buy');
    }

    /**
     * 決済キャンセル時
     */
    public function cancel()
    {
        return redirect('/')
            ->with('error', '決済がキャンセルされました。');
    }
}
