import { Head, router } from '@inertiajs/react';
import GradeController from '@/actions/App/Http/Controllers/ProgramHead/GradeController';
import Heading from '@/components/heading';
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

type PendingGrade = {
    id: number;
    student_name: string;
    subject_code: string;
    subject_title: string;
    grade: string | null;
    grade_point: number | null;
    adviser_name: string;
    rubric_score: number | null;
    created_at: string;
};

type PageProps = {
    grades: PendingGrade[];
};

export default function GradeApprovalQueue({ grades }: PageProps) {
    const handleApprove = (gradeId: number) => {
        router.put(GradeController.approve.url(gradeId));
    };

    return (
        <>
            <Head title="Grade Approvals" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Grade Approvals" description="Review and approve submitted grades" />

                <Card>
                    <CardHeader>
                        <CardTitle>Pending Approvals</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Grade</TableHead>
                                    <TableHead>Adviser</TableHead>
                                    <TableHead>Rubric Score</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {grades.map((g) => (
                                    <TableRow key={g.id}>
                                        <TableCell className="font-medium">{g.student_name}</TableCell>
                                        <TableCell>{g.subject_code} - {g.subject_title}</TableCell>
                                        <TableCell>{g.grade ?? '-'}</TableCell>
                                        <TableCell>{g.adviser_name}</TableCell>
                                        <TableCell>{g.rubric_score ?? '-'}</TableCell>
                                        <TableCell>{g.created_at}</TableCell>
                                        <TableCell>
                                            <Button onClick={() => handleApprove(g.id)} size="sm">
                                                Approve
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {grades.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={7} className="py-8 text-center text-muted-foreground">
                                            No pending grade approvals.
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

GradeApprovalQueue.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Grade Approvals', href: GradeController.pending() },
    ],
};
