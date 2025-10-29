<?php

namespace App\Services;

use App\Models\Diagnostic;
use App\Repositories\DiagnosticRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DiagnosticService
{
    public function __construct(
        protected DiagnosticRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?Diagnostic
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Diagnostic
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $diagnostic = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($diagnostic, $data);
            
            DB::commit();
            
            return $diagnostic;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating diagnostic: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?Diagnostic
    {
        DB::beginTransaction();
        
        try {
            $diagnostic = $this->repository->findById($id);
            
            if (!$diagnostic) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($diagnostic, $data);
            
            // Update the resource
            $diagnostic = $this->repository->update($diagnostic, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($diagnostic, $data);
            
            DB::commit();
            
            return $diagnostic;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating diagnostic: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $diagnostic = $this->repository->findById($id);
            
            if (!$diagnostic) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($diagnostic);
            
            // Delete the resource
            $deleted = $this->repository->delete($diagnostic);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($diagnostic);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting diagnostic: ' . $e->getMessage());
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
    protected function prepareDataForUpdate(Diagnostic $diagnostic, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(Diagnostic $diagnostic, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new DiagnosticCreated($diagnostic));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(Diagnostic $diagnostic, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new DiagnosticUpdated($diagnostic));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(Diagnostic $diagnostic): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(Diagnostic $diagnostic): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new DiagnosticDeleted($diagnostic));
    }
}