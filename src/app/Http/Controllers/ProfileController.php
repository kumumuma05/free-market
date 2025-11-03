<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        // バリデーションチェック（仕様に準じる）
        $request->validate([
            'profile_image' => 'image|mimes:jpeg,png'
        ]);

        // ユーザー専用のセッションファイル保存用ディレクトリを作成
        $userDir = 'tmp/profile_image' . auth()->id();
        Storage::disk('public')->deleteDirectory($userDir);
        Storage::disk('public')->makeDirectory($userDir);

        // セッションファイル保存
        $extension = $request->file('profile_image')->extension();
        $name = 'current.' . $extension;
        $path = $request->file('profile_image')->storeAs($userDir, $name, 'public');

        $request->session()->put('temp_image', $path);

        return redirect('/mypage/profile');
    }

    /**
     * プロフィール更新
     */
    public function update(ProfileRequest $request)
    {
        $user = auth()->user();
        $data = $request->validated();
        $tempPath = $request->session()->get('temp_image');

        if ($tempPath && Storage::disk('public')->exists($tempPath)) {

            // プロフィール画像のディレクトリへの本登録
            $extension = pathinfo($tempPath, PATHINFO_EXTENSION) ?: 'jpg';
            $destDir = 'profile_image/' . auth()->id();
            $dest = $destDir . '/' . Str::uuid() . '.' . $extension;

            Storage::disk('public')->makeDirectory($destDir);
            Storage::disk('public')->move($tempPath, $dest);

            if (!empty($user->profile_image)) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $data['profile_image'] = $dest;

            session()->forget('temp_image');
        } else {
            unset($data['profile_image']);
        }

        $user->update($data);

        // 初回登録終了後はホーム画面へそれ以外はマイページへ遷移
        if (!$user->profile_completed) {
            $user->update(['profile_completed' => true]);
            return redirect('/');
        }

        return redirect('/mypage')->with('status', 'プロフィールを更新しました');
    }

}
