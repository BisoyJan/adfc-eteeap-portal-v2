import { Head, router } from '@inertiajs/react';
import GraduationController from '@/actions/App/Http/Controllers/Vpaa/GraduationController';
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

type Clearance = {
    id: number;
    student_name: string;
    student_email: string;
    status: string;
    status_label: string;
    created_at: string;
    confirmation_letter: boolean;
};

type PageProps = {
    clearances: Clearance[];
};

export default function ApprovalsIndex({ clearances }: PageProps) {
    const handleApprove = (clearanceId: number) => {
        router.put(GraduationController.approve.url(clearanceId));
    };

    return (
        <>
            <Head title="Pending Approvals" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Pending Approvals" description="Review and approve graduation clearances" />

                <Card>
                    <CardHeader>
                        <CardTitle>Clearances Awaiting Approval</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Submitted</TableHead>
                                    <TableHead>Confirmation Letter</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {clearances.map((c) => (
                                    <TableRow key={c.id}>
                                        <TableCell className="font-medium">{c.student_name}</TableCell>
                                        <TableCell>{c.student_email}</TableCell>
                                        <TableCell><Badge>{c.status_label}</Badge></TableCell>
                                        <TableCell>{c.created_at}</TableCell>
                                        <TableCell>
                                            <Badge variant={c.confirmation_letter ? 'default' : 'outline'}>
                                                {c.confirmation_letter ? 'Ready' : 'Missing'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="text-right">
                                            <Button onClick={() => handleApprove(c.id)}>
                                                Approve
                                            </Button>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {clearances.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={6} className="py-8 text-center text-muted-foreground">
                                            No clearances awaiting approval.
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

ApprovalsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Approvals', href: GraduationController.pendingApprovals() },
    ],
};
