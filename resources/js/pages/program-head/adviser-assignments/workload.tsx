import { Head } from '@inertiajs/react';
import AdviserAssignmentController from '@/actions/App/Http/Controllers/ProgramHead/AdviserAssignmentController';
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

type AdviserLoad = {
    id: number;
    name: string;
    email: string;
    adviser_assignments_count: number;
};

type PageProps = {
    advisers: AdviserLoad[];
    maxStudents: number;
};

export default function AdviserWorkload({ advisers, maxStudents }: PageProps) {
    return (
        <>
            <Head title="Adviser Workload" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Adviser Workload" description={`Maximum ${maxStudents} students per adviser`} />

                <Card>
                    <CardHeader>
                        <CardTitle>Workload Overview</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Adviser</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Active Students</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {advisers.map((adviser) => (
                                    <TableRow key={adviser.id}>
                                        <TableCell className="font-medium">{adviser.name}</TableCell>
                                        <TableCell className="text-sm text-muted-foreground">{adviser.email}</TableCell>
                                        <TableCell>{adviser.adviser_assignments_count} / {maxStudents}</TableCell>
                                        <TableCell>
                                            <Badge variant={adviser.adviser_assignments_count >= maxStudents ? 'destructive' : 'default'}>
                                                {adviser.adviser_assignments_count >= maxStudents ? 'Full' : 'Available'}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AdviserWorkload.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Adviser Workload', href: AdviserAssignmentController.workload() },
    ],
};
