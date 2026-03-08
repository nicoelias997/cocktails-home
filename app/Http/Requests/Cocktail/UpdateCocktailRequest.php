<?php

namespace App\Http\Requests\Cocktail;

use App\Http\Requests\ResourceRequest;
use Illuminate\Validation\Rule;

class UpdateCocktailRequest extends ResourceRequest
{
    protected function schemaName(): string
    {
        return 'cocktails';
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'         => ['sometimes', 'required', 'string', 'max:120'],
            'alcohol_type' => ['sometimes', 'required', Rule::in(['gin', 'vodka', 'rum', 'whiskey', 'tequila', 'other'])],
            'photo'        => ['nullable', 'image', 'max:4096'],
            'remove_photo' => ['sometimes', 'boolean'],
            'attributes'   => ['sometimes', 'array'],
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
