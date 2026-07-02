import { Head, Link, useForm } from '@inertiajs/react';
import RubricController from '@/actions/App/Http/Controllers/Adviser/RubricController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';

export default function CreateRubric() {
    const form = useForm({
        title: '',
        curriculum_subject_id: '',
        is_template: false,
        criteria: [] as { name: string; weight: string; levels: { score: string; description: string }[] }[],
    });

    function addCriterion() {
        form.setData('criteria', [
            ...form.data.criteria,
            { name: '', weight: '', levels: [{ score: '', description: '' }] },
        ]);
    }

    function removeCriterion(index: number) {
        form.setData('criteria', form.data.criteria.filter((_, i) => i !== index));
    }

    function addLevel(criterionIndex: number) {
        const updated = [...form.data.criteria];
        updated[criterionIndex].levels.push({ score: '', description: '' });
        form.setData('criteria', updated);
    }

    function submit() {
        form.post(RubricController.store().url, { preserveScroll: true });
    }

    return (
        <>
            <Head title="Create Rubric" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Create Rubric" description="Define scoring criteria and performance levels" />

                <Card>
                    <CardContent className="space-y-4 pt-6">
                        <div className="space-y-1">
                            <Label htmlFor="title">Rubric Title</Label>
                            <Input id="title" value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} />
                            <InputError message={form.errors.title} />
                        </div>

                        <div className="space-y-4">
                            <div className="flex items-center justify-between">
                                <Label>Criteria</Label>
                                <Button type="button" variant="outline" size="sm" onClick={addCriterion}>
                                    Add Criterion
                                </Button>
                            </div>
                            <InputError message={form.errors.criteria} />

                            {form.data.criteria.map((criterion, ci) => (
                                <div key={ci} className="space-y-2 rounded-lg border p-4">
                                    <div className="flex items-center justify-between">
                                        <span className="text-sm font-medium">Criterion {ci + 1}</span>
                                        {form.data.criteria.length > 1 && (
                                            <Button type="button" variant="ghost" size="sm" onClick={() => removeCriterion(ci)}>
                                                Remove
                                            </Button>
                                        )}
                                    </div>

                                    <div className="grid grid-cols-2 gap-4">
                                        <div className="space-y-1">
                                            <Label>Name</Label>
                                            <Input
                                                value={criterion.name}
                                                onChange={(e) => {
                                                    const updated = [...form.data.criteria];
                                                    updated[ci].name = e.target.value;
                                                    form.setData('criteria', updated);
                                                }}
                                                placeholder="e.g. Content Quality"
                                            />
                                        </div>
                                        <div className="space-y-1">
                                            <Label>Weight (%)</Label>
                                            <Input
                                                type="number"
                                                min="0"
                                                max="100"
                                                value={criterion.weight}
                                                onChange={(e) => {
                                                    const updated = [...form.data.criteria];
                                                    updated[ci].weight = e.target.value;
                                                    form.setData('criteria', updated);
                                                }}
                                            />
                                        </div>
                                    </div>

                                    <div className="space-y-2">
                                        <div className="flex items-center justify-between">
                                            <Label className="text-xs text-muted-foreground">Performance Levels</Label>
                                            <Button type="button" variant="ghost" size="sm" onClick={() => addLevel(ci)}>
                                                Add Level
                                            </Button>
                                        </div>
                                        {criterion.levels.map((level, li) => (
                                            <div key={li} className="grid grid-cols-2 gap-4">
                                                <div className="space-y-1">
                                                    <Label className="text-xs">Score</Label>
                                                    <Input
                                                        type="number"
                                                        value={level.score}
                                                        onChange={(e) => {
                                                            const updated = [...form.data.criteria];
                                                            updated[ci].levels[li].score = e.target.value;
                                                            form.setData('criteria', updated);
                                                        }}
                                                        placeholder="e.g. 4"
                                                    />
                                                </div>
                                                <div className="space-y-1">
                                                    <Label className="text-xs">Description</Label>
                                                    <Input
                                                        value={level.description}
                                                        onChange={(e) => {
                                                            const updated = [...form.data.criteria];
                                                            updated[ci].levels[li].description = e.target.value;
                                                            form.setData('criteria', updated);
                                                        }}
                                                        placeholder="e.g. Excellent"
                                                    />
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                <div className="flex justify-end gap-2">
                    <Link href={RubricController.index()}>
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button onClick={submit} disabled={form.processing}>
                        Create Rubric
                    </Button>
                </div>
            </div>
        </>
    );
}

CreateRubric.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Rubrics', href: RubricController.index() },
        { title: 'Create', href: RubricController.create() },
    ],
};
