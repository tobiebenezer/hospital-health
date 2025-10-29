<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PatientController extends Controller
{
    public function __construct(protected PatientService $service) {}

    /**
     * @OA\Get(
     *   path="/api/patients",
     *   tags={"Patients"},
     *   summary="List patients",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/Patient"))
     *   )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->all());
        return response()->json($data);
    }

    /**
     * @OA\Post(
     *   path="/api/patients",
     *   tags={"Patients"},
     *   summary="Create patient",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/StorePatientRequest")),
     *   @OA\Response(
     *     response=201,
     *     description="Created",
     *     @OA\JsonContent(ref="#/components/schemas/Patient")
     *   )
     * )
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        $data = $this->service->create($request->validated());
        return response()->json($data, Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *   path="/api/patients/{id}",
     *   tags={"Patients"},
     *   summary="Get patient by ID",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Patient")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(string $id): JsonResponse
    {
        $data = $this->service->getById($id);
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'Patient not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Put(
     *   path="/api/patients/{id}",
     *   tags={"Patients"},
     *   summary="Update patient",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *   @OA\RequestBody(required=true, @OA\JsonContent(ref="#/components/schemas/UpdatePatientRequest")),
     *   @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/Patient")),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function update(UpdatePatientRequest $request, string $id): JsonResponse
    {
        $data = $this->service->update($id, $request->validated());
        return $data
            ? response()->json($data)
            : response()->json(['message' => 'Patient not found'], Response::HTTP_NOT_FOUND);
    }

    /**
     * @OA\Delete(
     *   path="/api/patients/{id}",
     *   tags={"Patients"},
     *   summary="Delete patient",
     *   security={{"sanctum":{}}},
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
            : response()->json(['message' => 'Patient not found'], Response::HTTP_NOT_FOUND);
    }
}