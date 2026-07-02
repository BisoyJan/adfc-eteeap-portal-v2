import { Head } from '@inertiajs/react';
import { Clock, FileCheck, Users } from 'lucide-react';
import { StatsCard } from '@/components/dashboard/stats-card';
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

type Deadline = { id: number; title: string; student: string; due_date: string; status: string };
type StudentProgress = { student_id: number; name: string; total: number; completed: number; percentage: number };

type PageProps = {
    myAdviseesCount: number;
    pendingSubmissions: number;
    upcomingDeadlines: Deadline[];
    studentProgress: StudentProgress[];
};

export default function AdviserDashboard({
    myAdviseesCount,
    pendingSubmissions,
    upcomingDeadlines,
    studentProgress,
}: PageProps) {
    return (
        <>
            <Head title="Adviser Dashboard" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="Adviser Dashboard" description="Manage your advisees and activities" />

                <div className="grid gap-4 md:grid-cols-3">
                    <StatsCard title="My Advisees" value={myAdviseesCount} icon={Users} />
                    <StatsCard title="Pending Submissions" value={pendingSubmissions} icon={FileCheck} />
                    <StatsCard title="Upcoming Deadlines" value={upcomingDeadlines.length} icon={Clock} />
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Upcoming Deadlines</CardTitle>
                        </CardHeader>
                        <CardContent>
                            {upcomingDeadlines.length === 0 ? (
                                <p className="text-sm text-muted-foreground">No upcoming deadlines.</p>
                            ) : (
                                <div className="space-y-3">
                                    {upcomingDeadlines.map((d) => (
                                        <div key={d.id} className="rounded-lg border p-3">
                                            <p className="text-sm font-medium">{d.title}</p>
                                            <p className="text-xs text-muted-foreground">{d.student}</p>
                                            <div className="mt-1 flex items-center justify-between">
                                                <span className="text-xs text-muted-foreground">Due: {d.due_date}</span>
                                                <Badge variant="outline">{d.status}</Badge>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Student Progress</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Student</TableHead>
                                        <TableHead>Progress</TableHead>
                                        <TableHead>Completed</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {studentProgress.map((s) => (
                                        <TableRow key={s.student_id}>
                                            <TableCell className="font-medium">{s.name}</TableCell>
                                            <TableCell>
                                                <div className="flex items-center gap-2">
                                                    <div className="h-2 flex-1 rounded-full bg-muted">
                                                        <div
                                                            className="h-2 rounded-full bg-primary"
                                                            style={{ width: `${s.percentage}%` }}
                                                        />
                                                    </div>
                                                    <span className="text-xs text-muted-foreground">{s.percentage}%</span>
                                                </div>
                                            </TableCell>
                                            <TableCell>{s.completed}/{s.total}</TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

AdviserDashboard.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
    ],
};
