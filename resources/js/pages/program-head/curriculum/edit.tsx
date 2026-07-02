import { Head, Link, useForm } from '@inertiajs/react';
import CurriculumController from '@/actions/App/Http/Controllers/ProgramHead/CurriculumController';
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
import { dashboard } from '@/routes';

type CurriculumSubject = {
    id: number;
    code: string;
    title: string;
    units: number;
    category: string;
    year_level: number | null;
    semester: number | null;
    curriculum_version: string | null;
    is_active: boolean;
};

type PageProps = {
    subject: CurriculumSubject;
};

export default function EditCurriculumSubject({ subject }: PageProps) {
    const form = useForm({
        code: subject.code,
        title: subject.title,
        units: String(subject.units),
        category: subject.category,
        year_level: String(subject.year_level ?? ''),
        semester: String(subject.semester ?? ''),
        curriculum_version: subject.curriculum_version ?? '',
        is_active: subject.is_active,
        prerequisite_codes: [] as string[],
        learning_outcomes: [] as string[],
    });

    function submit() {
        form.put(CurriculumController.update(subject.id).url, { preserveScroll: true });
    }

    return (
        <>
            <Head title={`Edit ${subject.code}`} />

            <div className="flex flex-col gap-6 p-4">
                <Heading title={`Edit ${subject.code} — ${subject.title}`} description="Update curriculum subject details" />

                <Card>
                    <CardContent className="space-y-4 pt-6">
                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label htmlFor="code">Subject Code</Label>
                                <Input id="code" value={form.data.code} onChange={(e) => form.setData('code', e.target.value)} />
                                <InputError message={form.errors.code} />
                            </div>
                            <div className="space-y-1">
                                <Label htmlFor="units">Units</Label>
                                <Input id="units" type="number" step="0.5" value={form.data.units} onChange={(e) => form.setData('units', e.target.value)} />
                                <InputError message={form.errors.units} />
                            </div>
                        </div>

                        <div className="space-y-1">
                            <Label htmlFor="title">Subject Title</Label>
                            <Input id="title" value={form.data.title} onChange={(e) => form.setData('title', e.target.value)} />
                            <InputError message={form.errors.title} />
                        </div>

                        <div className="grid grid-cols-3 gap-4">
                            <div className="space-y-1">
                                <Label htmlFor="category">Category</Label>
                                <Select value={form.data.category} onValueChange={(v) => form.setData('category', v)}>
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="core">Core</SelectItem>
                                        <SelectItem value="major">Major</SelectItem>
                                        <SelectItem value="ge">General Education</SelectItem>
                                        <SelectItem value="elective">Elective</SelectItem>
                                        <SelectItem value="pe">PE</SelectItem>
                                        <SelectItem value="nstp">NSTP</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={form.errors.category} />
                            </div>
                            <div className="space-y-1">
                                <Label htmlFor="year_level">Year Level</Label>
                                <Select value={form.data.year_level} onValueChange={(v) => form.setData('year_level', v)}>
                                    <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">1st Year</SelectItem>
                                        <SelectItem value="2">2nd Year</SelectItem>
                                        <SelectItem value="3">3rd Year</SelectItem>
                                        <SelectItem value="4">4th Year</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={form.errors.year_level} />
                            </div>
                            <div className="space-y-1">
                                <Label htmlFor="semester">Semester</Label>
                                <Select value={form.data.semester} onValueChange={(v) => form.setData('semester', v)}>
                                    <SelectTrigger><SelectValue placeholder="Select" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="1">1st Semester</SelectItem>
                                        <SelectItem value="2">2nd Semester</SelectItem>
                                        <SelectItem value="0">Summer</SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError message={form.errors.semester} />
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <div className="flex justify-end gap-2">
                    <Link href={CurriculumController.index()}>
                        <Button variant="outline">Cancel</Button>
                    </Link>
                    <Button onClick={submit} disabled={form.processing}>
                        Update Subject
                    </Button>
                </div>
            </div>
        </>
    );
}

EditCurriculumSubject.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Curriculum', href: CurriculumController.index() },
        { title: 'Edit Subject', href: '#' },
    ],
};
