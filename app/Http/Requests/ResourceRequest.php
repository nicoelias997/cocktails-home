<?php

namespace App\Http\Requests;

use App\Services\FormSchemaService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

abstract class ResourceRequest extends FormRequest
{
    abstract protected function schemaName(): string;

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            try {
                $normalized = app(FormSchemaService::class)
                    ->validate($this->schemaName(), $this->input('attributes') ?? []);
                $this->merge(['attributes' => $normalized]);
            } catch (ValidationException $e) {
                foreach ($e->errors() as $key => $messages) {
                    foreach ($messages as $message) {
                        $validator->errors()->add($key, $message);
                    }
                }
            }
        });
    }
}
