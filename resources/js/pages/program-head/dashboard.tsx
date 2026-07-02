import { Head } from '@inertiajs/react';
import { FileCheck, GraduationCap, Users } from 'lucide-react';
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

type AdviserRow = { id: number; name: string; email: string; adviser_assignments_count: number };
type CurriculumStats = { totalSubjects: number; creditedSubjects: number; enrolledSubjects: number; completedSubjects: number };

type PageProps = {
    pendingEquivalencies: number;
    advisers: AdviserRow[];
    maxLoad: number;
    overloadedAdvisers: number;
    pendingGradeApprovals: number;
    curriculumStats: CurriculumStats;
};

export default function ProgramHeadDashboard({
    pendingEquivalencies,
    advisers,
    maxLoad,
    overloadedAdvisers,
    pendingGradeApprovals,
    curriculumStats,
}: PageProps) {
    return (
        <>
            <Head title="Program Head Dashboard" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="Program Head Dashboard" description="Curriculum and faculty oversight" />

                <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <StatsCard title="Pending Equivalencies" value={pendingEquivalencies} icon={FileCheck} />
                    <StatsCard title="Pending Grade Approvals" value={pendingGradeApprovals} icon={GraduationCap} />
                    <StatsCard title="Advisers" value={advisers.length} icon={Users} />
                    <StatsCard title="Overloaded Advisers" value={overloadedAdvisers} icon={Users} />
                </div>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Curriculum Statistics</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <PipelineChart
                                data={[
                                    { name: 'Total', value: curriculumStats.totalSubjects },
                                    { name: 'Credited', value: curriculumStats.creditedSubjects },
                                    { name: 'Enrolled', value: curriculumStats.enrolledSubjects },
                                    { name: 'Completed', value: curriculumStats.completedSubjects },
                                ]}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Adviser Workload</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Name</TableHead>
                                        <TableHead>Email</TableHead>
                                        <TableHead>Students</TableHead>
                                        <TableHead>Status</TableHead>
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {advisers.map((a) => (
                                        <TableRow key={a.id}>
                                            <TableCell className="font-medium">{a.name}</TableCell>
                                            <TableCell>{a.email}</TableCell>
                                            <TableCell>{a.adviser_assignments_count}/{maxLoad}</TableCell>
                                            <TableCell>
                                                <span className={`text-sm ${a.adviser_assignments_count > maxLoad ? 'text-red-600 font-medium' : 'text-green-600'}`}>
                                                    {a.adviser_assignments_count > maxLoad ? 'Overloaded' : 'OK'}
                                                </span>
                                            </TableCell>
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

ProgramHeadDashboard.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
    ],
};
