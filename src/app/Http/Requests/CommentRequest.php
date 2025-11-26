<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
            'comment' => [
                'required',
                'string',
                'max:255',
            ],
        ];
    }
    public function messages()
    {
        // エラーメッセージ内で使われる項目名（日本語にしておくと便利）
        return [
            'comment.required' => 'コメントを入力してください',
            'comment.max' => 'コメントの最大文字数は255文字です',
        ];
    }
}
