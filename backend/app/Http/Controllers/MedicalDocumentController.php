<?php

namespace App\Http\Controllers;

use App\Models\MedicalDocument;
use App\Http\Requests\StoreMedicalDocumentRequest;
use App\Http\Requests\UpdateMedicalDocumentRequest;

class MedicalDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMedicalDocumentRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalDocument $medicalDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalDocument $medicalDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicalDocumentRequest $request, MedicalDocument $medicalDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalDocument $medicalDocument)
    {
        //
    }
}
