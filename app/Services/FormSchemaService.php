<?php

namespace App\Services;

use App\Models\FormSchema;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class FormSchemaService
{
    /**
     * Return a summary list of all schemas.
     */
    public function index(): array
    {
        return FormSchema::query()
            ->orderBy('name')
            ->get()
            ->map(fn (FormSchema $s) => $this->mapSchema($s))
            ->values()
            ->all();
    }

    /**
     * Replace the sections of an existing schema and return the updated record.
     */
    public function update(string $name, array $sections): array
    {
        $schema = FormSchema::query()
            ->where('name', $name)
            ->firstOrFail();

        $schema->sections = $sections;
        $schema->save();

        return $this->mapSchema($schema);
    }

    /**
     * Return the full sections array for the given schema name.
     * Used by the API endpoint consumed by the frontend.
     */
    public function getByName(string $name): ?array
    {
        $schema = FormSchema::query()
            ->where('name', $name)
            ->where('active', true)
            ->first();

        return $schema?->sections;
    }

    /**
     * Validate and normalise the attributes array against the schema.
     * Unknown keys are left untouched (schema can evolve without breaking old data).
     */
    public function validate(string $name, array $attributes): array
    {
        $fields = $this->attributeFields($name);

        if ($fields->isEmpty()) {
            return $attributes;
        }

        $normalized = $attributes;

        foreach ($fields as $key => $field) {
            $required = (bool) ($field['required'] ?? false);

            if (!array_key_exists($key, $attributes)) {
                if ($required) {
                    throw ValidationException::withMessages([
                        "attributes.$key" => 'This field is required.',
                    ]);
                }
                continue;
            }

            $normalized[$key] = $this->castValue($key, $field, $attributes[$key]);
        }

        return $normalized;
    }

    /**
     * Flatten all sections → fields whose key starts with "attributes.",
     * returning a Collection keyed by the bare attribute name (without the prefix).
     */
    private function attributeFields(string $name): Collection
    {
        $schema = FormSchema::query()
            ->where('name', $name)
            ->where('active', true)
            ->first();

        return collect($schema?->sections ?? [])
            ->flatMap(fn (array $section) => $section['fields'] ?? [])
            ->filter(fn (array $field) => str_starts_with($field['key'] ?? '', 'attributes.'))
            ->mapWithKeys(fn (array $field) => [
                substr($field['key'], strlen('attributes.')) => $field,
            ]);
    }

    private function castValue(string $key, array $field, mixed $value): mixed
    {
        return match ($field['type'] ?? 'text') {
            'number'   => $this->castNumber($key, $field, $value),
            'checkbox' => (bool) $value,
            'select'   => $this->castSelect($key, $field, $value),
            'repeater' => $this->castRepeater($value),
            default    => is_string($value) ? trim($value) : $value,
        };
    }

    private function castNumber(string $key, array $field, mixed $value): ?float
    {
        if ($value === '' || $value === null) {
            return null;
        }

        if (!is_numeric($value)) {
            throw ValidationException::withMessages([
                "attributes.$key" => 'Numeric value expected.',
            ]);
        }

        $number = (float) $value;

        if (isset($field['min']) && $number < (float) $field['min']) {
            throw ValidationException::withMessages([
                "attributes.$key" => "Minimum value is {$field['min']}.",
            ]);
        }

        if (isset($field['max']) && $number > (float) $field['max']) {
            throw ValidationException::withMessages([
                "attributes.$key" => "Maximum value is {$field['max']}.",
            ]);
        }

        return $number;
    }

    private function castSelect(string $key, array $field, mixed $value): string
    {
        $stringValue = is_string($value) ? trim($value) : (string) $value;

        $allowed = collect($field['options'] ?? [])
            ->map(fn ($opt) => is_array($opt) ? ($opt['value'] ?? null) : (string) $opt)
            ->filter()
            ->values();

        if ($allowed->isNotEmpty() && !$allowed->contains($stringValue)) {
            throw ValidationException::withMessages([
                "attributes.$key" => 'Value is not in allowed options.',
            ]);
        }

        return $stringValue;
    }

    private function castRepeater(mixed $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, fn ($item) => is_array($item)));
    }

    private function mapSchema(FormSchema $schema): array
    {
        $fieldCount = collect($schema->sections ?? [])
            ->sum(fn (array $s) => count($s['fields'] ?? []));

        return [
            'name'        => $schema->name,
            'active'      => (bool) $schema->active,
            'sections'    => $schema->sections ?? [],
            'field_count' => $fieldCount,
            'updated_at'  => $schema->updated_at?->toISOString(),
        ];
    }
}
