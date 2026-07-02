import { Head } from '@inertiajs/react';
import GradeController from '@/actions/App/Http/Controllers/Student/GradeController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
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
    subject_code: string;
    subject_title: string;
    grade: string | null;
    grade_point: number | null;
    status: string;
    adviser_name: string;
    approved_at: string | null;
};

type PageProps = {
    grades: GradeRecord[];
};

export default function StudentGradesIndex({ grades }: PageProps) {
    return (
        <>
            <Head title="My Grades" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="My Grades" description="View your recorded grades" />

                <Card>
                    <CardHeader>
                        <CardTitle>Grades</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Grade</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Adviser</TableHead>
                                    <TableHead>Approved</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {grades.map((g) => (
                                    <TableRow key={g.id}>
                                        <TableCell>
                                            <span className="font-medium">{g.subject_code}</span>
                                            <span className="ml-2 text-sm text-muted-foreground">{g.subject_title}</span>
                                        </TableCell>
                                        <TableCell>
                                            {g.grade ?? '-'}
                                            {g.grade_point ? ` (${g.grade_point})` : ''}
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant={statusVariant[g.status] ?? 'secondary'}>
                                                {g.status}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="text-sm">{g.adviser_name}</TableCell>
                                        <TableCell className="text-sm">{g.approved_at ?? '-'}</TableCell>
                                    </TableRow>
                                ))}
                                {grades.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="py-8 text-center text-muted-foreground">
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

StudentGradesIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Grades', href: GradeController.index() },
    ],
};
