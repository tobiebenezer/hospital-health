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
use Carbon\Carbon;
use Carbon\CarbonPeriod;

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
            'type' => 'required|in:payments,reviews,registrations',
            'granularity' => 'sometimes|in:day,week,month'
        ]);

        $granularity = $request->input('granularity', 'day');
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        $query = match($request->type) {
            'payments' => Payment::query(),
            'reviews' => Review::query(),
            'registrations' => User::query(),
        };

        // Build date key expression based on granularity
        [$selectDate, $formatCallback, $periodStep] = match($granularity) {
            'week' => [DB::raw("DATE_FORMAT(DATE_SUB(created_at, INTERVAL (WEEKDAY(created_at)) DAY), '%Y-%m-%d') as bucket"), fn(Carbon $d) => $d->startOfWeek()->format('Y-m-d'), '1 week'],
            'month' => [DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bucket"), fn(Carbon $d) => $d->format('Y-m'), '1 month'],
            default => [DB::raw('DATE(created_at) as bucket'), fn(Carbon $d) => $d->format('Y-m-d'), '1 day'],
        };

        $rows = $query
            ->select(
                $selectDate,
                DB::raw('COUNT(*) as count')
            )
            ->when($request->type === 'payments', function ($q) {
                $q->addSelect(DB::raw('SUM(amount) as amount'));
            })
            ->when($request->type !== 'payments', function ($q) {
                $q->addSelect(DB::raw('NULL as amount'));
            })
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get()
            ->keyBy('bucket');

        // Build full timeline and zero-fill
        $period = CarbonPeriod::create($start->copy()->startOfDay(), $periodStep, $end);
        $series = [];
        foreach ($period as $point) {
            $key = $formatCallback($point);
            $row = $rows->get($key);
            $series[] = [
                'date' => $key,
                'count' => $row->count ?? 0,
                'amount' => isset($row->amount) ? (float) $row->amount : null,
            ];
        }

        return response()->json([
            'granularity' => $granularity,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'series' => $series,
        ]);
    }

    /**
     * @OA\Get(
     *   path="/api/dashboard/charts",
     *   tags={"Dashboard"},
     *   summary="Chart-ready datasets (multi-series)",
     *   security={{"sanctum":{}}},
     *   @OA\Parameter(name="start_date", in="query", @OA\Schema(type="string", format="date")),
     *   @OA\Parameter(name="end_date", in="query", @OA\Schema(type="string", format="date")),
     *   @OA\Parameter(name="granularity", in="query", @OA\Schema(type="string", enum={"day","week","month"})),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       @OA\Property(property="registrations_over_time", type="array", @OA\Items(@OA\Property(property="date", type="string"), @OA\Property(property="users", type="integer"), @OA\Property(property="patients", type="integer"), @OA\Property(property="doctors", type="integer"))),
     *       @OA\Property(property="payments_over_time", type="array", @OA\Items(@OA\Property(property="date", type="string"), @OA\Property(property="count", type="integer"), @OA\Property(property="amount", type="number", format="float"))),
     *       @OA\Property(property="reviews_over_time", type="array", @OA\Items(@OA\Property(property="date", type="string"), @OA\Property(property="count", type="integer")))
     *     )
     *   )
     * )
     */
    public function charts(Request $request): JsonResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'granularity' => 'sometimes|in:day,week,month',
        ]);

        $granularity = $request->input('granularity', 'day');
        $start = Carbon::parse($request->start_date)->startOfDay();
        $end = Carbon::parse($request->end_date)->endOfDay();

        [$selectDate, $formatCallback, $periodStep] = match($granularity) {
            'week' => [DB::raw("DATE_FORMAT(DATE_SUB(created_at, INTERVAL (WEEKDAY(created_at)) DAY), '%Y-%m-%d') as bucket"), fn(Carbon $d) => $d->startOfWeek()->format('Y-%m-%d'), '1 week'],
            'month' => [DB::raw("DATE_FORMAT(created_at, '%Y-%m') as bucket"), fn(Carbon $d) => $d->format('Y-%m'), '1 month'],
            default => [DB::raw('DATE(created_at) as bucket'), fn(Carbon $d) => $d->format('Y-%m-%d'), '1 day'],
        };

        $period = CarbonPeriod::create($start->copy()->startOfDay(), $periodStep, $end);
        $timeline = [];
        foreach ($period as $point) {
            $timeline[] = $formatCallback($point);
        }

        // Helper to build zero-filled map
        $zeros = function(array $keys) { return array_fill_keys($keys, 0); };

        // Registrations (users, patients, doctors)
        $registrations = [
            'users' => User::select($selectDate, DB::raw('COUNT(*) as count'))->whereBetween('created_at', [$start, $end])->groupBy('bucket')->pluck('count', 'bucket')->all(),
            'patients' => Patient::select($selectDate, DB::raw('COUNT(*) as count'))->whereBetween('created_at', [$start, $end])->groupBy('bucket')->pluck('count', 'bucket')->all(),
            'doctors' => Doctor::select($selectDate, DB::raw('COUNT(*) as count'))->whereBetween('created_at', [$start, $end])->groupBy('bucket')->pluck('count', 'bucket')->all(),
        ];

        $registrationsSeries = [];
        foreach ($timeline as $key) {
            $registrationsSeries[] = [
                'date' => $key,
                'users' => (int) ($registrations['users'][$key] ?? 0),
                'patients' => (int) ($registrations['patients'][$key] ?? 0),
                'doctors' => (int) ($registrations['doctors'][$key] ?? 0),
            ];
        }

        // Payments over time
        $paymentsRows = Payment::select($selectDate, DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as amount'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('bucket')
            ->pluck('amount', 'bucket')
            ->all();
        $paymentsCount = Payment::select($selectDate, DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('bucket')
            ->pluck('count', 'bucket')
            ->all();

        $paymentsSeries = [];
        foreach ($timeline as $key) {
            $paymentsSeries[] = [
                'date' => $key,
                'count' => (int) ($paymentsCount[$key] ?? 0),
                'amount' => (float) ($paymentsRows[$key] ?? 0),
            ];
        }

        // Reviews over time
        $reviewsCount = Review::select($selectDate, DB::raw('COUNT(*) as count'))
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('bucket')
            ->pluck('count', 'bucket')
            ->all();
        $reviewsSeries = [];
        foreach ($timeline as $key) {
            $reviewsSeries[] = [
                'date' => $key,
                'count' => (int) ($reviewsCount[$key] ?? 0),
            ];
        }

        return response()->json([
            'granularity' => $granularity,
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'registrations_over_time' => $registrationsSeries,
            'payments_over_time' => $paymentsSeries,
            'reviews_over_time' => $reviewsSeries,
        ]);
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
