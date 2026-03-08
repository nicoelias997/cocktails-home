<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FormSchemaService;
use Illuminate\Http\JsonResponse;

class FormSchemaController extends Controller
{
    public function __construct(private readonly FormSchemaService $service)
    {
    }

    public function show(string $name): JsonResponse
    {
        $sections = $this->service->getByName($name);

        if ($sections === null) {
            return response()->json(['message' => "Schema '{$name}' not found."], 404);
        }

        return response()->json(['data' => $sections]);
    }
}
