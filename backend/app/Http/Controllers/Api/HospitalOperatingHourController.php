<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperatingHourRequest;
use App\Http\Requests\UpdateOperatingHourRequest;
use App\Http\Resources\HospitalOperatingHourResource;
use App\Models\HospitalOperatingHour;
use App\Services\HospitalOperatingHourService;



class HospitalOperatingHourController extends Controller
{


public function __construct(
protected HospitalOperatingHourService $service
){}




public function index()
{


$this->authorize(
'viewAny',
HospitalOperatingHour::class
);


return HospitalOperatingHourResource::collection(

$this->service->all()

);


}




public function store(StoreOperatingHourRequest $request)
{


$this->authorize(
'create',
HospitalOperatingHour::class
);


$data =
$this->service->create(
$request->validated()
);



return new HospitalOperatingHourResource($data);


}





public function show(
HospitalOperatingHour $operatingHour
)
{


$this->authorize(
'view',
$operatingHour
);


return new HospitalOperatingHourResource(
$operatingHour
);


}





public function update(
UpdateOperatingHourRequest $request,
HospitalOperatingHour $operatingHour
)
{


$this->authorize(
'update',
$operatingHour
);



$data =
$this->service->update(
$operatingHour,
$request->validated()
);



return new HospitalOperatingHourResource($data);


}




public function destroy(
HospitalOperatingHour $operatingHour
)
{


$this->authorize(
'delete',
$operatingHour
);



$this->service->delete(
$operatingHour
);



return response()->json([

'message'
=>
'Operating hour deleted successfully'

]);


}


}