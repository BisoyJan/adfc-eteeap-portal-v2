import { Head } from '@inertiajs/react';
import { Clock, FileText, GraduationCap, BookOpen } from 'lucide-react';
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

type Deadline = { id: number; title: string; due_date: string; status: string };
type Grade = { id: number; subject: string; grade: string; status: string };
type ClearanceInfo = { id: number; status: string; registry_number: string | null };

type PageProps = {
    progressPercentage: number;
    totalSubjects: number;
    completedSubjects: number;
    upcomingDeadlines: Deadline[];
    recentGrades: Grade[];
    clearance: ClearanceInfo | null;
};

export default function StudentDashboard({
    progressPercentage,
    totalSubjects,
    completedSubjects,
    upcomingDeadlines,
    recentGrades,
    clearance,
}: PageProps) {
    return (
        <>
            <Head title="Student Dashboard" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="Student Dashboard" description="Track your academic progress" />

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatsCard
                        title="Overall Progress"
                        value={`${progressPercentage}%`}
                        icon={GraduationCap}
                        description={`${completedSubjects} of ${totalSubjects} subjects`}
                    />
                    <StatsCard title="Total Subjects" value={totalSubjects} icon={BookOpen} />
                    <StatsCard title="Completed" value={completedSubjects} icon={FileText} />
                    <StatsCard
                        title="Clearance"
                        value={clearance?.status ?? 'N/A'}
                        icon={Clock}
                    />
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
                            <CardTitle>Recent Grades</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Subject</TableHead>
                                        <TableHead>Grade</TableHead>
                                        <TableHead>Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {recentGrades.map((g) => (
                                        <TableRow key={g.id}>
                                            <TableCell className="font-medium">{g.subject}</TableCell>
                                            <TableCell>{g.grade}</TableCell>
                                            <TableCell>
                                                <Badge variant="outline">{g.status}</Badge>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                    {recentGrades.length === 0 && (
                                        <TableRow>
                                            <TableCell colSpan={3} className="py-8 text-center text-muted-foreground">
                                                No grades posted yet.
                                            </TableCell>
                                        </TableRow>
                                    )}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                </div>

                {clearance && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Graduation Clearance</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="flex items-center justify-between">
                                <div>
                                    <p className="text-sm font-medium">Status: {clearance.status}</p>
                                    {clearance.registry_number && (
                                        <p className="text-xs text-muted-foreground">
                                            RGN: {clearance.registry_number}
                                        </p>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}

StudentDashboard.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
    ],
};
