<?php

namespace App\Http\Controllers\Vpaa;

use App\Exports\GenericArrayExport;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController
{
    public function __construct(
        protected ReportService $reportService,
    ) {}

    public function index(): Response
    {
        return Inertia::render('vpaa/reports/index', [
            'reports' => config('eteeap.reports'),
        ]);
    }

    /**
     * @return BinaryFileResponse|\Illuminate\Http\Response
     */
    public function show(string $type, Request $request)
    {
        $allowed = ['graduation_audit', 'timeline_analysis', 'pipeline', 'ched_compliance'];
        $reports = config('eteeap.reports', []);

        if (! in_array($type, $allowed, true) || ! isset($reports[$type])) {
            abort(404, "Report type '{$type}' not found.");
        }

        $method = Str::camel($type) . 'Report';
        $data = $this->reportService->$method();

        $title = $reports[$type];
        $format = $request->query('format', 'pdf');

        if ($format === 'excel') {
            $export = new GenericArrayExport($data['headers'], $data['rows']);

            return Excel::download($export, "{$type}-report.xlsx");
        }

        $pdf = Pdf::loadView('pdf.reports.generic-table', [
            'title' => $title,
            'headers' => $data['headers'],
            'rows' => $data['rows'],
        ]);

        return $pdf->download("{$type}-report.pdf");
    }
}
