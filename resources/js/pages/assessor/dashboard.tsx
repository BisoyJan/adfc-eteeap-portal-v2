import { Head } from '@inertiajs/react';
import { Calendar, Users } from 'lucide-react';
import { StatsCard } from '@/components/dashboard/stats-card';
import Heading from '@/components/heading';
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

type Interview = { id: number; scheduled_at: string; location: string; applicant: string };

type PageProps = {
    assignedInterviewsCount: number;
    upcomingInterviews: Interview[];
};

export default function AssessorDashboard({
    assignedInterviewsCount,
    upcomingInterviews,
}: PageProps) {
    return (
        <>
            <Head title="Assessor Dashboard" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="Assessor Dashboard" description="Manage your interview panels" />

                <div className="grid gap-4 md:grid-cols-2">
                    <StatsCard title="Assigned Interviews" value={assignedInterviewsCount} icon={Users} />
                    <StatsCard title="Upcoming" value={upcomingInterviews.length} icon={Calendar} />
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Upcoming Interviews</CardTitle>
                    </CardHeader>
                    <CardContent>
                        {upcomingInterviews.length === 0 ? (
                            <p className="text-sm text-muted-foreground">No upcoming interviews assigned to you.</p>
                        ) : (
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Applicant</TableHead>
                                        <TableHead>Schedule</TableHead>
                                        <TableHead>Location</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {upcomingInterviews.map((i) => (
                                        <TableRow key={i.id}>
                                            <TableCell className="font-medium">{i.applicant}</TableCell>
                                            <TableCell>{i.scheduled_at}</TableCell>
                                            <TableCell>{i.location}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AssessorDashboard.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
    ],
};
