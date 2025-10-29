<?php

namespace App\Services;

use App\Models\Review;
use App\Repositories\ReviewRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewService
{
    public function __construct(
        protected ReviewRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?Review
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Review
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $review = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($review, $data);
            
            DB::commit();
            
            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating review: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?Review
    {
        DB::beginTransaction();
        
        try {
            $review = $this->repository->findById($id);
            
            if (!$review) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($review, $data);
            
            // Update the resource
            $review = $this->repository->update($review, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($review, $data);
            
            DB::commit();
            
            return $review;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating review: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $review = $this->repository->findById($id);
            
            if (!$review) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($review);
            
            // Delete the resource
            $deleted = $this->repository->delete($review);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($review);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting review: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Prepare data before creation.
     */
    protected function prepareDataForCreate(array $data): array
    {
        // Add any data transformations, calculations, or defaults here
        // Example: $data['slug'] = Str::slug($data['title']);
        return $data;
    }

    /**
     * Prepare data before update.
     */
    protected function prepareDataForUpdate(Review $review, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(Review $review, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new ReviewCreated($review));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(Review $review, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new ReviewUpdated($review));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(Review $review): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(Review $review): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new ReviewDeleted($review));
    }
}