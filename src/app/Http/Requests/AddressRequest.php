<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
     * バリデーションルール
     *
     * @return array
     */
    public function rules()
    {
        return [
            'shipping_postal' => 'required|regex:/^\d{3}-\d{4}$/',
            'shipping_address' => 'required',
            'shipping_building' => 'nullable',
        ];
    }

    /**
     * バリデーションメッセージ
     */
    public function messages()
    {
        return [
            'shipping_postal.required' => '郵便番号を入力してください',
            'shipping_postal.regex' => '郵便番号はハイフンをつけた3桁-4桁（例123-4567）の形式で入力してください',
            'shipping_address.required' => '住所を入力してください',
        ];
    }
}
