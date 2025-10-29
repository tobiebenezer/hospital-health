<?php

namespace App\Services;

use App\Models\Settings;
use App\Repositories\SettingsRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingsService
{
    public function __construct(
        protected SettingsRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?Settings
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Settings
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $settings = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($settings, $data);
            
            DB::commit();
            
            return $settings;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating settings: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?Settings
    {
        DB::beginTransaction();
        
        try {
            $settings = $this->repository->findById($id);
            
            if (!$settings) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($settings, $data);
            
            // Update the resource
            $settings = $this->repository->update($settings, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($settings, $data);
            
            DB::commit();
            
            return $settings;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating settings: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $settings = $this->repository->findById($id);
            
            if (!$settings) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($settings);
            
            // Delete the resource
            $deleted = $this->repository->delete($settings);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($settings);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting settings: ' . $e->getMessage());
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
    protected function prepareDataForUpdate(Settings $settings, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(Settings $settings, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new SettingsCreated($settings));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(Settings $settings, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new SettingsUpdated($settings));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(Settings $settings): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(Settings $settings): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new SettingsDeleted($settings));
    }
}