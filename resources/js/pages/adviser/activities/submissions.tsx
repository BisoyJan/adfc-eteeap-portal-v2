import { Head, Link } from '@inertiajs/react';
import ActivityController from '@/actions/App/Http/Controllers/Adviser/ActivityController';
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

type Activity = {
    id: number;
    title: string;
    student: { id: number; name: string; email: string };
    curriculum_subject: { id: number; code: string; title: string } | null;
    status: string;
    submitted_at: string;
};

type PageProps = {
    activities: Activity[];
};

export default function PendingSubmissions({ activities }: PageProps) {
    return (
        <>
            <Head title="Pending Submissions" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Pending Submissions" description="Submitted activities awaiting your review and scoring" />

                <Card>
                    <CardHeader>
                        <CardTitle>Awaiting Review</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Activity</TableHead>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {activities.map((a) => (
                                    <TableRow key={a.id}>
                                        <TableCell className="font-medium">{a.title}</TableCell>
                                        <TableCell>{a.student.name}</TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {a.curriculum_subject?.code ?? '-'}
                                        </TableCell>
                                        <TableCell>
                                            <Badge variant="outline">Submitted</Badge>
                                        </TableCell>
                                        <TableCell>
                                            <Link href={ActivityController.show(a.id)}>
                                                <Button variant="ghost" size="sm">Review</Button>
                                            </Link>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {activities.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-center text-muted-foreground py-8">
                                            No pending submissions.
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

PendingSubmissions.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Pending Submissions', href: ActivityController.pendingSubmissions() },
    ],
};
