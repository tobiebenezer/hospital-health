<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Wallet;
use App\Models\Payment;
use App\Models\Review;
use App\Models\LabReport;
use App\Models\Diagnostic;
use App\Models\SystemSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *   path="/api/dashboard/summary",
     *   tags={"Dashboard"},
     *   summary="Get system summary statistics",
     *   security={{"sanctum":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="users", type="integer", example=150),
     *       @OA\Property(property="doctors", type="integer", example=25),
     *       @OA\Property(property="patients", type="integer", example=500),
     *       @OA\Property(property="reviews", type="integer", example=1200),
     *       @OA\Property(property="revenue", type="number", format="float", example=125000.50)
     *     )
     *   )
     * )
     */
    public function summary(Request $request): JsonResponse
    {
        $stats = [
            'users' => User::count(),
            'doctors' => Doctor::count(),
            'patients' => Patient::count(),
            'reviews' => Review::count(),
            'revenue' => Payment::sum('amount'),
            'lab_reports' => LabReport::count(),
            'diagnostics' => Diagnostic::count()
        ];

        return response()->json($stats);
    }

    /**
     * @OA\Get(
     *   path="/api/dashboard/analytics",
     *   tags={"Dashboard"},
     *   summary="Get filtered analytics data",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="start_date",
     *     in="query",
     *     description="Start date (YYYY-MM-DD)",
     *     @OA\Schema(type="string", format="date")
     *   ),
     *   @OA\Parameter(
     *     name="end_date",
     *     in="query",
     *     description="End date (YYYY-MM-DD)",
     *     @OA\Schema(type="string", format="date")
     *   ),
     *   @OA\Parameter(
     *     name="type",
     *     in="query",
     *     description="Type of analytics (payments|reviews|registrations)",
     *     @OA\Schema(type="string", enum={"payments", "reviews", "registrations"})
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(
     *         @OA\Property(property="date", type="string", format="date"),
     *         @OA\Property(property="count", type="integer"),
     *         @OA\Property(property="amount", type="number", format="float")
     *       )
     *     )
     *   )
     * )
     */
    public function analytics(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:payments,reviews,registrations'
        ]);

        $query = match($request->type) {
            'payments' => Payment::query(),
            'reviews' => Review::query(),
            'registrations' => User::query()
        };

        $data = $query
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count'),
                $request->type === 'payments' ? DB::raw('SUM(amount) as amount') : DB::raw('NULL as amount')
            )
            ->whereBetween('created_at', [$request->start_date, $request->end_date])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json($data);
    }

    /**
     * @OA\Get(
     *   path="/api/dashboard/doctor-stats",
     *   tags={"Dashboard"},
     *   summary="Get top performing doctors",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(
     *     name="limit",
     *     in="query",
     *     description="Number of doctors to return",
     *     @OA\Schema(type="integer", default=5)
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/Doctor")
     *     )
     *   )
     * )
     */
    public function doctorStats(Request $request): JsonResponse
    {
        $doctors = Doctor::withCount(['reviews', 'labReports', 'diagnostics'])
            ->orderByDesc('reviews_count')
            ->limit($request->input('limit', 5))
            ->get();

        return response()->json($doctors);
    }
}
