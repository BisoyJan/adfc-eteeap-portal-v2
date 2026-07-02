import { Head } from '@inertiajs/react';
import { Users, FileCheck, Calendar, GraduationCap } from 'lucide-react';
import { PipelineChart } from '@/components/dashboard/pipeline-chart';
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

type DataPoint = { name: string; value: number };
type Interview = { id: number; applicant: string; scheduled_at: string; location: string };
type NotificationRow = { id: number; title: string; body: string | null; created_at: string };

type PageProps = {
    pipeline: DataPoint[];
    pendingReviews: number;
    upcomingInterviews: Interview[];
    totalApplicants: number;
    totalStudents: number;
    totalEnrolled: number;
    recentNotifications: NotificationRow[];
};

export default function CoordinatorDashboard({
    pipeline,
    pendingReviews,
    upcomingInterviews,
    totalApplicants,
    totalStudents,
    totalEnrolled,
    recentNotifications,
}: PageProps) {
    return (
        <>
            <Head title="Coordinator Dashboard" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="Coordinator Dashboard" description="Overview of the ETEEAP program" />

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatsCard title="Total Applicants" value={totalApplicants} icon={Users} />
                    <StatsCard title="Enrolled Students" value={totalStudents} icon={GraduationCap} />
                    <StatsCard title="Pending Reviews" value={pendingReviews} icon={FileCheck} />
                    <StatsCard title="Approved Applications" value={totalEnrolled} icon={Calendar} />
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Application Pipeline</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <PipelineChart data={pipeline} />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Upcoming Interviews</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {upcomingInterviews.length === 0 ? (
                                <p className="text-sm text-muted-foreground">No upcoming interviews.</p>
                            ) : (
                                <div className="space-y-3">
                                    {upcomingInterviews.map((i) => (
                                        <div key={i.id} className="rounded-lg border p-3">
                                            <p className="text-sm font-medium">{i.applicant}</p>
                                            <p className="text-xs text-muted-foreground">{i.scheduled_at}</p>
                                            <p className="text-xs text-muted-foreground">{i.location}</p>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Recent Activity</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Event</TableHead>
                                    <TableHead>Details</TableHead>
                                    <TableHead>Date</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {recentNotifications.map((n) => (
                                    <TableRow key={n.id}>
                                        <TableCell className="font-medium">{n.title}</TableCell>
                                        <TableCell className="text-muted-foreground">{n.body ?? '—'}</TableCell>
                                        <TableCell className="text-muted-foreground">{n.created_at}</TableCell>
                                    </TableRow>
                                ))}
                                {recentNotifications.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={3} className="py-8 text-center text-muted-foreground">
                                            No recent activity.
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

CoordinatorDashboard.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
    ],
};
