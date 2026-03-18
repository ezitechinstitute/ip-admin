<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\CertificateRequest;
use Barryvdh\DomPDF\Facade\Pdf;
$r = CertificateRequest::find(1);
if (!$r) {
    echo "request missing\n";
    exit(1);
}
$html = '<html><body><h1>Test Certificate</h1><p>Intern: ' . e($r->intern_name) . '</p></body></html>';
$pdf = Pdf::loadHTML($html);
$pdf->setPaper('a4', 'portrait');
$pdfData = $pdf->output();
$folder = storage_path('app/certificates');
if (!file_exists($folder)) {
    mkdir($folder, 0755, true);
}
$filename = 'certificate_' . $r->certificate_request_id . '.pdf';
file_put_contents($folder . '/' . $filename, $pdfData);
$r->pdf_path = 'certificates/' . $filename;
$r->status = 'approved';
$r->approved_at = now();
$r->save();
echo "done\n";
