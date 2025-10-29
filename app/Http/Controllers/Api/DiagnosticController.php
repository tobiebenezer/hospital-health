<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDiagnosticRequest;
use App\Http\Requests\UpdateDiagnosticRequest;
use App\Services\DiagnosticService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DiagnosticController extends Controller
{
    public function __construct(protected DiagnosticService $service) {}

    /**
     * @OA\Get(
     *   path="/api/diagnostics",
     *   tags={"Diagnostics"},
     *   summary="List diagnostics",
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Diagnostic")))
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->all());
        return response()->json($data);
    }

    /**
     * @OA\Post(
     *   path="/api/diagnostics",
     *   tags={"Diagnostics"},
     *   summary="Create diagnostic",
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StoreDiagnosticRequest")),
     *   @OA\Response(response=201, description="Created", @OA\JsonContent(ref="#/components/schemas/Diagnostic"))
     * )
     */
    public function store(StoreDiagnosticRequest $request): JsonResponse
    {
        $data = $this->service->create($request->validated());
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *   path="/api/diagnostics/{id}",
     *   tags={"Diagnostics"},
     *   summary="Get diagnostic by ID",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Diagnostic")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $data = $this->service->getById($id);
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'Diagnostic not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Put(
     *   path="/api/diagnostics/{id}",
     *   tags={"Diagnostics"},
     *   summary="Update diagnostic",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdateDiagnosticRequest")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Diagnostic")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(UpdateDiagnosticRequest $request, string $id): JsonResponse
    {
        $data = $this->service->update($id, $request->validated());
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'Diagnostic not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Delete(
     *   path="/api/diagnostics/{id}",
     *   tags={"Diagnostics"},
     *   summary="Delete diagnostic",
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
            : response()->json(['message' => 'Diagnostic not found'], Response::HTTP_NOT_FOUND);
    }
}