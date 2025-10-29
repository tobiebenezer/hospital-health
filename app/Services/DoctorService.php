<?php

namespace App\Services;

use App\Models\Doctor;
use App\Repositories\DoctorRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DoctorService
{
    public function __construct(
        protected DoctorRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?Doctor
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Doctor
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $doctor = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($doctor, $data);
            
            DB::commit();
            
            return $doctor;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating doctor: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?Doctor
    {
        DB::beginTransaction();
        
        try {
            $doctor = $this->repository->findById($id);
            
            if (!$doctor) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($doctor, $data);
            
            // Update the resource
            $doctor = $this->repository->update($doctor, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($doctor, $data);
            
            DB::commit();
            
            return $doctor;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating doctor: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $doctor = $this->repository->findById($id);
            
            if (!$doctor) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($doctor);
            
            // Delete the resource
            $deleted = $this->repository->delete($doctor);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($doctor);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting doctor: ' . $e->getMessage());
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
    protected function prepareDataForUpdate(Doctor $doctor, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(Doctor $doctor, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new DoctorCreated($doctor));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(Doctor $doctor, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new DoctorUpdated($doctor));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(Doctor $doctor): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(Doctor $doctor): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new DoctorDeleted($doctor));
    }
}