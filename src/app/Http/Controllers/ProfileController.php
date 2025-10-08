<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Requests\ProfileRequest;

class ProfileController extends Controller
{
    /**
     * プロフィール編集画面表示
     */
    public function profile()
    {
        $user = auth()->user();
        return view('mypage.profile', compact('user'));
    }
    /**
     * プロフィール初回登録
     */
    public function store(ProfileRequest $request)
    {
        $user = auth()->user();
        $user->fill($request->validated());
        $user->profile_completed = true;
        $user->save();

        return redirect('/item.index');
    }

    /**
     * プロフィール更新
     */
    public function update(ProfileRequest $request)
    {

        $user = auth()->user();
        $data = $request->validated();

        if ($request->hasFile('profile_image')){
            $data['profile_image'] = $request->file('profile_image')->store('profile_image', 'public');
        } else {
            unset($data['profile_image']);
        }

        $user->update($data);

        return redirect('/');
    }


}
