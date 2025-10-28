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
    public function edit(Request $request)
    {
        $user = auth()->user();
        $image = $request->session()->get('temp_image');

        return view('mypage.profile', compact('user', 'image'));

    }

    /**
     * プロフィール画像のセッションアップロード
     */
    public function imagePostSession(Request $request)
    {
        $path = $request->file('profile_image')->store('profile_image', 'public');

        $request->session()->put('temp_image', $path);

        return redirect('/mypage/profile');
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
        $sesImage = $request->session()->get('temp_image');

        if ($sesImage) {

            if (!empty($user->profile_image) && $user->profile_image !== $sesImage) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $data['profile_image'] = $sesImage;

            $request->session()->forget('temp_image');
        } else {
            unset($data['profile_image']);
        }

        $user->update($data);

        return redirect('/');
    }

}
