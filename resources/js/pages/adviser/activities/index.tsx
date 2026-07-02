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

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    assigned: 'secondary',
    in_progress: 'default',
    submitted: 'outline',
    approved: 'default',
    rejected: 'destructive',
};

type Activity = {
    id: number;
    student: { id: number; name: string; email: string };
    curriculum_subject: { id: number; code: string; title: string } | null;
    activity_type: string;
    title: string;
    status: string;
    score: number | null;
    due_date: string | null;
    created_at: string;
};

type PageProps = {
    activities: Activity[];
};

export default function AdviserActivitiesIndex({ activities }: PageProps) {
    return (
        <>
            <Head title="Enhancement Activities" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Enhancement Activities" description="Manage activities assigned to your advisees" />

                <div className="flex justify-end">
                    <Link href={ActivityController.create()}>
                        <Button>Assign Activity</Button>
                    </Link>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>All Activities</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Activity</TableHead>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Score</TableHead>
                                    <TableHead>Due</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {activities.map((a) => (
                                    <TableRow key={a.id}>
                                        <TableCell className="font-medium">{a.title}</TableCell>
                                        <TableCell className="text-sm">{a.student.name}</TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {a.curriculum_subject?.code ?? '-'}
                                        </TableCell>
                                        <TableCell className="text-sm">{a.activity_type.replace(/_/g, ' ')}</TableCell>
                                        <TableCell>
                                            <Badge variant={statusVariant[a.status] ?? 'secondary'}>
                                                {a.status.replace(/_/g, ' ')}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>{a.score ?? '-'}</TableCell>
                                        <TableCell className="text-sm">
                                            {a.due_date ? new Date(a.due_date).toLocaleDateString() : '-'}
                                        </TableCell>
                                        <TableCell>
                                            <Link href={ActivityController.show(a.id)}>
                                                <Button variant="ghost" size="sm">View</Button>
                                            </Link>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {activities.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={8} className="text-center text-muted-foreground py-8">
                                            No activities assigned yet.
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

AdviserActivitiesIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Activities', href: ActivityController.index() },
    ],
};
