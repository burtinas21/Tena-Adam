<?php

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Department\StoreDepartmentRequest;
use App\Http\Requests\Api\Department\UpdateDepartmentRequest;
use App\Http\Resources\DepartmentResource;
use App\Models\Department;
use App\Services\DepartmentService;


class DepartmentController extends Controller
{


public function __construct(
    protected DepartmentService $departmentService
){}



public function index()
{


$this->authorize(
    'viewAny',
    Department::class
);


return DepartmentResource::collection(

    $this->departmentService->all()

);


}




public function store(StoreDepartmentRequest $request)
{


$this->authorize(
    'create',
    Department::class
);


$department =
$this->departmentService->create(
    $request->validated()
);


return new DepartmentResource($department);


}




public function show(Department $department)
{


$this->authorize(
    'view',
    $department
);


return new DepartmentResource($department);


}





public function update(
UpdateDepartmentRequest $request,
Department $department
)
{


$this->authorize(
    'update',
    $department
);


$department =
$this->departmentService->update(
    $department,
    $request->validated()
);


return new DepartmentResource($department);


}




public function destroy(Department $department)
{


$this->authorize(
    'delete',
    $department
);


$this->departmentService
->delete($department);



return response()->json([

'message'
=>
'Department deleted successfully'

]);


}


}