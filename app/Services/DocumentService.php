<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Models\Application;
use App\Models\Document;
use App\Models\User;
use App\Support\DocumentTypeRegistry;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use setasign\Fpdi\Fpdi;

class DocumentService
{
    /**
     * Store an uploaded document, versioning it against prior uploads of the same type.
     */
    public function store(User $user, Application $application, string $documentType, UploadedFile $file): Document
    {
        $this->validateFile($file);

        $nextVersion = 1 + (int) Document::query()
            ->where('user_id', $user->id)
            ->where('document_type', $documentType)
            ->max('version');

        $path = $file->store(
            config('eteeap.document.storage_path')."/{$user->id}/{$documentType}",
            config('eteeap.document.storage_disk'),
        );

        return Document::create([
            'user_id' => $user->id,
            'application_id' => $application->id,
            'document_type' => $documentType,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType(),
            'version' => $nextVersion,
            'status' => DocumentStatus::Uploaded,
        ]);
    }

    /**
     * @throws ValidationException
     */
    protected function validateFile(UploadedFile $file): void
    {
        /** @var array<int, string> $allowed */
        $allowed = config('eteeap.document.allowed_mime_types');

        if (! in_array($file->getMimeType(), $allowed, true)) {
            throw ValidationException::withMessages(['file' => 'Unsupported file type.']);
        }

        if ($file->getSize() > (int) config('eteeap.document.max_size')) {
            throw ValidationException::withMessages(['file' => 'File exceeds the maximum allowed size.']);
        }
    }

    /**
     * Build the 18-item document checklist for a user, showing the latest
     * version uploaded for each document type.
     *
     * @return array<int, array{key: string, label: string, required: bool, document: ?Document, status: ?string}>
     */
    public function checklist(User $user): array
    {
        $latestByType = $user->documents()
            ->orderByDesc('version')
            ->get()
            ->groupBy('document_type')
            ->map(fn ($documents) => $documents->first());

        $checklist = [];

        foreach (DocumentTypeRegistry::all() as $type) {
            /** @var Document|null $document */
            $document = $latestByType->get($type['key']);

            $checklist[] = [
                'key' => $type['key'],
                'label' => $type['label'],
                'required' => $type['required'],
                'document' => $document,
                'status' => $document?->status->value,
            ];
        }

        return $checklist;
    }

    /**
     * Summarize checklist completeness for the required document types.
     *
     * @return array{total_required: int, uploaded_required: int, verified_required: int}
     */
    public function progress(User $user): array
    {
        $required = collect($this->checklist($user))->where('required', true);

        return [
            'total_required' => $required->count(),
            'uploaded_required' => $required->filter(fn (array $item) => $item['document'] !== null)->count(),
            'verified_required' => $required->filter(fn (array $item) => $item['status'] === DocumentStatus::Verified->value)->count(),
        ];
    }

    /**
     * Mark a document as verified.
     */
    public function verify(Document $document, User $verifier): void
    {
        $document->update([
            'status' => DocumentStatus::Verified,
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        $this->watermark($document);
    }

    /**
     * Stamp a verification watermark onto the document.
     */
    public function watermark(Document $document): void
    {
        $fullPath = Storage::disk(config('eteeap.document.storage_disk'))->path($document->file_path);

        if (! file_exists($fullPath)) {
            return;
        }

        $mime = $document->mime_type;

        try {
            if ($mime === 'application/pdf') {
                $this->applyPdfWatermark($fullPath);
            } elseif (str_starts_with($mime, 'image/')) {
                $this->applyImageWatermark($fullPath);
            } else {
                return;
            }
        } catch (\Throwable) {
            return;
        }

        $document->is_watermarked = true;
        $document->save();
    }

    protected function applyPdfWatermark(string $fullPath): void
    {
        $pdf = new Fpdi;
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

            $pdf->SetFont('Helvetica', 'B', 24);
            $pdf->SetTextColor(180, 180, 180);

            $cx = $size['width'] / 2;
            $cy = $size['height'] / 2;
            $angle = deg2rad(45);
            $cos = cos($angle);
            $sin = sin($angle);

            $pdf->_out('q');
            $pdf->_out(sprintf('1 0 0 1 %.2F %.2F cm', $cx, $cy));
            $pdf->_out(sprintf('%.3F %.3F %.3F %.3F 0 0 cm', $cos, $sin, -$sin, $cos));
            $pdf->SetXY(-$size['width'] / 2, -20);
            $pdf->Cell($size['width'], 12, 'VERIFIED', 0, 1, 'C');
            $pdf->SetX(-$size['width'] / 2);
            $pdf->Cell($size['width'], 12, 'ADFC ETEEAP', 0, 1, 'C');
            $pdf->SetX(-$size['width'] / 2);
            $pdf->Cell($size['width'], 12, date('Y-m-d'), 0, 1, 'C');
            $pdf->_out('Q');
        }

        $pdf->Output('F', $fullPath);
    }

    protected function applyImageWatermark(string $fullPath): void
    {
        $content = file_get_contents($fullPath);
        if ($content === false) {
            return;
        }

        $image = imagecreatefromstring($content);
        if ($image === false) {
            return;
        }

        $width = imagesx($image);
        $height = imagesy($image);
        $color = imagecolorallocatealpha($image, 150, 150, 150, 80);
        if ($color === false) {
            imagedestroy($image);
            return;
        }

        $fontSize = max(12, intval($width / 20));
        $text = 'VERIFIED - ADFC ETEEAP - ' . date('Y-m-d');

        $fontFile = $this->findWatermarkFont();
        if ($fontFile !== null) {
            $box = imagettfbbox($fontSize, 45, $fontFile, $text);
            if (is_array($box)) {
                $textWidth = abs($box[2] - $box[0]);
                $x = (int) (($width - $textWidth) / 2);
                $y = (int) ($height / 2);
                imagettftext($image, $fontSize, 45, $x, $y, $color, $fontFile, $text);
            }
        }

        imagepng($image, $fullPath);
        imagedestroy($image);
    }

    protected function findWatermarkFont(): ?string
    {
        $candidates = [
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
            '/usr/share/fonts/TTF/DejaVuSans-Bold.ttf',
            'C:/Windows/Fonts/arialbd.ttf',
            'C:/Windows/Fonts/ARIALBD.TTF',
            'C:/Windows/Fonts/ArialBD.ttf',
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Reject a document with a reason.
     */
    public function reject(Document $document, User $verifier, string $reason): void
    {
        $document->update([
            'status' => DocumentStatus::Rejected,
            'verified_by' => $verifier->id,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }
}
