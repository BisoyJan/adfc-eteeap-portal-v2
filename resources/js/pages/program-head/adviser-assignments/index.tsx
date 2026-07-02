import { Head, Link } from '@inertiajs/react';
import AdviserAssignmentController from '@/actions/App/Http/Controllers/ProgramHead/AdviserAssignmentController';
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

type Assignment = {
    id: number;
    student: { id: number; name: string; email: string };
    adviser: { id: number; name: string; email: string };
    term: string | null;
    assigned_at: string;
    is_active: boolean;
};

type PageProps = {
    assignments: Assignment[];
};

export default function AdviserAssignmentsIndex({ assignments }: PageProps) {
    return (
        <>
            <Head title="Adviser Assignments" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Adviser Assignments" description="View and manage adviser-to-student assignments" />

                <div className="flex justify-end">
                    <Link href={AdviserAssignmentController.create()}>
                        <Button>Assign Adviser</Button>
                    </Link>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Active Assignments</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Adviser</TableHead>
                                    <TableHead>Term</TableHead>
                                    <TableHead>Assigned At</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {assignments.map((a) => (
                                    <TableRow key={a.id}>
                                        <TableCell>
                                            <div className="text-sm font-medium">{a.student.name}</div>
                                            <div className="text-xs text-muted-foreground">{a.student.email}</div>
                                        </TableCell>
                                        <TableCell>
                                            <div className="text-sm font-medium">{a.adviser.name}</div>
                                            <div className="text-xs text-muted-foreground">{a.adviser.email}</div>
                                        </TableCell>
                                        <TableCell>{a.term ?? '-'}</TableCell>
                                        <TableCell className="text-sm">{new Date(a.assigned_at).toLocaleDateString()}</TableCell>
                                        <TableCell>
                                            <Badge variant={a.is_active ? 'default' : 'secondary'}>
                                                {a.is_active ? 'Active' : 'Inactive'}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {assignments.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-center text-muted-foreground py-8">
                                            No adviser assignments yet.
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

AdviserAssignmentsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Adviser Assignments', href: AdviserAssignmentController.index() },
    ],
};
