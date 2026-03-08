<?php

namespace App\Services;

use App\Models\Cocktail;
use App\Models\User;
use Illuminate\Support\Collection;

class CocktailService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function index(User $user, array $query): array
    {
        $paginator = Cocktail::query()
            ->where('user_id', (string) $user->getAuthIdentifier())
            ->when($query['alcohol_type'] ?? null, fn ($q, $v) => $q->where('alcohol_type', $v))
            ->orderBy(...match ((string) ($query['sort'] ?? 'created_desc')) {
                'created_asc' => ['created_at', 'asc'],
                default       => ['created_at', 'desc'],
            })
            ->paginate(min(50, max(1, (int) ($query['per_page'] ?? 12))));

        return [
            'data' => $paginator->through(fn (Cocktail $c) => $this->mapCocktail($c))->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page'    => $paginator->lastPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
            ],
        ];
    }
    public function show(User $user, string $id): array
    {
        $cocktail = Cocktail::query()
            ->where('_id', $id)
            ->where('user_id', (string) $user->getAuthIdentifier())
            ->firstOrFail();

        return [
            ...$this->mapCocktail($cocktail)
        ];
    }

    public function update(User $user, string $id, array $data): array
    {
        $cocktail = Cocktail::query()
            ->where('_id', $id)
            ->where('user_id', (string) $user->getAuthIdentifier())
            ->firstOrFail();

        $cocktail->fill(array_intersect_key($data, array_flip(['name', 'alcohol_type', 'attributes'])));

        if (!empty($data['remove_photo'])) {
            $cocktail->photo_path = null;
        } elseif (array_key_exists('photo_path', $data)) {
            $cocktail->photo_path = $data['photo_path'];
        }

        $cocktail->save();

        return $this->mapCocktail($cocktail);
    }

    public function store(User $user, array $data): array
    {
        $cocktail = Cocktail::query()->create([
            'user_id'      => (string) $user->getAuthIdentifier(),
            'name'         => (string) $data['name'],
            'alcohol_type' => (string) $data['alcohol_type'],
            'photo_path'   => $data['photo_path'] ?? null,
            'attributes'   => $data['attributes'] ?? [],
        ]);

        return $this->mapCocktail($cocktail);
    }

     public function delete(User $user, string $id): void
    {
        $cocktail = Cocktail::query()
            ->where('_id', $id)
            ->where('user_id', (string) $user->getAuthIdentifier())
            ->firstOrFail();

        $cocktail->delete();
    }

    private function mapCocktail(Cocktail $cocktail): array
    {
        return [
            'id'           => (string) $cocktail->getKey(),
            'name'         => $cocktail->name,
            'alcohol_type' => $cocktail->alcohol_type ?? null,
            'photo_path'   => $cocktail->photo_path ? asset("storage/{$cocktail->photo_path}") : null,
            'attributes'   => $cocktail->attributes ?? [],
            'created_at'   => $cocktail->created_at?->toISOString(),
            'updated_at' => $cocktail->updated_at?->toISOString(),
        ];
    }
}
