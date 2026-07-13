<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Models\HealthcareProvider;
use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\UpdateHealthcareProviderRequest;
use App\Services\HealthcareProviderService;
use App\Http\Resources\HealthcareProviderResource;



class HealthcareProviderController extends Controller
{


public function __construct(
private HealthcareProviderService $service
)
{}




public function index()
{
    $user = auth()->user();

    $query = HealthcareProvider::with(['user', 'department', 'hospital']);

    if ($user->hasRole('platform_admin')) {
        // sees all
    } elseif ($user->hasRole('hospital_admin')) {
        // scoped to their hospital only
        $hospitalIds = $user->hospitals()->pluck('hospitals.id');
        $query->whereIn('hospital_id', $hospitalIds);
    } elseif ($user->hasRole('receptionist')) {
        // receptionist sees only doctors at their own hospital
        $hospitalId = $user->hospitalStaff()->value('hospital_id');
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        } else {
            return HealthcareProviderResource::collection(collect([]));
        }
    } elseif ($user->hasRole('doctor')) {
        // doctor sees colleagues in their hospital
        $hospitalId = $user->hospitalStaff()->value('hospital_id');
        if ($hospitalId) {
            $query->where('hospital_id', $hospitalId);
        }
    }
    // patients see all (for browsing/booking)

    return HealthcareProviderResource::collection($query->get());
}




public function store(
CreateDoctorRequest $request
)
{


$this->authorize('create',HealthcareProvider::class);



$provider =
$this->service->create(
$request->validated()
);



return response()->json([

    'message' => 'Doctor profile created',

    'data' => new HealthcareProviderResource(
        $provider
    ),

], 201);


}




public function update(
UpdateHealthcareProviderRequest $request,
HealthcareProvider $provider
)
{


$this->authorize('update',$provider);



$provider = $this->service->update(
    $provider,
    $request->validated()
);

return response()->json([

    'message' => 'Doctor updated successfully',

    'data' => new HealthcareProviderResource(
        $provider
    ),

]);


}

public function destroy(
HealthcareProvider $provider
)
{


$this->authorize('delete',$provider);



$this->service->delete($provider);



return response()->json([
'message'=>'Deleted'
]);


}
public function show(
    HealthcareProvider $provider
)
{
    $this->authorize(
        'view',
        $provider
    );

    $provider->load([
        'user',
        'department',
        'hospital',
    ]);

    return new HealthcareProviderResource(
        $provider
    );
}

}