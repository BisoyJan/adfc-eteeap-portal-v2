<?php

namespace App\Http\Controllers;

use App\Models\CurriculumSubject;
use Inertia\Inertia;
use Inertia\Response;

class CurriculumController extends Controller
{
    /**
     * Publicly list active curriculum subjects.
     */
    public function index(): Response
    {
        $subjects = CurriculumSubject::query()
            ->where('is_active', true)
            ->orderBy('year_level')
            ->orderBy('semester')
            ->orderBy('code')
            ->get(['id', 'code', 'title', 'units', 'category', 'year_level', 'semester']);

        return Inertia::render('curriculum/subjects', [
            'subjects' => $subjects,
        ]);
    }
}
