import { Head, Link, useForm } from '@inertiajs/react';
import ActivityController from '@/actions/App/Http/Controllers/Adviser/ActivityController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';

type StudentOption = {
    id: number;
    name: string;
    email: string;
};

type PageProps = {
    students: StudentOption[];
};

export default function CreateActivity({ students }: PageProps) {
    const form = useForm({
        student_id: '',
        curriculum_subject_id: '',
        activity_type: 'modular_study',
        title: '',
        description: '',
        due_date: '',
    });

    function submit() {
        form.post(ActivityController.store().url, { preserveScroll: true });
    }

    return (
        <>
            <Head title="Assign Activity" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Assign Enhancement Activity" description="Create a new activity for a student" />

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
                            <InputError message={form.errors.student_id} />
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="title">Activity Title</Label>
                            <Input id="title" value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} />
                            <InputError message={form.errors.title} />
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label htmlFor="type">Activity Type</Label>
                                <Select value={form.data.activity_type} onValueChange={(v) => form.setData('activity_type', v)}>
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="modular_study">Modular Study</SelectItem>
                                        <SelectItem value="case_analysis">Case Analysis</SelectItem>
                                        <SelectItem value="research">Research Activity</SelectItem>
                                        <SelectItem value="video_presentation">Video Presentation</SelectItem>
                                        <SelectItem value="webinar">Webinar Attendance</SelectItem>
                                        <SelectItem value="skills_demo">Skills Demonstration</SelectItem>
                                        <SelectItem value="reporting">Reporting</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={form.errors.activity_type} />
                            </div>
                            <div className="space-y-1">
                                <Label htmlFor="due_date">Due Date</Label>
                                <Input id="due_date" type="date" value={form.data.due_date} onChange={(e) => form.setData('due_date', e.target.value)} />
                                <InputError message={form.errors.due_date} />
                            </div>
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="description">Description / Instructions</Label>
                            <Textarea id="description" value={form.data.description} onChange={(e) => form.setData('description', e.target.value)} rows={4} />
                            <InputError message={form.errors.description} />
                        </div>
                    </CardContent>
                </Card>

                <div className="flex justify-end gap-2">
                    <Link href={ActivityController.index()}>
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button onClick={submit} disabled={form.processing}>
                        Assign Activity
                    </Button>
                </div>
            </div>
        </>
    );
}

CreateActivity.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Activities', href: ActivityController.index() },
        { title: 'Assign', href: ActivityController.create() },
    ],
};
