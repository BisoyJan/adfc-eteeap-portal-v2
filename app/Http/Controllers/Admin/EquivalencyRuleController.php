<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CurriculumSubject;
use App\Models\EquivalencyRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EquivalencyRuleController extends Controller
{
    /**
     * List all equivalency rules.
     */
    public function index(): Response
    {
        $rules = EquivalencyRule::query()
            ->with('curriculumSubject:id,code,title')
            ->orderBy('priority')
            ->get();

        $subjects = CurriculumSubject::where('is_active', true)->get(['id', 'code', 'title']);

        return Inertia::render('admin/curriculum-rules/index', [
            'rules' => $rules,
            'subjects' => $subjects,
        ]);
    }

    /**
     * Store a new equivalency rule.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'rule_type' => ['required', 'string', 'in:direct,experience,certification,training,prior_education'],
            'condition_key' => ['required', 'string', 'max:100'],
            'condition_value' => ['required', 'string'],
            'curriculum_subject_id' => ['required', 'exists:curriculum_subjects,id'],
            'equivalent_credits' => ['nullable', 'numeric', 'min:0'],
            'priority' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        EquivalencyRule::create($validated);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Equivalency rule created.')]);

        return back();
    }
}
