<?php

namespace App\Services;

use App\Models\Patient;
use App\Repositories\PatientRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PatientService
{
    public function __construct(
        protected PatientRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?Patient
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Patient
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $patient = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($patient, $data);
            
            DB::commit();
            
            return $patient;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating patient: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?Patient
    {
        DB::beginTransaction();
        
        try {
            $patient = $this->repository->findById($id);
            
            if (!$patient) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($patient, $data);
            
            // Update the resource
            $patient = $this->repository->update($patient, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($patient, $data);
            
            DB::commit();
            
            return $patient;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating patient: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $patient = $this->repository->findById($id);
            
            if (!$patient) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($patient);
            
            // Delete the resource
            $deleted = $this->repository->delete($patient);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($patient);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting patient: ' . $e->getMessage());
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
    protected function prepareDataForUpdate(Patient $patient, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(Patient $patient, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new PatientCreated($patient));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(Patient $patient, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new PatientUpdated($patient));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(Patient $patient): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(Patient $patient): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new PatientDeleted($patient));
    }
}