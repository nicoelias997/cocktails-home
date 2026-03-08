<?php

namespace App\Http\Requests\Cocktail;

use App\Http\Requests\ResourceRequest;
use Illuminate\Validation\Rule;

class StoreCocktailRequest extends ResourceRequest
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
            'name'         => ['required', 'string', 'max:120'],
            'alcohol_type' => ['required', Rule::in(['gin', 'vodka', 'rum', 'whiskey', 'tequila', 'other'])],
            'photo'        => ['nullable', 'image', 'max:4096'],
            'attributes'   => ['nullable', 'array'],
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
