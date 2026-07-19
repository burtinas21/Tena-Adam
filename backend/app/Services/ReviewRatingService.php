<?php

namespace App\Services;

use App\Models\ReviewRating;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\HealthcareProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class ReviewRatingService
{

    public function createReview(array $data): ReviewRating
    {
        return DB::transaction(function () use ($data) {


            $appointment = Appointment::findOrFail(
                $data['appointment_id']
            );


            if ($appointment->patient_id !== $data['patient_id']) {

                throw ValidationException::withMessages([

                    'appointment' => [
                        'This appointment does not belong to this patient.'
                    ]

                ]);

            }


            if ($appointment->status !== 'completed') {

                throw ValidationException::withMessages([

                    'appointment' => [
                        'You can only review completed appointments.'
                    ]

                ]);

            }


            if (
                ReviewRating::where(
                    'appointment_id',
                    $data['appointment_id']
                )->exists()
            ) {

                throw ValidationException::withMessages([

                    'review' => [
                        'This appointment already has a review.'
                    ]

                ]);

            }


            $review = ReviewRating::create([

                'patient_id' =>
                    $data['patient_id'],

                'doctor_id' =>
                    $data['doctor_id'],

                'appointment_id' =>
                    $data['appointment_id'],

                'rating' =>
                    $data['rating'],

                'comment' =>
                    $data['comment'] ?? null,

                'is_anonymous' =>
                    $data['is_anonymous'] ?? false,

            ]);


            return $this->loadRelations($review);

        });
    }



    public function getDoctorReviews(
        string $doctorId
    ) {

        return ReviewRating::with([

            'patient.user',

            'appointment'

        ])
        ->where(
            'doctor_id',
            $doctorId
        )
        ->latest()
        ->get();

    }



    public function getPatientReviews(
        string $patientId
    ) {

        return ReviewRating::with([

            'doctor.user',

            'appointment'

        ])
        ->where(
            'patient_id',
            $patientId
        )
        ->latest()
        ->get();

    }




    public function updateReview(
        string $reviewId,
        array $data
    ): ReviewRating {

        try {


            return DB::transaction(function () use (
                $reviewId,
                $data
            ) {


                $review = $this->findReviewOrFail(
                    $reviewId
                );


                $review->update([


                    'rating' =>
                        $data['rating']
                        ??
                        $review->rating,


                    'comment' =>
                        $data['comment']
                        ??
                        $review->comment,


                    'is_anonymous' =>
                        $data['is_anonymous']
                        ??
                        $review->is_anonymous,

                ]);


                return $this->loadRelations(
                    $review->fresh()
                );


            });


        } catch (ModelNotFoundException $e) {


            throw ValidationException::withMessages([

                'review' => [
                    'Review not found.'
                ]

            ]);

        }

    }




    public function deleteReview(
        string $reviewId
    ): bool {

        try {


            return DB::transaction(function () use ($reviewId) {


                $review = $this->findReviewOrFail(
                    $reviewId
                );


                return $review->delete();


            });


        } catch (ModelNotFoundException $e) {


            throw ValidationException::withMessages([

                'review' => [
                    'Review not found.'
                ]

            ]);

        }

    }




    public function getDoctorAverageRating(
        string $doctorId
    ): array {


        $reviews = ReviewRating::where(
            'doctor_id',
            $doctorId
        );


        return [

            'doctor_id' =>
                $doctorId,


            'total_reviews' =>
                $reviews->count(),


            'average_rating' =>
                round(
                    $reviews->avg('rating') ?? 0,
                    2
                )

        ];

    }




    private function findReviewOrFail(
        string $reviewId
    ): ReviewRating {

        return ReviewRating::findOrFail(
            $reviewId
        );

    }




    private function loadRelations(
        ReviewRating $review
    ): ReviewRating {


        return $review->load([

            'patient.user',

            'doctor.user',

            'appointment'

        ]);

    }

}