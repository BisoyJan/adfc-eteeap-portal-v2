<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\PortfolioStatus;
use App\Models\Application;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use setasign\Fpdi\Fpdi;

class PortfolioService
{
    public function __construct(private readonly DocumentService $documents) {}

    /**
     * Get or create the portfolio associated with an application.
     */
    public function forApplication(Application $application): Portfolio
    {
        return $application->portfolio ?? $application->portfolio()->create([
            'status' => PortfolioStatus::Building,
        ]);
    }

    /**
     * Submit the portfolio, requiring every required document to be verified first.
     *
     * @throws ValidationException
     */
    public function submit(Application $application): Portfolio
    {
        $progress = $this->documents->progress($application->user);

        if ($progress['verified_required'] < $progress['total_required']) {
            throw ValidationException::withMessages([
                'portfolio' => 'All required documents must be verified before submitting the portfolio.',
            ]);
        }

        $portfolio = $this->forApplication($application);

        $verifiedDocuments = $application->user->documents()
            ->where('status', DocumentStatus::Verified)
            ->get()
            ->values();

        $portfolio->documents()->sync(
            $verifiedDocuments->mapWithKeys(fn ($document, int $index) => [$document->id => ['position' => $index]]),
        );

        $portfolio->update([
            'status' => PortfolioStatus::Submitted,
            'submitted_at' => now(),
        ]);

        try {
            $this->mergeToPdf($portfolio);
        } catch (\Throwable) {
            // Merge is best-effort; portfolio submission succeeds regardless.
        }

        return $portfolio->refresh();
    }

    /**
     * Concatenate all portfolio documents into a single PDF.
     */
    public function mergeToPdf(Portfolio $portfolio): void
    {
        $disk = config('eteeap.document.storage_disk');
        $documents = $portfolio->documents;

        if ($documents->isEmpty()) {
            return;
        }

        $pdf = new Fpdi;

        foreach ($documents as $document) {
            $fullPath = Storage::disk($disk)->path($document->file_path);

            if (! file_exists($fullPath)) {
                continue;
            }

            if ($document->mime_type === 'application/pdf') {
                $pageCount = $pdf->setSourceFile($fullPath);
                for ($i = 1; $i <= $pageCount; $i++) {
                    $templateId = $pdf->importPage($i);
                    $size = $pdf->getTemplateSize($templateId);
                    if (! is_array($size)) {
                        continue;
                    }
                    $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                    $pdf->addPage($orientation, [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
            } elseif (str_starts_with($document->mime_type, 'image/')) {
                $pdf->addPage();
                $pdf->Image($fullPath, 0, 0, $pdf->GetPageWidth(), $pdf->GetPageHeight());
            }
        }

        $storagePath = 'portfolios/merged/' . $portfolio->id . '.pdf';
        Storage::disk($disk)->put($storagePath, $pdf->Output('S'));

        $portfolio->merged_pdf_path = $storagePath;
        $portfolio->save();
    }
}
