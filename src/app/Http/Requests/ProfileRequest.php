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
            'name' => ['required', 'string', 'max:20'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg'],
            'postal_code' => ['required', 'regex:/^[0-9]{3}-?[0-9]{4}$/'],
            'address' => ['required'],
        ];
    }
    public function messages()
    {
        // エラーメッセージ内で使われる項目名（日本語にしておくと便利）
        return [
            'name.required' => 'お名前を入力してください',
            'name.max' => 'お名前は20文字以内で入力してください',
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex' => '郵便番号はハイフンを含めた8文字で入力してください',
            'address.required' => '住所を入力してください',
        ];
    }
}
