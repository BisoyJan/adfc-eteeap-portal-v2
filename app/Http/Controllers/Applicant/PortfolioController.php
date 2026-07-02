<?php

namespace App\Http\Controllers\Applicant;

use App\Http\Controllers\Controller;
use App\Services\ApplicationService;
use App\Services\DocumentService;
use App\Services\PortfolioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function __construct(
        private readonly PortfolioService $portfolios,
        private readonly ApplicationService $applications,
        private readonly DocumentService $documents,
    ) {}

    /**
     * Show the applicant's e-portfolio.
     */
    public function show(Request $request): Response
    {
        $application = $this->applications->currentDraftFor($request->user());
        $portfolio = $this->portfolios->forApplication($application);

        return Inertia::render('applicant/portfolio/index', [
            'portfolio' => $portfolio->load('documents'),
            'progress' => $this->documents->progress($request->user()),
        ]);
    }

    /**
     * Submit the portfolio for assessment.
     */
    public function submit(Request $request): RedirectResponse
    {
        $application = $this->applications->currentDraftFor($request->user());

        try {
            $this->portfolios->submit($application);
        } catch (ValidationException $exception) {
            return back()->withErrors($exception->errors());
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Portfolio submitted.')]);

        return to_route('applicant.portfolio.show');
    }

    public function download(Request $request): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $application = $this->applications->currentDraftFor($request->user());
        $portfolio = $this->portfolios->forApplication($application);

        abort_if(is_null($portfolio->merged_pdf_path), 404);

        return Storage::disk(config('eteeap.document.storage_disk'))
            ->download($portfolio->merged_pdf_path, 'portfolio.pdf');
    }
}
