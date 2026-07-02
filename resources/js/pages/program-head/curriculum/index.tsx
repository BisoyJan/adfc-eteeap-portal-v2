import { Head, Link, useForm } from '@inertiajs/react';
import CurriculumController from '@/actions/App/Http/Controllers/ProgramHead/CurriculumController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';

type CurriculumSubject = {
    id: number;
    code: string;
    title: string;
    units: number;
    category: string;
    year_level: number | null;
    semester: number | null;
    is_active: boolean;
    curriculum_version: string | null;
    prerequisite_codes: string[] | null;
};

type PageProps = {
    subjects: CurriculumSubject[];
};

export default function CurriculumIndex({ subjects }: PageProps) {
    const versionForm = useForm<{ version: string }>({ version: '' });

    function setVersion() {
        versionForm.post(CurriculumController.newVersion().url, { preserveScroll: true });
    }

    return (
        <>
            <Head title="Curriculum Subjects" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Curriculum Subjects" description="Manage the BSIT curriculum subject repository" />

                <div className="flex items-center justify-between">
                    <Link href={CurriculumController.create()}>
                        <Button>Add Subject</Button>
                    </Link>

                    <div className="flex items-center gap-2">
                        <Input
                            placeholder="Version label (e.g. AY-2026-2027)"
                            value={versionForm.data.version}
                            onChange={(e) => versionForm.setData('version', e.target.value)}
                            className="w-64"
                        />
                        <Button variant="outline" onClick={setVersion} disabled={versionForm.processing}>
                            Set Version
                        </Button>
                        <InputError message={versionForm.errors.version} />
                    </div>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>All Subjects</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Code</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Units</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>Year</TableHead>
                                    <TableHead>Sem</TableHead>
                                    <TableHead>Version</TableHead>
                                    <TableHead>Active</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {subjects.map((subject) => (
                                    <TableRow key={subject.id}>
                                        <TableCell className="font-mono text-sm">{subject.code}</TableCell>
                                        <TableCell>{subject.title}</TableCell>
                                        <TableCell>{subject.units}</TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">{subject.category}</Badge>
                                        </TableCell>
                                        <TableCell>{subject.year_level ?? '-'}</TableCell>
                                        <TableCell>{subject.semester ?? '-'}</TableCell>
                                        <TableCell className="text-xs text-muted-foreground">
                                            {subject.curriculum_version ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant={subject.is_active ? 'default' : 'secondary'}>
                                                {subject.is_active ? 'Active' : 'Inactive'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Link href={CurriculumController.edit(subject.id)}>
                                                <Button variant="ghost" size="sm">Edit</Button>
                                            </Link>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {subjects.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={9} className="text-center text-muted-foreground py-8">
                                            No subjects yet. Click "Add Subject" to create one.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

CurriculumIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Curriculum', href: CurriculumController.index() },
    ],
};
