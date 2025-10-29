<?php

namespace App\Services;

use App\Models\LabReport;
use App\Repositories\LabReportRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LabReportService
{
    public function __construct(
        protected LabReportRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?LabReport
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): LabReport
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $labReport = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($labReport, $data);
            
            DB::commit();
            
            return $labReport;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating labReport: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?LabReport
    {
        DB::beginTransaction();
        
        try {
            $labReport = $this->repository->findById($id);
            
            if (!$labReport) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($labReport, $data);
            
            // Update the resource
            $labReport = $this->repository->update($labReport, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($labReport, $data);
            
            DB::commit();
            
            return $labReport;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating labReport: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $labReport = $this->repository->findById($id);
            
            if (!$labReport) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($labReport);
            
            // Delete the resource
            $deleted = $this->repository->delete($labReport);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($labReport);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting labReport: ' . $e->getMessage());
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
    protected function prepareDataForUpdate(LabReport $labReport, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(LabReport $labReport, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new LabReportCreated($labReport));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(LabReport $labReport, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new LabReportUpdated($labReport));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(LabReport $labReport): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(LabReport $labReport): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new LabReportDeleted($labReport));
    }
}