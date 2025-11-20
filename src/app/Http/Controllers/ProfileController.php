<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('/mypage/profile', compact('user'));
    }
    public function store(ProfileRequest $request)
    {
        $user = $request->user();
        $to = "";
        if ($user->is_profile_completed == 0) {
            $to = "/";
        } else {
            $to = "/mypage/profile";
        }
        // ① usersテーブルの name を更新（これも only() でOK）
        $user->update(array_merge(
            $request->only('name'),
            ['is_profile_completed' => 1]
        ));
        // User::find($request->user()->id)->update($user);

        $profileData = $request->only(['postal_code', 'address', 'building', '']);

        // ③ 画像アップロードがあればパスを profileData に追加
        if ($request->hasFile('avatar')) {

            // 既存画像があれば削除（任意）
            if ($user->profile && $user->profile->avatar_path) {
                Storage::disk('public')->delete($user->profile->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public');

            // only() で作った配列に後からキーを足す
            $profileData['avatar'] = $path;
        }

        // ④ updateOrCreate で「新規 or 更新」
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id], // 検索条件
            $profileData              // 更新／作成するカラム
        );

        return redirect($to);
    }
}
