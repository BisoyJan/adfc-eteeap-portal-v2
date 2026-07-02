import { useForm } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { AssessmentCriterion, AssessmentFormRecord, AssessmentVerdict } from '@/types';

type Props = {
    actionUrl: string;
    existing?: AssessmentFormRecord | null;
};

const emptyCriterion: AssessmentCriterion = { name: '', score: 0, max_score: 10, comments: '' };

export default function AssessmentCriteriaForm({ actionUrl, existing }: Props) {
    const [criteria, setCriteria] = useState<AssessmentCriterion[]>(existing?.criteria ?? [{ ...emptyCriterion }]);

    const form = useForm<{ criteria: AssessmentCriterion[]; verdict: AssessmentVerdict }>({
        criteria,
        verdict: existing?.verdict ?? 'pass',
    });

    function updateCriterion(index: number, field: keyof AssessmentCriterion, value: string | number) {
        const next = criteria.map((criterion, i) => (i === index ? { ...criterion, [field]: value } : criterion));
        setCriteria(next);
        form.setData('criteria', next);
    }

    function addCriterion() {
        const next = [...criteria, { ...emptyCriterion }];
        setCriteria(next);
        form.setData('criteria', next);
    }

    function removeCriterion(index: number) {
        const next = criteria.filter((_, i) => i !== index);
        setCriteria(next);
        form.setData('criteria', next);
    }

    function submit() {
        form.post(actionUrl, { preserveScroll: true });
    }

    return (
        <div className="space-y-4">
            {existing?.submitted_at && (
                <p className="text-muted-foreground text-sm">
                    Last submitted {existing.submitted_at} &middot; total {existing.total_score}/{existing.max_score}
                </p>
            )}

            {criteria.map((criterion, index) => (
                <div key={index} className="grid gap-2 rounded-lg border p-3 sm:grid-cols-4">
                    <div className="sm:col-span-2">
                        <Label>Criterion</Label>
                        <Input value={criterion.name} onChange={(e) => updateCriterion(index, 'name', e.target.value)} />
                    </div>
                    <div>
                        <Label>Score</Label>
                        <Input type="number" value={criterion.score} onChange={(e) => updateCriterion(index, 'score', Number(e.target.value))} />
                    </div>
                    <div>
                        <Label>Max score</Label>
                        <Input
                            type="number"
                            value={criterion.max_score}
                            onChange={(e) => updateCriterion(index, 'max_score', Number(e.target.value))}
                        />
                    </div>
                    <div className="sm:col-span-4">
                        <Label>Comments</Label>
                        <Textarea value={criterion.comments ?? ''} onChange={(e) => updateCriterion(index, 'comments', e.target.value)} />
                    </div>
                    <div className="sm:col-span-4">
                        <Button type="button" variant="outline" size="sm" onClick={() => removeCriterion(index)}>
                            Remove criterion
                        </Button>
                    </div>
                </div>
            ))}

            <Button type="button" variant="outline" onClick={addCriterion}>
                Add criterion
            </Button>

            <div className="flex items-center gap-2">
                <Label>Verdict</Label>
                {(['pass', 'conditional', 'fail'] as const).map((verdict) => (
                    <Button
                        key={verdict}
                        type="button"
                        size="sm"
                        variant={form.data.verdict === verdict ? 'default' : 'outline'}
                        onClick={() => form.setData('verdict', verdict)}
                    >
                        {verdict}
                    </Button>
                ))}
            </div>
            <InputError message={form.errors.verdict} />

            <Button onClick={submit} disabled={form.processing}>
                Submit assessment
            </Button>
        </div>
    );
}
