import { Head, router } from '@inertiajs/react';
import GraduationController from '@/actions/App/Http/Controllers/Coordinator/GraduationController';
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

type EligibleStudent = {
    id: number;
    name: string;
    email: string;
};

type Clearance = {
    id: number;
    student_name: string;
    status: string;
    status_label: string;
    verified_count: number;
    total_count: number;
    created_at: string;
};

type PageProps = {
    eligibleStudents: EligibleStudent[];
    clearances: Clearance[];
};

export default function GraduationPipeline({ eligibleStudents, clearances }: PageProps) {
    const handleInitiate = (studentId: number) => {
        router.post(GraduationController.store(), {
            student_id: studentId,
        });
    };

    return (
        <>
            <Head title="Graduation Pipeline" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Graduation Pipeline" description="Manage student graduation clearances" />

                {eligibleStudents.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Eligible Students</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Student</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead />
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {eligibleStudents.map((s) => (
                                        <TableRow key={s.id}>
                                            <TableCell className="font-medium">{s.name}</TableCell>
                                            <TableCell>{s.email}</TableCell>
                                            <TableCell className="text-right">
                                                <Button onClick={() => handleInitiate(s.id)}>
                                                    Initiate Clearance
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                )}

                <Card>
                    <CardHeader>
                        <CardTitle>Active Clearances</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Documents</TableHead>
                                    <TableHead>Date Created</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {clearances.map((c) => (
                                    <TableRow key={c.id}>
                                        <TableCell className="font-medium">{c.student_name}</TableCell>
                                        <TableCell><Badge>{c.status_label}</Badge></TableCell>
                                        <TableCell>{c.verified_count} / {c.total_count} verified</TableCell>
                                        <TableCell>{c.created_at}</TableCell>
                                        <TableCell className="text-right">
                                            <Button variant="outline" onClick={() => router.get(GraduationController.show.url(c.id))}>
                                                View
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {clearances.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="py-8 text-center text-muted-foreground">
                                            No clearances yet.
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

GraduationPipeline.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Graduation Pipeline', href: GraduationController.pipeline() },
    ],
};
