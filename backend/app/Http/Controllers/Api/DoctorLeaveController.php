<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Doctor\StoreDoctorLeaveRequest;
use App\Http\Requests\Api\Doctor\UpdateDoctorLeaveRequest;
use App\Models\DoctorLeave;
use App\Services\DoctorLeaveService;
use Illuminate\Http\Request;

class DoctorLeaveController extends Controller
{
    public function __construct(
        private DoctorLeaveService $service
    ) {
    }

    public function index()
    {
        $this->authorize('viewAny', DoctorLeave::class);

        $user  = auth()->user();
        $query = DoctorLeave::with(['doctor.user', 'approvedBy']);

        if ($user->hasRole('platform_admin')) {
            // sees all
        } elseif ($user->hasRole('hospital_admin')) {
           
            $hospitalIds = $user->hospitals()->pluck('hospitals.id');
            $query->whereHas('doctor', function ($q) use ($hospitalIds) {
                $q->whereIn('hospital_id', $hospitalIds);
            });
        } elseif ($user->hasRole('doctor')) {
           
            $query->where('doctor_id', $user->id);
        }

        return response()->json(['data' => $query->orderByDesc('leave_date')->get()]);
    }

    public function show(DoctorLeave $doctorLeave)
    {
        $this->authorize('view', $doctorLeave);

        return response()->json([
            'data' => $doctorLeave->load([
                'doctor.user',
                'approvedBy',
            ])
        ]);
    }

    public function store(StoreDoctorLeaveRequest $request)
    {
        $this->authorize('create', DoctorLeave::class);

        $leave = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Leave request submitted successfully.',
            'data' => $leave
        ], 201);
    }

    public function update(
        UpdateDoctorLeaveRequest $request,
        DoctorLeave $doctorLeave
    ) {

        $this->authorize('update', $doctorLeave);

        $leave = $this->service->update(
            $doctorLeave,
            $request->validated()
        );

        return response()->json([
            'message' => 'Leave updated successfully.',
            'data' => $leave
        ]);
    }

    public function destroy(DoctorLeave $doctorLeave)
    {
        $this->authorize('delete', $doctorLeave);

        $this->service->delete($doctorLeave);

        return response()->json([
            'message' => 'Leave deleted successfully.'
        ]);
    }

    public function approve(
    Request $request,
    DoctorLeave $doctorLeave
) {
    $this->authorize('approve', $doctorLeave);

    $request->validate([
        'status' => [
            'required',
            'in:approved,rejected'
        ]
    ]);

    $result = $this->service->approve(
        $doctorLeave,
        $request->status
    );

    return response()->json([
        'message'                     => 'Leave processed successfully.',
        'data'                        => $result['leave'],
        'blocked_slots'               => $result['blocked_slots'],
        'appointments_to_reschedule'  => $result['appointments_to_reschedule'],
        'warning'                     => $result['warning'],
        'appointments'                => $result['appointments'] ?? [],
    ]);
}
}