<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequest extends FormRequest
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
            'payment_method' => 'required|integer|in:1,2',
            'shipping_postal' => 'required|regex:/^\d{3}-\d{4}$/',
            'shipping_address' => 'required',
            'shipping_building' => 'nullable',
        ];
    }

    /**
     * セッション(支払い選択)を本登録時のリクエストに送る
     */
    protected function prepareForValidation()
    {

        $itemId = $this->route('item_id');

        if (session()->has("payment_method.$itemId")) {
            $this->merge([
                'payment_method' => session("payment_method.$itemId"),
            ]);
        }
    }

    /**
     * バリデーションメッセージ
     */
    public function messages()
    {
        return [
            'payment_method.required' => '支払方法を選択してください',
            'payment_method.in' => 'その支払い方法は無効です',
            'shipping_postal.required' => '配送先の郵便番号を入力してください',
            'shipping_postal.regex' => '郵便番号はハイフンをつけた3桁-4桁（例123-4567）の形式で入力してください',
            'shipping_address.required' => '配送先の住所を入力してください',
        ];
    }
}
