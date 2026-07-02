import { Head, Link } from '@inertiajs/react';
import GradeController from '@/actions/App/Http/Controllers/Adviser/GradeController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    draft: 'secondary',
    submitted: 'outline',
    approved: 'default',
};

type GradeRecord = {
    id: number;
    student_name: string;
    subject_code: string;
    subject_title: string;
    grade: string | null;
    grade_point: number | null;
    rubric_title: string | null;
    rubric_score: number | null;
    status: string;
};

type PageProps = {
    grades: GradeRecord[];
};

export default function AdviserGradesIndex({ grades }: PageProps) {
    return (
        <>
            <Head title="Grades" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Grades" description="Manage grades for your advisees" />

                <div className="flex justify-end">
                    <Link href={GradeController.create()}>
                        <Button>Enter Grade</Button>
                    </Link>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>All Grades</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Grade</TableHead>
                                    <TableHead>Rubric</TableHead>
                                    <TableHead>Score</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {grades.map((g) => (
                                    <TableRow key={g.id}>
                                        <TableCell className="font-medium">{g.student_name}</TableCell>
                                        <TableCell>{g.subject_code} - {g.subject_title}</TableCell>
                                        <TableCell>{g.grade ?? '-'}</TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {g.rubric_title ?? '-'}
                                        </TableCell>
                                        <TableCell>{g.rubric_score ?? '-'}</TableCell>
                                        <TableCell>
                                            <Badge variant={statusVariant[g.status] ?? 'secondary'}>
                                                {g.status}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {grades.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={6} className="py-8 text-center text-muted-foreground">
                                            No grades recorded yet.
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

AdviserGradesIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Grades', href: GradeController.index() },
    ],
};
