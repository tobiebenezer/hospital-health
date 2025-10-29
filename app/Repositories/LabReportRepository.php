<?php

namespace App\Repositories;

use App\Models\LabReport;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LabReportRepository
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
    public function findById(string $id): ?LabReport
    {
        return LabReport::find($id);
    }

    /**
     * Create a new resource.
     */
    public function create(array $data): LabReport
    {
        return LabReport::create($data);
    }

    /**
     * Update an existing resource.
     */
    public function update(LabReport $labReport, array $data): LabReport
    {
        $labReport->update($data);
        return $labReport->fresh();
    }

    /**
     * Delete a resource.
     */
    public function delete(LabReport $labReport): bool
    {
        return $labReport->delete();
    }

    /**
     * Build query with filters.
     */
    protected function buildQuery(array $filters = []): Builder
    {
        $query = LabReport::query();

        // Search filter - dynamically uses $fillable from model
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $searchTerm = '%' . $filters['search'] . '%';
                $fillable = (new LabReport)->getFillable();

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
