<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\AppointmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * @OA\Schema(
 *   schema="StoreAppointmentRequest",
 *   required={"doctor_id","patient_id","start_time","end_time"},
 *   @OA\Property(property="doctor_id", type="integer"),
 *   @OA\Property(property="patient_id", type="integer"),
 *   @OA\Property(property="start_time", type="string", format="date-time"),
 *   @OA\Property(property="end_time", type="string", format="date-time"),
 *   @OA\Property(property="notes", type="string")
 * )
 * @OA\Schema(
 *   schema="Appointment",
 *   @OA\Property(property="id", type="integer"),
 *   @OA\Property(property="doctor_id", type="integer"),
 *   @OA\Property(property="patient_id", type="integer"),
 *   @OA\Property(property="start_time", type="string", format="date-time"),
 *   @OA\Property(property="end_time", type="string", format="date-time"),
 *   @OA\Property(property="status", type="string"),
 *   @OA\Property(property="notes", type="string"),
 *   @OA\Property(property="created_at", type="string", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class AppointmentController extends Controller
{
    public function __construct(protected AppointmentService $service) {}

    /**
     * @OA\Get(
     *   path="/api/doctors/{id}/availability",
     *   tags={"Appointments"},
     *   summary="Check doctor availability",
     *   @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *   @OA\Parameter(
     *     name="date",
     *     in="query",
     *     required=true,
     *     @OA\Schema(type="string", format="date")
     *   ),
     *   @OA\Parameter(
     *     name="duration",
     *     in="query",
     *     description="Duration in minutes",
     *     @OA\Schema(type="integer", default=30)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Available slots",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         @OA\Property(property="start", type="string", format="date-time"),
     *         @OA\Property(property="end", type="string", format="date-time")
     *       )
     *     )
     *   )
     * )
     */
    public function availability(Request $request, $doctorId): JsonResponse
    {
        $request->validate([
            'date' => 'required|date',
            'duration' => 'sometimes|integer|min:15|max:240'
        ]);

        $slots = $this->service->getAvailableSlots(
            $doctorId,
            $request->date,
            $request->input('duration', 30)
        );

        return response()->json($slots);
    }

    /**
     * @OA\Post(
     *   path="/api/appointments",
     *   tags={"Appointments"},
     *   summary="Book appointment",
     *   security={{"sanctum":{}}},
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(ref="#/components/schemas/StoreAppointmentRequest")
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="Appointment created",
     *     @OA\JsonContent(ref="#/components/schemas/Appointment")
     *   )
     * )
     */
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $appointment = $this->service->bookAppointment($request->validated());
        
        return response()->json($appointment, 201);
    }
}
