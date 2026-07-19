<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\MedicalDocument;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class MedicalDocumentService
{
    public function uploadDocument(array $data): MedicalDocument
    {
        return DB::transaction(function () use ($data) {

            $patient = Patient::find($data['patient_id']);

            if (!$patient) {

                throw ValidationException::withMessages([

                    'patient_id' => [
                        'Patient not found.'
                    ]

                ]);

            }

            $file = $data['file'];

            $filePath = $file->store(
                'medical-documents',
                'public'
            );

            $document = MedicalDocument::create([

                'patient_id' => $data['patient_id'],

                'encounter_id' => $data['encounter_id'] ?? null,

                'file_name' => $file->getClientOriginalName(),

                'file_url' => $filePath,

                'file_type' => $file->getMimeType(),

                'file_size' => $file->getSize(),

                'document_type' => $data['document_type'],

                'uploaded_by' => auth()->id(),

                'description' => $data['description'] ?? null,

            ]);

            return $document->load([

                'patient',

                'encounter',

                'uploader',

            ]);

        });
    }
    public function updateDocument(
    MedicalDocument $document,
    array $data
): MedicalDocument
{

    return DB::transaction(function () use (
        $document,
        $data
    ) {

        if (isset($data['file'])) {

            if (
                $document->file_url &&
                Storage::disk('public')->exists(
                    $document->file_url
                )
            ) {

                Storage::disk('public')->delete(
                    $document->file_url
                );

            }

            $file = $data['file'];

            $document->file_name =
                $file->getClientOriginalName();

            $document->file_url =
                $file->store(
                    'medical-documents',
                    'public'
                );

            $document->file_type =
                $file->getMimeType();

            $document->file_size =
                $file->getSize();

        }

        $document->document_type =
            $data['document_type']
            ?? $document->document_type;

        $document->description =
            $data['description']
            ?? $document->description;

        $document->save();

        return $document->load([

            'patient',

            'encounter',

            'uploader',

        ]);

    });

}
public function deleteDocument(
    MedicalDocument $document
): bool
{

    return DB::transaction(function () use (
        $document
    ) {

        if (
            $document->file_url &&
            Storage::disk('public')->exists(
                $document->file_url
            )
        ) {

            Storage::disk('public')->delete(
                $document->file_url
            );

        }

        $document->delete();

        return true;

    });

}
public function getPatientDocuments(
    string $patientId
)
{
    return MedicalDocument::with([

        'patient',

        'encounter',

        'uploader',

    ])
    ->where(
        'patient_id',
        $patientId
    )
    ->latest()
    ->get();
}
public function getEncounterDocuments(
    string $encounterId
)
{
    return MedicalDocument::with([

        'patient',

        'encounter',

        'uploader',

    ])
    ->where(
        'encounter_id',
        $encounterId
    )
    ->latest()
    ->get();
}
public function downloadDocument(
    MedicalDocument $document
)
{
    if (
        !Storage::disk('public')->exists(
            $document->file_url
        )
    ) {

        throw ValidationException::withMessages([

            'document' => [

                'Document file not found.'

            ]

        ]);

    }

    return Storage::disk('public')->download(

        $document->file_url,

        $document->file_name

    );
}
}