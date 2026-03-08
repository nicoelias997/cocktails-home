<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Cocktail\StoreCocktailRequest;
use App\Http\Requests\Cocktail\UpdateCocktailRequest;
use App\Services\CocktailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CocktailController extends Controller
{
    public function __construct(private readonly CocktailService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        return response()->json($this->service->index($request->user(), $request->query()));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        return response()->json([
            'data' => $this->service->show($request->user(), $id),
        ]);
    }

    public function store(StoreCocktailRequest $request): JsonResponse
    {
        $payload = $request->validated();

        if ($request->hasFile('photo')) {
            $payload['photo_path'] = $request->file('photo')->store('cocktails', 'public');
        }

        return response()->json([
            'data' => $this->service->store($request->user(), $payload),
        ], 201);
    }

    public function update(UpdateCocktailRequest $request, string $id): JsonResponse
    {
        $payload = $request->validated();

        if ($request->hasFile('photo')) {
            $payload['photo_path'] = $request->file('photo')->store('cocktails', 'public');
        }

        return response()->json([
            'data' => $this->service->update($request->user(), $id, $payload),
        ]);
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        $this->service->delete($request->user(), $id);

        return response()->json([
            'ok' => true,
        ]);
    }
}
