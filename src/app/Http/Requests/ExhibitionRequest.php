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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'product_name' => ['required', 'string',],
            'explanation' => ['required', 'max:255',],
            'img_url' => ['required', 'image', 'mimes:jpeg,png'],
            'price' => ['required', 'numeric', 'min:0'],
            'condition' => ['required'],
            'categories' => ['required', 'array', 'min:1'],
            'categories.*' => ['integer', 'exists:categories,id'],
        ];
    }
    public function messages()
    {
        return [
            'product_name.required' => '商品名を入力してください',
            'explanation.required' => '商品説明を入力してください',
            'explanation.max' => '商品説明は255文字までです',
            'img_url.required' => '商品画像を選択してください',
            'img_url.mimes' => '商品画像はjpegまたはpng形式にしてください',
            'price.required' => '商品価格を入力してください',
            'price.numeric' => '商品価格は数値で入力してください',
            'price.min' => '商品価格は0円以上で入力してください',
            'condition.required' => '商品の状態を選択してください',
            'categories.required' => 'カテゴリーを1つ以上選択してください。',
            'categories.array' => 'カテゴリーの形式が不正です。',
            'categories.min' => 'カテゴリーを1つ以上選択してください。',
            'categories.*.exists' => '選択されたカテゴリーが不正です。',
        ];
    }
}
