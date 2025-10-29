<?php

namespace App\Services;

use App\Models\Wallet;
use App\Repositories\WalletRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    public function __construct(
        protected WalletRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?Wallet
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Wallet
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $wallet = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($wallet, $data);
            
            DB::commit();
            
            return $wallet;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating wallet: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?Wallet
    {
        DB::beginTransaction();
        
        try {
            $wallet = $this->repository->findById($id);
            
            if (!$wallet) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($wallet, $data);
            
            // Update the resource
            $wallet = $this->repository->update($wallet, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($wallet, $data);
            
            DB::commit();
            
            return $wallet;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating wallet: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $wallet = $this->repository->findById($id);
            
            if (!$wallet) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($wallet);
            
            // Delete the resource
            $deleted = $this->repository->delete($wallet);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($wallet);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting wallet: ' . $e->getMessage());
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
    protected function prepareDataForUpdate(Wallet $wallet, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(Wallet $wallet, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new WalletCreated($wallet));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(Wallet $wallet, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new WalletUpdated($wallet));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(Wallet $wallet): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(Wallet $wallet): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new WalletDeleted($wallet));
    }
}