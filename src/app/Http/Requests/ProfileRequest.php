<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'profile_image' => 'mimes:jpeg,png',
            'name' => 'required|max:20',
            'postal' => 'required|regex:/^\d{3}-\d{4}$/',
            'address' => 'required',
        ];
    }

    /**
     * パリシーションメッセージ
     */
    public function messages()
    {
        return [
            'profile_image.mimes' => '画像は拡張子が.jpegもしくはpngを選択してください',
            'name.required' => 'お名前を入力してください',
            'postal.required' => '郵便番号を入力してください',
            'postal.regex' => '郵便番号はハイフンをつけてください',
            'address.required' => '住所を入力してください',
        ];
    }
}
