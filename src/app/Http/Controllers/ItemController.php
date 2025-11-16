<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ProfileRequest;
class ItemController extends Controller
{
    public function index()
    {
        return view('index');
    }
    public function store(ProfileRequest $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        Auth::login($user);
        $request->session()->regenerate();
        return redirect('/mypage/profile');
    }
}
