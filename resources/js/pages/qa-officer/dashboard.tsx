import { Head } from '@inertiajs/react';
import { FileCheck, ClipboardList } from 'lucide-react';
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

type Overview = { total: number; assigned: number; in_progress: number; submitted: number; approved: number };
type Submission = { id: number; title: string; student: string; adviser: string; updated_at: string };

type PageProps = {
    pendingF3Validations: number;
    enhancementOverview: Overview;
    recentSubmissions: Submission[];
};

export default function QaOfficerDashboard({
    pendingF3Validations,
    enhancementOverview,
    recentSubmissions,
}: PageProps) {
    return (
        <>
            <Head title="QA Officer Dashboard" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="QA Officer Dashboard" description="Monitor assessments and enhancement activities" />

                <div className="grid gap-4 md:grid-cols-3">
                    <StatsCard title="Pending F.3 Validations" value={pendingF3Validations} icon={FileCheck} />
                    <StatsCard title="Total Activities" value={enhancementOverview.total} icon={ClipboardList} />
                    <StatsCard title="Submitted for Review" value={enhancementOverview.submitted} icon={ClipboardList} />
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Enhancement Activity Overview</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <PipelineChart
                                data={[
                                    { name: 'Assigned', value: enhancementOverview.assigned },
                                    { name: 'In Progress', value: enhancementOverview.in_progress },
                                    { name: 'Submitted', value: enhancementOverview.submitted },
                                    { name: 'Approved', value: enhancementOverview.approved },
                                ]}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Recent Submissions</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Activity</TableHead>
                                        <TableHead>Student</TableHead>
                                        <TableHead>Adviser</TableHead>
                                        <TableHead>Date</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {recentSubmissions.map((s) => (
                                        <TableRow key={s.id}>
                                            <TableCell className="font-medium">{s.title}</TableCell>
                                            <TableCell>{s.student}</TableCell>
                                            <TableCell>{s.adviser}</TableCell>
                                            <TableCell className="text-muted-foreground">{s.updated_at}</TableCell>
                                        </TableRow>
                                    ))}
                                    {recentSubmissions.length === 0 && (
                                        <TableRow>
                                            <TableCell colSpan={4} className="py-8 text-center text-muted-foreground">
                                                No recent submissions.
                                            </TableCell>
                                        </TableRow>
                                    )}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </>
    );
}

QaOfficerDashboard.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
    ],
};
