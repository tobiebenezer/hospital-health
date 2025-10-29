<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function __construct(
        protected UserRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?User
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): User
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $user = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($user, $data);
            
            DB::commit();
            
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?User
    {
        DB::beginTransaction();
        
        try {
            $user = $this->repository->findById($id);
            
            if (!$user) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($user, $data);
            
            // Update the resource
            $user = $this->repository->update($user, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($user, $data);
            
            DB::commit();
            
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $user = $this->repository->findById($id);
            
            if (!$user) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($user);
            
            // Delete the resource
            $deleted = $this->repository->delete($user);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($user);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());
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
    protected function prepareDataForUpdate(User $user, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(User $user, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new UserCreated($user));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(User $user, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new UserUpdated($user));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(User $user): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(User $user): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new UserDeleted($user));
    }
}