import { Head } from '@inertiajs/react';
import EnhancementPlanController from '@/actions/App/Http/Controllers/QaOfficer/EnhancementPlanController';
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

type Student = {
    id: number;
    name: string;
    email: string;
    pending_activities: number;
};

type PageProps = {
    students: Student[];
};

export default function EnhancementPlanOverview({ students }: PageProps) {
    return (
        <>
            <Head title="Enhancement Plans" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Enhancement Plan Overview" description="Monitor active enhancement plans across all students" />

                <Card>
                    <CardHeader>
                        <CardTitle>Students with Enhancement Plans</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Pending Activities</TableHead>
                                    <TableHead>Status</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {students.map((s) => (
                                    <TableRow key={s.id}>
                                        <TableCell className="font-medium">{s.name}</TableCell>
                                        <TableCell className="text-sm text-muted-foreground">{s.email}</TableCell>
                                        <TableCell>{s.pending_activities}</TableCell>
                                        <TableCell>
                                            <Badge variant={s.pending_activities > 0 ? 'default' : 'secondary'}>
                                                {s.pending_activities > 0 ? 'In Progress' : 'Complete'}
                                            </Badge>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {students.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={4} className="text-center text-muted-foreground py-8">
                                            No enhancement plans yet.
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

EnhancementPlanOverview.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Enhancement Plans', href: EnhancementPlanController.overview() },
    ],
};
