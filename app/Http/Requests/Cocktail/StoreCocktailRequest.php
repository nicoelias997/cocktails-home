<?php

namespace App\Http\Requests\Cocktail;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCocktailRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:120'],
            'alcohol_type' => ['required', Rule::in(['gin', 'vodka', 'rum', 'whiskey', 'tequila', 'other'])],
            'photo' => ['nullable', 'image', 'max:4096'],
            'attributes' => ['nullable', 'array'],
        ];
    }

      protected function prepareForValidation(): void
    {
        if (is_string($this->input('attributes'))) {
            $decoded = json_decode($this->input('attributes'), true);
            $this->merge([
                'attributes' => is_array($decoded) ? $decoded : [],
            ]);
        }
    }
}
