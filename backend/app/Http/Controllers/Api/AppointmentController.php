<?php

namespace App\Http\Controllers\Api;
use App\Http\Requests\Api\Appointment\RescheduleAppointmentRequest;
use App\Http\Requests\Api\Appointment\AdminRescheduleAppointmentRequest;
use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\HealthcareProvider;
use App\Http\Requests\Api\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Api\Appointment\UpdateAppointmentRequest;
use App\Services\AppointmentService;
use App\Services\AppointmentSlotService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function __construct(
        private AppointmentService $service,
        private AppointmentSlotService $slotService
    ) {}

    public function index()
    {
        $this->authorize('viewAny', Appointment::class);

        return response()->json([
            'data' => $this->service->all()
        ]);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $this->authorize('create', Appointment::class);

        $appointment = $this->service->create(
            $request->validated()
        );

        return response()->json([
            'message' => 'Appointment created successfully',
            'data'    => $appointment,
        ], 201);
    }

    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);

        return response()->json([
            'data' => $appointment->load([
                'patient',
                'doctor.user',
                'hospital',
                'department',
                'approvedBy',
                'slot',
            ])
        ]);
    }

    public function update(
        UpdateAppointmentRequest $request,
        Appointment $appointment
    ) {
        $this->authorize('update', $appointment);

        $updated = $this->service->update(
            $appointment,
            $request->validated()
        );

        return response()->json([
            'message' => 'Appointment updated successfully',
            'data'    => $updated,
        ]);
    }

    public function destroy(Appointment $appointment)
    {
        $this->authorize('delete', $appointment);

        $this->service->delete($appointment);

        return response()->json([
            'message' => 'Appointment deleted successfully'
        ]);
    }
    public function reschedule(
    RescheduleAppointmentRequest $request,
    Appointment $appointment
)
{
    $this->authorize('update', $appointment);

    $appointment = $this->service->reschedule(
        $appointment,
        $request->slot_id
    );

    return response()->json([

        'message' => 'Appointment rescheduled successfully.',

        'data' => $appointment

    ]);
}

    /**
     * Hospital-admin reassigns a leave-affected confirmed appointment
     * to a different available doctor in the same hospital + department.
     */
    public function adminReschedule(
        AdminRescheduleAppointmentRequest $request,
        Appointment $appointment
    ) {
        $this->authorize('adminReschedule', $appointment);

        $updated = $this->service->adminReschedule(
            $appointment,
            $request->slot_id
        );

        return response()->json([
            'message' => 'Appointment reassigned successfully.',
            'data'    => $updated,
        ]);
    }

    /**
     * Returns doctors (with their available slots) for a given
     * hospital, department, and date — for the admin reassign picker.
     *
     * Query params: hospital_id, department_id, date, exclude_doctor_id (optional)
     */
    public function availableDoctorSlots(Request $request)
    {
        $request->validate([
            'hospital_id'       => ['required', 'uuid', 'exists:hospitals,id'],
            'department_id'     => ['required', 'uuid', 'exists:departments,id'],
            'date'              => ['required', 'date'],
            'exclude_doctor_id' => ['nullable', 'uuid'],
        ]);

        $date = Carbon::parse($request->date);

        $doctors = HealthcareProvider::with(['user', 'department'])
            ->where('hospital_id',   $request->hospital_id)
            ->where('department_id', $request->department_id)
            ->when($request->exclude_doctor_id, function ($q) use ($request) {
                $q->where('id', '!=', $request->exclude_doctor_id);
            })
            ->get();

        $result = $doctors->map(function ($doctor) use ($date) {
            // Auto-generate slots for this doctor/date if not yet created
            try {
                $this->slotService->generateSlots($doctor, $date);
            } catch (\Throwable $e) {
                // silent — doctor may be on leave or have no schedule that day
            }

            $slots = AppointmentSlot::where('doctor_id', $doctor->id)
                ->where('status', 'available')
                ->whereDate('start_time', $date)
                ->orderBy('start_time')
                ->get()
                ->map(fn($s) => [
                    'id'         => $s->id,
                    'start_time' => $s->start_time,
                    'end_time'   => $s->end_time,
                ]);

            return [
                'doctor' => [
                    'id'         => $doctor->id,
                    'name'       => trim(($doctor->user->first_name ?? '') . ' ' . ($doctor->user->last_name ?? '')),
                    'department' => $doctor->department?->name,
                ],
                'available_slots' => $slots,
            ];
        })->filter(fn($d) => $d['available_slots']->isNotEmpty())->values();

        return response()->json(['data' => $result]);
    }
}