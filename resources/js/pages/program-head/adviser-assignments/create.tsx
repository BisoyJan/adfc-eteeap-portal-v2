import { Head, Link, useForm } from '@inertiajs/react';
import AdviserAssignmentController from '@/actions/App/Http/Controllers/ProgramHead/AdviserAssignmentController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { dashboard } from '@/routes';

type UserOption = {
    id: number;
    name: string;
    email: string;
};

type PageProps = {
    advisers: UserOption[];
    students: UserOption[];
};

export default function CreateAdviserAssignment({ advisers, students }: PageProps) {
    const form = useForm({
        student_id: '',
        adviser_id: '',
        term: '',
    });

    function submit() {
        form.post(AdviserAssignmentController.store().url, { preserveScroll: true });
    }

    return (
        <>
            <Head title="Assign Adviser" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Assign Adviser" description="Select a student and an adviser" />

                <InputError message={form.errors.student_id} />
                <InputError message={form.errors.adviser_id} />

                <Card>
                    <CardContent className="space-y-4 pt-6">
                        <div className="space-y-1">
                            <Label htmlFor="student">Student</Label>
                            <Select value={form.data.student_id} onValueChange={(v) => form.setData('student_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select student" /></SelectTrigger>
                                <SelectContent>
                                    {students.map((s) => (
                                        <SelectItem key={s.id} value={String(s.id)}>{s.name} ({s.email})</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="adviser">Adviser</Label>
                            <Select value={form.data.adviser_id} onValueChange={(v) => form.setData('adviser_id', v)}>
                                <SelectTrigger><SelectValue placeholder="Select adviser" /></SelectTrigger>
                                <SelectContent>
                                    {advisers.map((a) => (
                                        <SelectItem key={a.id} value={String(a.id)}>{a.name} ({a.email})</SelectItem>
                                    ))}
                                </SelectContent>
                            </Select>
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="term">Term (optional)</Label>
                            <input
                                id="term"
                                className="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                placeholder="e.g. 1st Sem 2026-2027"
                                value={form.data.term}
                                onChange={(e) => form.setData('term', e.target.value)}
                            />
                            <InputError message={form.errors.term} />
                        </div>
                    </CardContent>
                </Card>

                <div className="flex justify-end gap-2">
                    <Link href={AdviserAssignmentController.index()}>
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button onClick={submit} disabled={form.processing}>
                        Assign
                    </Button>
                </div>
            </div>
        </>
    );
}

CreateAdviserAssignment.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Adviser Assignments', href: AdviserAssignmentController.index() },
        { title: 'Assign', href: AdviserAssignmentController.create() },
    ],
};
