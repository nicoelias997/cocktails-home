<?php

namespace App\Http\Requests\FormSchema;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFormSchemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                                      => ['required', 'string', 'max:80'],
            'sections'                                  => ['required', 'array'],
            'sections.*.title'                          => ['sometimes', 'nullable', 'string', 'max:80'],
            'sections.*.fields'                         => ['sometimes', 'nullable', 'array'],
            'sections.*.fields.*.key'                   => ['required', 'string', 'max:80', 'regex:/^[a-z][a-z0-9_.]*$/'],
            'sections.*.fields.*.label'                 => ['required', 'string', 'max:80'],
            'sections.*.fields.*.type'                  => ['required', Rule::in(['text', 'textarea', 'number', 'checkbox', 'select', 'file', 'repeater'])],
            'sections.*.fields.*.required'              => ['sometimes', 'boolean'],
            'sections.*.fields.*.placeholder'           => ['sometimes', 'nullable', 'string', 'max:120'],
            'sections.*.fields.*.min'                   => ['sometimes', 'nullable', 'numeric'],
            'sections.*.fields.*.max'                   => ['sometimes', 'nullable', 'numeric'],
            'sections.*.fields.*.options'               => ['sometimes', 'nullable', 'array'],
            'sections.*.fields.*.options.*.value'       => ['required_with:sections.*.fields.*.options', 'string'],
            'sections.*.fields.*.options.*.label'       => ['required_with:sections.*.fields.*.options', 'string'],
            'sections.*.fields.*.subfields'             => ['sometimes', 'nullable', 'array'],
            'sections.*.fields.*.subfields.*.key'       => ['required_with:sections.*.fields.*.subfields', 'string'],
            'sections.*.fields.*.subfields.*.label'     => ['required_with:sections.*.fields.*.subfields', 'string'],
            'sections.*.fields.*.subfields.*.type'      => ['sometimes', Rule::in(['text', 'number'])],
        ];
    }
}
