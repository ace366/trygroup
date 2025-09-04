<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return false;
        return true; // ✅ これを true にすることで、全ユーザーがフォームを送信可能
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'last_name' => ['required', 'regex:/^[ぁ-んァ-ヶ一-龥々ー]+$/u'],
            'first_name' => ['required', 'regex:/^[ぁ-んァ-ヶ一-龥々ー]+$/u'],
            'last_name_kana' => ['required', 'regex:/^[ぁ-んー]+$/u'],
            'first_name_kana' => ['required', 'regex:/^[ぁ-んー]+$/u'],
            //'last_name' => ['required', 'string', 'max:255'],
            //'first_name' => ['required', 'string', 'max:255'],
            //'last_name_kana' => ['required', 'string', 'max:255'],
            //'first_name_kana' => ['required', 'string', 'max:255'],
            'school' => ['required', 'string'],
            'grade' => ['required', 'string'],
            'class' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:15'],
            'lesson_type' => ['required', 'string'],
            'lesson_time' => ['required', 'in:午前,午後'], // ✅ 午前/午後のバリデーションを追加
            'eiken' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:4'],
            'other' => ['nullable', 'string'],
        ];
    }
        public function messages(): array
    {
        return [
            'last_name.regex' => '姓はひらがな・カタカナ・漢字のみで入力してください。',
            'first_name.regex' => '名はひらがな・カタカナ・漢字のみで入力してください。',
            'last_name_kana.regex' => '姓（かな）はひらがなのみで入力してください。',
            'first_name_kana.regex' => '名（かな）はひらがなのみで入力してください。',
            'phone.regex' => '電話番号の形式が正しくありません（例：090-1234-5678、048-123-4567）。',
        ];
    }
}
