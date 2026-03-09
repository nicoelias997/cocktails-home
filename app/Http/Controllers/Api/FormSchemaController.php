<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FormSchema\UpdateFormSchemaRequest;
use App\Services\FormSchemaService;
use Illuminate\Http\JsonResponse;

class FormSchemaController extends Controller
{
    public function __construct(private readonly FormSchemaService $service)
    {
    }

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->service->index()]);
    }

    public function show(string $name): JsonResponse
    {
        $sections = $this->service->getByName($name);

        if ($sections === null) {
            return response()->json(['message' => "Schema '{$name}' not found."], 404);
        }

        return response()->json(['data' => $sections]);
    }

    public function update(UpdateFormSchemaRequest $request, string $name): JsonResponse
    {
        $schema = $this->service->update($name, $request->validated()['sections']);

        return response()->json(['data' => $schema]);
    }
}
