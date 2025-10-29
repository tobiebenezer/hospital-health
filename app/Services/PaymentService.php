<?php

namespace App\Services;

use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        protected PaymentRepository $repository
    ) {}


    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator|Collection
    {
        return $this->repository->getAll($filters, $perPage);
    }

    public function getById(string $id): ?Payment
    {
        return $this->repository->findById($id);
    }

    public function create(array $data): Payment
    {
        DB::beginTransaction();
        
        try {
            // Pre-processing logic
            $data = $this->prepareDataForCreate($data);
            
            // Create the resource
            $payment = $this->repository->create($data);
            
            // Post-processing logic (e.g., create related records, dispatch events)
            $this->afterCreate($payment, $data);
            
            DB::commit();
            
            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating payment: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update(string $id, array $data): ?Payment
    {
        DB::beginTransaction();
        
        try {
            $payment = $this->repository->findById($id);
            
            if (!$payment) {
                return null;
            }
            
            // Pre-processing logic
            $data = $this->prepareDataForUpdate($payment, $data);
            
            // Update the resource
            $payment = $this->repository->update($payment, $data);
            
            // Post-processing logic (e.g., sync relationships, dispatch events)
            $this->afterUpdate($payment, $data);
            
            DB::commit();
            
            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment: ' . $e->getMessage());
            throw $e;
        }
    }

    public function delete(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $payment = $this->repository->findById($id);
            
            if (!$payment) {
                return false;
            }
            
            // Pre-deletion logic (e.g., check dependencies, archive data)
            $this->beforeDelete($payment);
            
            // Delete the resource
            $deleted = $this->repository->delete($payment);
            
            // Post-deletion logic (e.g., cleanup related records, dispatch events)
            $this->afterDelete($payment);
            
            DB::commit();
            
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting payment: ' . $e->getMessage());
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
    protected function prepareDataForUpdate(Payment $payment, array $data): array
    {
        // Add any data transformations specific to updates
        return $data;
    }

    /**
     * Logic to execute after resource creation.
     */
    protected function afterCreate(Payment $payment, array $data): void
    {
        // Dispatch events, create related records, send notifications, etc.
        // Example: event(new PaymentCreated($payment));
    }

    /**
     * Logic to execute after resource update.
     */
    protected function afterUpdate(Payment $payment, array $data): void
    {
        // Dispatch events, sync relationships, send notifications, etc.
        // Example: event(new PaymentUpdated($payment));
    }

    /**
     * Logic to execute before resource deletion.
     */
    protected function beforeDelete(Payment $payment): void
    {
        // Check constraints, archive data, etc.
    }

    /**
     * Logic to execute after resource deletion.
     */
    protected function afterDelete(Payment $payment): void
    {
        // Cleanup related records, dispatch events, etc.
        // Example: event(new PaymentDeleted($payment));
    }
}