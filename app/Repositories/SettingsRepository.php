<?php

namespace App\Repositories;

use App\Models\Settings;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SettingsRepository
{
    /**
     * Get all resources with optional filters and pagination.
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        $query = $this->buildQuery($filters);

        return $perPage > 0
            ? $query->paginate($perPage)
            : $query->get();
    }

    /**
     * Find a resource by ID.
     */
    public function findById(string $id): ?Settings
    {
        return Settings::find($id);
    }

    /**
     * Create a new resource.
     */
    public function create(array $data): Settings
    {
        return Settings::create($data);
    }

    /**
     * Update an existing resource.
     */
    public function update(Settings $settings, array $data): Settings
    {
        $settings->update($data);
        return $settings->fresh();
    }

    /**
     * Delete a resource.
     */
    public function delete(Settings $settings): bool
    {
        return $settings->delete();
    }

    /**
     * Build query with filters.
     */
    protected function buildQuery(array $filters = []): Builder
    {
        $query = Settings::query();

        // Search filter - dynamically uses $fillable from model
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $searchTerm = '%' . $filters['search'] . '%';
                $fillable = (new Settings)->getFillable();

                // Search through all fillable text fields
                foreach ($fillable as $field) {
                    $q->orWhere($field, 'like', $searchTerm);
                }
            });
        }

        // Sorting
        $sortBy = $filters['sort'] ?? 'created_at';
        $sortOrder = $filters['order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query;
    }
}
