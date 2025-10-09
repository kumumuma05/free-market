<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
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
            'product_name' => 'required',
            'description' => 'required|max:255',
            'image_path' => 'required|image|images:jpeg,png',
            'category_ids' => 'required|array|min:1',
            'condition' => 'required|integer|in:1,2,3,4',
            'price' => 'required|numeric|min:0',
        ];
    }

    /**
     * バリデーションメッセージ
     */
    public function messages()
    {
        return [
            'product_name.required' => '商品名を入力してください',
            'description.required' => '商品の説明を入力してください',
            'description.max' => '商品の説明は225文字以内でしてください',
            'image_path.required' => '商品画像を貼付してください',
            'image_path.image' => '商品画像は画像ファイルを選択してください',
            'image_path.images' => '拡張子はjpegまたはpngのみ有効です',
            'category_ids.required' => 'カテゴリーを1つ以上選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '販売価格を入力してください',
            'price.numeric' => '販売価格は数字で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
        ];
    }
}
