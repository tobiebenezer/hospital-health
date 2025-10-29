<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    public function __construct(protected UserService $service) {}

    /**
     * @OA\Get(
     *   path="/api/users",
     *   tags={"Users"},
     *   summary="List users",
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/User")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->all());
        return response()->json($data);
    }

    /**
     * @OA\Post(
     *   path="/api/users",
     *   tags={"Users"},
     *   summary="Create user",
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreUserRequest")),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/User"))
     * )
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $this->service->create($request->validated());
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *   path="/api/users/{id}",
     *   tags={"Users"},
     *   summary="Get user by ID",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/User")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $data = $this->service->getById($id);
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Put(
     *   path="/api/users/{id}",
     *   tags={"Users"},
     *   summary="Update user",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateUserRequest")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/User")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $data = $this->service->update($id, $request->validated());
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Delete(
     *   path="/api/users/{id}",
     *   tags={"Users"},
     *   summary="Delete user",
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
            : response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
}