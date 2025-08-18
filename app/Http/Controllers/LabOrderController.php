<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLabOrderRequest;
use App\Http\Requests\UpdateLabOrderStatusRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\LabOrderResource;
use App\Models\LabOrder;
use App\Jobs\UpdateLabStatsJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class LabOrderController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request)
    {
        $status  = $request->query('status');
        $from    = $request->query('from');
        $to      = $request->query('to');
        $q       = $request->query('q');
        $perPage = (int) $request->query('per_page', 10);

        $params = $request->query();
        ksort($params);
        if (isset($params['from'])) $params['from'] = (string) Carbon::parse($params['from'])->toIso8601String();
        if (isset($params['to']))   $params['to']   = (string) Carbon::parse($params['to'])->toIso8601String();

        $cacheKey = 'lab:orders:list:' . md5(json_encode($params));

        $payload = Cache::remember($cacheKey, 60, function () use ($status, $from, $to, $q, $perPage, $request) {
            $query = LabOrder::query()
                ->status($status)
                ->dateRange($from, $to)
                ->search($q)
                ->orderByRaw("FIELD(priority, 'urgent','normal')")
                ->orderByRaw("FIELD(status, 'created','received','testing','completed','archived')")
                ->orderBy('scheduled_at', 'asc');

            $paginator = $query->paginate($perPage)->appends($request->query());

            $responseArray = LabOrderResource::collection($paginator)
                ->additional([
                    'meta' => [
                        'current_page' => $paginator->currentPage(),
                        'per_page'     => $paginator->perPage(),
                        'total'        => $paginator->total(),
                    ],
                ])
                ->response()
                ->getData(true);

            return $responseArray;
        });

        return response()->json($payload, 200);
    }

    public function store(StoreLabOrderRequest $request)
    {
        $this->authorize('create', LabOrder::class);
        $data = $request->validated();
        $order = LabOrder::create([
            'patient_name' => $data['patient_name'],
            'test_code'    => $data['test_code'],
            'priority'     => $data['priority'] ?? 'normal',
            'status'       => 'created',
            'scheduled_at' => $data['scheduled_at'],
            'created_by'   => Auth::id(),
        ]);

        return (new LabOrderResource($order))
            ->response()
            ->setStatusCode(201);
    }

    public function show(LabOrder $labOrder)
    {
        $this->authorize('view', $labOrder);
        return new LabOrderResource($labOrder);
    }

    public function updateStatus(UpdateLabOrderStatusRequest $request, LabOrder $labOrder)
    {
        $current = $labOrder->status;
        $new     = $request->validated()['status'];

        $allowed = [
            'created'   => ['received'],
            'received'  => ['testing'],
            'testing'   => ['completed'],
            'completed' => ['archived'],
            'archived'  => [],
        ];

        if (! in_array($new, $allowed[$current] ?? [])) {
            return (new ErrorResource([
                [
                    'code'   => 'STATUS_TRANSITION',
                    'detail' => "Transition {$current} → {$new} is not allowed",
                ]
            ]))->response()->setStatusCode(422);
        }

        $labOrder->status = $new;
        if ($new === 'completed') {
            $labOrder->completed_at = now();
            UpdateLabStatsJob::dispatch($labOrder->id);
        }
        $labOrder->save();

        return response()->json(['success' => true]);
    }

    public function destroy(LabOrder $labOrder)
    {
        $this->authorize('delete', $labOrder);
        $labOrder->delete();
        return response()->json(['success' => true]);
    }
}
