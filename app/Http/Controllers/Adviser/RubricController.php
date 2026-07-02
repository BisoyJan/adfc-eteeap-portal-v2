<?php

namespace App\Http\Controllers\Adviser;

use App\Http\Controllers\Controller;
use App\Http\Requests\Adviser\StoreRubricRequest;
use App\Models\Rubric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RubricController extends Controller
{
    /**
     * List rubrics created by the adviser (or templates).
     */
    public function index(Request $request): Response
    {
        $rubrics = Rubric::query()
            ->with('curriculumSubject:id,code,title')
            ->where(function ($q) use ($request) {
                $q->where('is_template', true)
                    ->orWhere('user_id', $request->user()->id);
            })
            ->latest()
            ->get();

        return Inertia::render('adviser/rubrics/index', [
            'rubrics' => $rubrics,
        ]);
    }

    /**
     * Show the rubric creation form.
     */
    public function create(): Response
    {
        return Inertia::render('adviser/rubrics/create');
    }

    /**
     * Store a new rubric.
     */
    public function store(StoreRubricRequest $request): RedirectResponse
    {
        /** @var array<int, array{name: string, weight: float, levels: array<int, array{score: float, description: string}>}> $criteria */
        $criteria = $request->validated('criteria');

        $maxScore = 0.0;

        foreach ($criteria as $criterion) {
            $highestLevelScore = array_reduce(
                $criterion['levels'],
                fn (float $carry, array $level) => max($carry, (float) $level['score']),
                0.0,
            );
            $maxScore += $highestLevelScore * ($criterion['weight'] / 100);
        }

        Rubric::create([
            'user_id' => $request->user()->id,
            'curriculum_subject_id' => $request->input('curriculum_subject_id'),
            'title' => $request->input('title'),
            'criteria' => $criteria,
            'max_score' => round($maxScore, 2),
            'is_template' => $request->boolean('is_template'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Rubric created.')]);

        return to_route('adviser.rubrics.index');
    }
}
