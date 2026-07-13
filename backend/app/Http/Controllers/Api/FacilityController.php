<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFacilityRequest;
use App\Http\Requests\UpdateFacilityRequest;
use App\Http\Resources\FacilityResource;
use App\Models\Facility;
use App\Services\FacilityService;



class FacilityController extends Controller
{


public function __construct(
protected FacilityService $facilityService
){}




public function index()
{


$this->authorize(
'viewAny',
Facility::class
);


return FacilityResource::collection(

$this->facilityService->all()

);


}





public function store(StoreFacilityRequest $request)
{


$this->authorize(
'create',
Facility::class
);



$facility =
$this->facilityService->create(
$request->validated()
);



return new FacilityResource($facility);


}





public function show(Facility $facility)
{


$this->authorize(
'view',
$facility
);


return new FacilityResource($facility);


}





public function update(
UpdateFacilityRequest $request,
Facility $facility
)
{


$this->authorize(
'update',
$facility
);



$facility =
$this->facilityService->update(
$facility,
$request->validated()
);



return new FacilityResource($facility);


}





public function destroy(Facility $facility)
{


$this->authorize(
'delete',
$facility
);



$this->facilityService
->delete($facility);



return response()->json([

'message'
=>
'Facility deleted successfully'

]);


}


}