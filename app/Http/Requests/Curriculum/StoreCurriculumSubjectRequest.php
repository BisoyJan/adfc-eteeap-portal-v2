<?php

namespace App\Http\Requests\Curriculum;

use App\Enums\SubjectCategory;
use App\Models\CurriculumSubject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCurriculumSubjectRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $subject = $this->route('subject');
        $subjectId = $subject instanceof CurriculumSubject ? $subject->id : null;

        return [
            'code' => ['required', 'string', 'max:20', 'unique:curriculum_subjects,code,'.$subjectId],
            'title' => ['required', 'string', 'max:200'],
            'units' => ['required', 'numeric', 'min:0', 'max:99'],
            'prerequisite_codes' => ['nullable', 'array'],
            'prerequisite_codes.*' => ['string', 'max:20'],
            'category' => ['required', new Enum(SubjectCategory::class)],
            'year_level' => ['nullable', 'integer', 'min:1', 'max:4'],
            'semester' => ['nullable', 'integer', 'in:0,1,2'],
            'learning_outcomes' => ['nullable', 'array'],
            'learning_outcomes.*' => ['string'],
            'is_active' => ['boolean'],
            'curriculum_version' => ['nullable', 'string', 'max:50'],
        ];
    }
}
