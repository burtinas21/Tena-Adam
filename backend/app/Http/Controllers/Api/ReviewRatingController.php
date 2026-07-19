<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReviewRatingService;
use App\Http\Resources\ReviewRatingResource;
use App\Http\Requests\Api\Review\StoreReviewRatingRequest;
use App\Http\Requests\Api\Review\UpdateReviewRatingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;


class ReviewRatingController extends Controller
{

    public function __construct(
        private ReviewRatingService $reviewService
    ) {
    }


    public function store(
       StoreReviewRatingRequest  $request
    ): JsonResponse {

        try {

            $review = $this->reviewService
                ->createReview(
                    $request->validated()
                );


            return response()->json([

                'success' => true,

                'message' =>
                    'Review submitted successfully.',

                'data' =>
                    new ReviewRatingResource($review)

            ],201);


        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }



    public function doctorReviews(
        string $doctorId
    ): JsonResponse {


        $reviews = $this->reviewService
            ->getDoctorReviews($doctorId);


        return response()->json([

            'success'=>true,

            'data'=>
                ReviewRatingResource::collection(
                    $reviews
                )

        ]);

    }




    public function patientReviews(
        string $patientId
    ): JsonResponse {


        $reviews = $this->reviewService
            ->getPatientReviews($patientId);


        return response()->json([

            'success'=>true,

            'data'=>
                ReviewRatingResource::collection(
                    $reviews
                )

        ]);

    }




    public function update(
        UpdateReviewRatingRequest $request,
        string $reviewId
    ): JsonResponse {


        try {


            $review = $this->reviewService
                ->updateReview(

                    $reviewId,

                    $request->validated()

                );


            return response()->json([

                'success'=>true,

                'message'=>
                    'Review updated successfully.',

                'data'=>
                    new ReviewRatingResource($review)

            ]);



        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }




    public function destroy(
        string $reviewId
    ): JsonResponse {


        try {


            $this->reviewService
                ->deleteReview($reviewId);


            return response()->json([

                'success'=>true,

                'message'=>
                    'Review deleted successfully.'

            ]);



        } catch (ValidationException $e) {


            return response()->json([

                'success'=>false,

                'errors'=>$e->errors()

            ],422);

        }

    }





    public function doctorRating(
        string $doctorId
    ): JsonResponse {


        $rating = $this->reviewService
            ->getDoctorAverageRating(
                $doctorId
            );


        return response()->json([

            'success'=>true,

            'data'=>$rating

        ]);

    }

}