<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PaymentController extends Controller
{
    public function __construct(protected PaymentService $service) {}

    /**
     * @OA\Get(
     *   path="/api/payments",
     *   tags={"Payments"},
     *   summary="List payments",
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Payment")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->all());
        return response()->json($data);
    }

    /**
     * @OA\Post(
     *   path="/api/payments",
     *   tags={"Payments"},
     *   summary="Create payment",
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePaymentRequest")),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Payment"))
     * )
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        $data = $this->service->create($request->validated());
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *   path="/api/payments/{id}",
     *   tags={"Payments"},
     *   summary="Get payment by ID",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Payment")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $data = $this->service->getById($id);
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'Payment not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Put(
     *   path="/api/payments/{id}",
     *   tags={"Payments"},
     *   summary="Update payment",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdatePaymentRequest")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Payment")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(UpdatePaymentRequest $request, string $id): JsonResponse
    {
        $data = $this->service->update($id, $request->validated());
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'Payment not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Delete(
     *   path="/api/payments/{id}",
     *   tags={"Payments"},
     *   summary="Delete payment",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=204, description="No Content"),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->service->delete($id);
        return $deleted
            ? response()->json(null, Response::HTTP_NO_CONTENT)
            : response()->json(['message' => 'Payment not found'], Response::HTTP_NOT_FOUND);
    }
}