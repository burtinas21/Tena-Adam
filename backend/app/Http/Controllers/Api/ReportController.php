<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Http\Resources\ReportResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
class ReportController extends Controller
{

    public function __construct(
        private ReportService $reportService
    ) {
    }


    /**
     * Patient Statistics
     */
    public function getPatientStatistics(): JsonResponse
    {
        try {

            $data = $this->reportService
                ->getPatientStatistics();


            return response()->json([

                'success' => true,

                'data' => $data

            ]);


        } catch (ValidationException $e) {

            return response()->json([

                'success' => false,

                'errors' => $e->errors()

            ],422);

        }
    }



    /**
     * Appointment Report
     */
    public function getAppointmentReport(): JsonResponse
    {

        try {


            $data = $this->reportService
                ->getAppointmentReport();


            return response()->json([

                'success'=>true,

                'data'=>$data

            ]);


        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }




    /**
     * Doctor Workload
     */
    public function getDoctorWorkload(): JsonResponse
    {

        try {


            $data = $this->reportService
                ->getDoctorWorkload();


            return response()->json([

                'success'=>true,

                'data'=>$data

            ]);


        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }





    /**
     * Department Performance
     */
    public function getDepartmentPerformance(): JsonResponse
    {

        try {


            $data = $this->reportService
                ->getDepartmentPerformance();


            return response()->json([

                'success'=>true,

                'data'=>$data

            ]);


        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }





    /**
     * Telehealth Statistics
     */
    public function getTelehealthStatistics(): JsonResponse
    {

        try {


            $data = $this->reportService
                ->getTelehealthStatistics();


            return response()->json([

                'success'=>true,

                'data'=>$data

            ]);


        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }





    /**
     * Healthcare Trends
     */
    public function getHealthcareTrends(): JsonResponse
    {

        try {


            $data = $this->reportService
                ->getHealthcareTrends();


            return response()->json([

                'success'=>true,

                'data'=>$data

            ]);


        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }





    /**
     * Generate Custom Report
     */
    public function generateCustomReport(
        Request $request,
        string $reportId
    ): JsonResponse
    {

        try {


            $data = $this->reportService
                ->generateCustomReport(

                    $reportId,

                    $request->all()

                );


            return response()->json([

                'success'=>true,

                'data'=>$data

            ]);



        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }
    public function store(Request $request)
{

    $report = $this->reportService
        ->createReport(
            $request->all()
        );


    return response()->json([

        'success'=>true,

        'data'=>new ReportResource($report)

    ],201);

}
public function getDoctorRatingStatistics(): JsonResponse
{

    $data =
        $this->reportService
        ->getDoctorRatingStatistics();


    return response()->json([

        'success'=>true,

        'data'=>new ReportResource($data)
    ]);

}
public function exportExcel(
    string $type
): BinaryFileResponse
{
    return $this->reportService
        ->exportExcel($type);
}
public function exportCsv(
    string $type
): BinaryFileResponse
{
    return $this->reportService
        ->exportCsv($type);
}
public function exportPdf(
    string $type
)
{
    return $this->reportService
        ->exportPdf($type);
}
}