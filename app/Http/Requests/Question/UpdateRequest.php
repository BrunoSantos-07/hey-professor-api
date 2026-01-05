<?php

namespace App\Http\Requests\Question;

use App\Models\Question;
use App\Rules\WithQuestionMark;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 *  @property-read string $question
 */
class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var Question $question */
        $question = $this->route()->question; //@phpstan-ignore-line

        return Gate::allows('update', $question);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'question' => ['required',
                new WithQuestionMark(), 'min:10', 'max:255',
                Rule::unique('questions')->ignore($this->route()->question->id), // @phpstan-ignore-line
            ],
        ];
    }
}
