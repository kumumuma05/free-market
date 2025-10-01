<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * プロフィール登録画面表示
     */
    public function mypage()
    {
        return view('/mypage');
    }

    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $user->fill($request->validated());
        $user->profile_completed = true;
        $user->save();

        return redirect('/item.index');
    }



}
