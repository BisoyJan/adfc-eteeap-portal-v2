<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Http\Requests\Curriculum\StoreCurriculumSubjectRequest;
use App\Models\CurriculumSubject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CurriculumController extends Controller
{
    /**
     * List all curriculum subjects (public).
     */
    public function index(): Response
    {
        $subjects = CurriculumSubject::query()
            ->orderBy('year_level')
            ->orderBy('semester')
            ->orderBy('code')
            ->get();

        return Inertia::render('program-head/curriculum/index', [
            'subjects' => $subjects,
        ]);
    }

    /**
     * Show the subject creation form.
     */
    public function create(): Response
    {
        return Inertia::render('program-head/curriculum/create');
    }

    /**
     * Store a new curriculum subject.
     */
    public function store(StoreCurriculumSubjectRequest $request): RedirectResponse
    {
        CurriculumSubject::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subject created.')]);

        return to_route('program-head.curriculum.index');
    }

    /**
     * Show the subject edit form.
     */
    public function edit(CurriculumSubject $subject): Response
    {
        return Inertia::render('program-head/curriculum/edit', [
            'subject' => $subject,
        ]);
    }

    /**
     * Update an existing curriculum subject.
     */
    public function update(StoreCurriculumSubjectRequest $request, CurriculumSubject $subject): RedirectResponse
    {
        $subject->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Subject updated.')]);

        return to_route('program-head.curriculum.index');
    }

    /**
     * Create a new curriculum version snapshot.
     */
    public function newVersion(Request $request): RedirectResponse
    {
        $request->validate(['version' => ['required', 'string', 'max:50']]);

        $version = $request->input('version');

        CurriculumSubject::query()->update(['curriculum_version' => $version]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Curriculum version set to: ').$version]);

        return to_route('program-head.curriculum.index');
    }
}
