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
            'sections.*.title'                          => ['sometimes', 'string', 'max:80'],
            'sections.*.fields'                         => ['required', 'array'],
            'sections.*.fields.*.key'                   => ['required', 'string', 'max:80', 'regex:/^[a-z][a-z0-9_.]*$/'],
            'sections.*.fields.*.label'                 => ['required', 'string', 'max:80'],
            'sections.*.fields.*.type'                  => ['required', Rule::in(['text', 'textarea', 'number', 'checkbox', 'select', 'file', 'repeater'])],
            'sections.*.fields.*.required'              => ['sometimes', 'boolean'],
            'sections.*.fields.*.placeholder'           => ['sometimes', 'string', 'max:120'],
            'sections.*.fields.*.min'                   => ['sometimes', 'numeric'],
            'sections.*.fields.*.max'                   => ['sometimes', 'numeric'],
            'sections.*.fields.*.options'               => ['sometimes', 'array'],
            'sections.*.fields.*.options.*.value'       => ['required_with:sections.*.fields.*.options', 'string'],
            'sections.*.fields.*.options.*.label'       => ['required_with:sections.*.fields.*.options', 'string'],
            'sections.*.fields.*.subfields'             => ['sometimes', 'array'],
            'sections.*.fields.*.subfields.*.key'       => ['required_with:sections.*.fields.*.subfields', 'string'],
            'sections.*.fields.*.subfields.*.label'     => ['required_with:sections.*.fields.*.subfields', 'string'],
            'sections.*.fields.*.subfields.*.type'      => ['sometimes', Rule::in(['text', 'number'])],
        ];
    }
}
