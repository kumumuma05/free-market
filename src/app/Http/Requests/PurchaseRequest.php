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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'payment_method' => 'required',
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
        if (session()->has('payment_method')) {
            $this->merge(['payment_method' => session('payment_method')]);
        }
    }

    public function messages()
    {
        return [
            'payment_method.required' => '支払方法を選択してください',
            'shipping_postal.required' => '配送先の郵便番号を入力してください',
            'shipping_postal.regex' => '郵便番号はハイフンをつけてください',
            'shipping_address.required' => '配送先の住所を入力してください',
        ];
    }
}
