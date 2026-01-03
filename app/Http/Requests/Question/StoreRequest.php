<?php

namespace App\Http\Requests\Question;

use App\Rules\WithQuestionMark;
use Illuminate\Foundation\Http\FormRequest;

/**
 *  @property string $question
 */
class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['required', new WithQuestionMark(), 'string', 'max:255', 'min:10', 'unique:questions'],
        ];
    }

    public function messages(): array
    {
        return [
            'question.required' => 'The question field is required.',
            'question.string'   => 'The question field must be a string.',
            'question.max'      => 'The question field must be less than 255 characters.',
            'question.min'      => 'The question field must be at least 10 characters.',
            'question.unique'   => 'The question has already been taken.',
        ];
    }
}
