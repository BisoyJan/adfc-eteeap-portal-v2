import { Head } from '@inertiajs/react';
import { FileCheck, GraduationCap, Users, Clock } from 'lucide-react';
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

type Overview = { totalApplicants: number; activeStudents: number; totalGraduates: number };
type AuditSummary = { total: number; pending: number; in_review: number; approved: number; completed: number };

type PageProps = {
    programOverview: Overview;
    pendingApprovals: number;
    auditSummary: AuditSummary;
    avgProcessingDays: number | null;
};

export default function VpaaDashboard({
    programOverview,
    pendingApprovals,
    auditSummary,
    avgProcessingDays,
}: PageProps) {
    return (
        <>
            <Head title="VPAA Dashboard" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="VPAA Dashboard" description="Program oversight and graduation approvals" />

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatsCard title="Total Applicants" value={programOverview.totalApplicants} icon={Users} />
                    <StatsCard title="Active Students" value={programOverview.activeStudents} icon={GraduationCap} />
                    <StatsCard title="Total Graduates" value={programOverview.totalGraduates} icon={GraduationCap} />
                    <StatsCard title="Pending Approvals" value={pendingApprovals} icon={FileCheck} />
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Graduation Audit Summary</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Status</TableHead>
                                        <TableHead>Count</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    <TableRow>
                                        <TableCell>Total</TableCell>
                                        <TableCell className="font-medium">{auditSummary.total}</TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell>Pending</TableCell>
                                        <TableCell>{auditSummary.pending}</TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell>In Review</TableCell>
                                        <TableCell>{auditSummary.in_review}</TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell>Approved</TableCell>
                                        <TableCell>{auditSummary.approved}</TableCell>
                                    </TableRow>
                                    <TableRow>
                                        <TableCell>Completed</TableCell>
                                        <TableCell className="font-medium">{auditSummary.completed}</TableCell>
                                    </TableRow>
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Processing Timeline</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="flex items-center gap-4">
                                <Clock className="size-8 text-muted-foreground" />
                                <div>
                                    <p className="text-3xl font-bold">
                                        {avgProcessingDays !== null ? `${avgProcessingDays} days` : 'N/A'}
                                    </p>
                                    <p className="text-sm text-muted-foreground">
                                        Average from submission to approval
                                    </p>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

VpaaDashboard.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
    ],
};
