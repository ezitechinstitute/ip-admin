<?php

namespace App\Http\Controllers;

use App\Models\GeneratedCertificate;
use Illuminate\Http\Request;

class GeneratedCertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $path = $request->file('template')->store('certificates/templates');

        CertificateTemplate::create([
            'type' => $request->type,
            'template_file' => $path
        ]);

        return back()->with('success','Template uploaded successfully');
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GeneratedCertificate $generatedCertificate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GeneratedCertificate $generatedCertificate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, GeneratedCertificate $generatedCertificate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GeneratedCertificate $generatedCertificate)
    {
        //
    }
}
