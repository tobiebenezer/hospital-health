<?php

namespace App\Repositories;

use App\Models\Review;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReviewRepository
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
    public function findById(string $id): ?Review
    {
        return Review::find($id);
    }

    /**
     * Create a new resource.
     */
    public function create(array $data): Review
    {
        return Review::create($data);
    }

    /**
     * Update an existing resource.
     */
    public function update(Review $review, array $data): Review
    {
        $review->update($data);
        return $review->fresh();
    }

    /**
     * Delete a resource.
     */
    public function delete(Review $review): bool
    {
        return $review->delete();
    }

    /**
     * Build query with filters.
     */
    protected function buildQuery(array $filters = []): Builder
    {
        $query = Review::query();

        // Search filter - dynamically uses $fillable from model
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $searchTerm = '%' . $filters['search'] . '%';
                $fillable = (new Review)->getFillable();

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
