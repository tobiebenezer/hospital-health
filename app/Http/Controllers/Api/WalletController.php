<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWalletRequest;
use App\Http\Requests\UpdateWalletRequest;
use App\Services\WalletService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WalletController extends Controller
{
    public function __construct(protected WalletService $service) {}

    /**
     * @OA\Get(
     *   path="/api/wallets",
     *   tags={"Wallets"},
     *   summary="List wallets",
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Wallet")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->all());
        return response()->json($data);
    }

    /**
     * @OA\Post(
     *   path="/api/wallets",
     *   tags={"Wallets"},
     *   summary="Create wallet",
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreWalletRequest")),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Wallet"))
     * )
     */
    public function store(StoreWalletRequest $request): JsonResponse
    {
        $data = $this->service->create($request->validated());
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *   path="/api/wallets/{id}",
     *   tags={"Wallets"},
     *   summary="Get wallet by ID",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Wallet")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $data = $this->service->getById($id);
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'Wallet not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Put(
     *   path="/api/wallets/{id}",
     *   tags={"Wallets"},
     *   summary="Update wallet",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateWalletRequest")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Wallet")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(UpdateWalletRequest $request, string $id): JsonResponse
    {
        $data = $this->service->update($id, $request->validated());
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'Wallet not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Delete(
     *   path="/api/wallets/{id}",
     *   tags={"Wallets"},
     *   summary="Delete wallet",
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
            : response()->json(['message' => 'Wallet not found'], Response::HTTP_NOT_FOUND);
    }
}