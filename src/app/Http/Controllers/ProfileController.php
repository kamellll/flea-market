<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::query()->where('user_id', $user->id)->first();
        return view('/mypage/profile', compact('user', 'profile'));
    }
    public function store(ProfileRequest $request)
    {
        $user = $request->user();

        // リダイレクト先
        $to = $user->is_profile_completed == 0 ? '/' : '/mypage/profile';

        // users.name / is_profile_completed を更新
        $user->update([
            'name' => $request->input('name'),
            'is_profile_completed' => 1,
        ]);

        // プロフィール用データを取得
        $profileData = $request->only([
            'postal_code',
            'address',
            'building',
        ]);

        // 画像アップロード
        if ($request->hasFile('avatar')) {

            if ($user->profile && $user->profile->avatar) {
                Storage::disk('public')->delete($user->profile->avatar);
            }

            $path = $request->file('avatar')->store('avatars', 'public');
            $profileData['avatar'] = $path;
        }

        // 新規 or 更新
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],             // hasOne 経由なので user_id は自動でセットされる
            $profileData
        );

        return redirect($to)->with('success', 'プロフィールを保存しました。');
    }
    public function mypage(Request $request)
    {
        $page = $request->query('page', 'sell');
        $user = Auth::user();
        $userId = Auth::id();
        $profile = Profile::query()->where('user_id', $userId)->first();
        $query = Product::query();
        if ($page === 'sell') {
            // 通常タブ
            $products = Product::query()->UserIdSearch($userId)->get();
        } else {
            $products = Product::whereHas('purchase', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })->get();
        }
        return view('mypage', compact('user', 'profile', 'products'));
    }
}
