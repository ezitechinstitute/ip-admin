<?php

namespace App\Http\Controllers\manager_controllers;

use App\Http\Controllers\Controller;
use App\Models\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\GeneratedCertificate;
use App\Models\InternAccount;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Mail;
use App\Mail\CertificateMail;

use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{

    public function index()
{

    $requests = GeneratedCertificate::with('intern')
        ->where('status','pending')
        ->get();

    return view(
        'pages.manager.certificates.requests',
        compact('requests')
    );

}


    public function approve($id)
    {
        // $manager = new ImageManager(new Driver());

        $certificate = GeneratedCertificate::findOrFail($id);

        $intern = InternAccount::where('int_id',$certificate->intern_id)->first();

        $template = CertificateTemplate::find($certificate->template_id);

        $templatePath = storage_path('app/public/'.$template->template_path);

        $intern = InternAccount::where('int_id',$certificate->intern_id)->first();

        $pdf = Pdf::loadView('certificates.certificate',[
            'intern'=>$intern
        ]);

        $filename = 'certificates/certificate_'.$certificate->id.'.pdf';

        Storage::disk('public')->put($filename,$pdf->output());

        // $image = $manager->read($templatePath);

        // // Write Intern Name
        // $image->text($intern->name, 800, 600, function($font) {
        //     $font->size(60);
        //     $font->color('#000000');
        //     $font->align('center');
        // });

        // // Write Date
        // $image->text(date('Y-m-d'), 800, 700, function($font) {
        //     $font->size(30);
        //     $font->color('#000000');
        //     $font->align('center');
        // });

        // $filename = 'certificates/certificate_'.$certificate->id.'.png';

        // Storage::disk('public')->put($filename, (string) $image->encode());

        $certificate->certificate_path = $filename;
        $certificate->status = 'approved';

        $certificate->save();
        Mail::to($intern->email)->send(new CertificateMail($filename));

        return back()->with('success','Certificate generated successfully');

    }


    public function reject($id)
    {

        $certificate = GeneratedCertificate::findOrFail($id);

        $certificate->status = 'rejected';

        $certificate->save();

        return back()->with('success','Certificate rejected');
    }

}