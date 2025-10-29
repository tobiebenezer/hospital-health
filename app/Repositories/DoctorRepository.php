<?php

namespace App\Repositories;

use App\Models\Doctor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class DoctorRepository
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
    public function findById(string $id): ?Doctor
    {
        return Doctor::find($id);
    }

    /**
     * Create a new resource.
     */
    public function create(array $data): Doctor
    {
        return Doctor::create($data);
    }

    /**
     * Update an existing resource.
     */
    public function update(Doctor $doctor, array $data): Doctor
    {
        $doctor->update($data);
        return $doctor->fresh();
    }

    /**
     * Delete a resource.
     */
    public function delete(Doctor $doctor): bool
    {
        return $doctor->delete();
    }

    /**
     * Build query with filters.
     */
    protected function buildQuery(array $filters = []): Builder
    {
        $query = Doctor::query();

        // Search filter - dynamically uses $fillable from model
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $searchTerm = '%' . $filters['search'] . '%';
                $fillable = (new Doctor)->getFillable();

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
