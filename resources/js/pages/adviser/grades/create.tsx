import { Head, useForm } from '@inertiajs/react';
import GradeController from '@/actions/App/Http/Controllers/Adviser/GradeController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { dashboard } from '@/routes';

type EnrolledSubject = {
    id: number;
    subject_code: string;
    subject_title: string;
};

type StudentRecord = {
    id: number;
    name: string;
    email: string;
    enrolled_subjects: EnrolledSubject[];
};

type RubricRecord = {
    id: number;
    title: string;
};

type PageProps = {
    students: StudentRecord[];
    rubrics: RubricRecord[];
};

export default function CreateGrade({ students, rubrics }: PageProps) {
    const { data, setData, post, processing, errors } = useForm({
        enrollment_subject_id: '',
        grade: '',
        grade_point: '',
        rubric_id: '',
        rubric_score: '',
    });

    function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        post(GradeController.store().url);
    }

    return (
        <>
            <Head title="Enter Grade" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Enter Grade" description="Record a grade for one of your advisees" />

                <Card>
                    <CardHeader>
                        <CardTitle>Grade Form</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div className="space-y-2">
                                <Label htmlFor="enrollment_subject_id">Student / Subject</Label>
                                <Select
                                    value={data.enrollment_subject_id}
                                    onValueChange={(v) => setData('enrollment_subject_id', v)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select a subject" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        {students.map((s) =>
                                            s.enrolled_subjects.map((es) => (
                                                <SelectItem key={es.id} value={String(es.id)}>
                                                    {s.name} — {es.subject_code}
                                                </SelectItem>
                                            )),
                                        )}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.enrollment_subject_id} />
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="grade">Grade</Label>
                                <Input
                                    id="grade"
                                    value={data.grade}
                                    onChange={(e) => setData('grade', e.target.value)}
                                    placeholder="e.g. 1.75, 88, CE, T"
                                />
                                <InputError message={errors.grade} />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-2">
                                    <Label htmlFor="grade_point">Grade Point</Label>
                                    <Input
                                        id="grade_point"
                                        type="number"
                                        step="0.1"
                                        value={data.grade_point}
                                        onChange={(e) => setData('grade_point', e.target.value)}
                                        placeholder="1.0 – 5.0"
                                    />
                                    <InputError message={errors.grade_point} />
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="rubric_score">Rubric Score</Label>
                                    <Input
                                        id="rubric_score"
                                        type="number"
                                        step="0.01"
                                        value={data.rubric_score}
                                        onChange={(e) => setData('rubric_score', e.target.value)}
                                        placeholder="0 – 100"
                                    />
                                    <InputError message={errors.rubric_score} />
                                </div>
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="rubric_id">Rubric</Label>
                                <Select
                                    value={data.rubric_id}
                                    onValueChange={(v) => setData('rubric_id', v)}
                                >
                                    <SelectTrigger>
                                        <SelectValue placeholder="Select rubric (optional)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value=" ">None</SelectItem>
                                        {rubrics.map((r) => (
                                            <SelectItem key={r.id} value={String(r.id)}>
                                                {r.title}
                                            </SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                                <InputError message={errors.rubric_id} />
                            </div>

                            <div className="flex gap-2">
                                <Button type="submit" disabled={processing}>
                                    Save Draft
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

CreateGrade.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Grades', href: GradeController.index() },
        { title: 'Enter Grade', href: GradeController.create() },
    ],
};
