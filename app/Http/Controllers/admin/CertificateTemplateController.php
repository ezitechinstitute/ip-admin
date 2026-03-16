<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;

class CertificateTemplateController extends Controller
{

    public function index()
    {
        $templates = CertificateTemplate::latest()->get();

        return view('pages.admin.certificates.templates', compact('templates'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'type' => 'required|in:internship,course',
            'template_file' => 'required|file|mimes:png,jpg,pdf'
        ]);

        $path = $request->file('template_file')
            ->store('certificate_templates','public');

        CertificateTemplate::create([
            'type' => $request->type,
            'template_path' => $path
        ]);

        return back()->with('success','Template uploaded successfully');
    }


    public function destroy($id)
    {
        $template = CertificateTemplate::findOrFail($id);

        $template->delete();

        return back()->with('success','Template deleted successfully');
    }

}