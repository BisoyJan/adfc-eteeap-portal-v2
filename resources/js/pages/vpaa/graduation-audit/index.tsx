import { Head } from '@inertiajs/react';
import GraduationController from '@/actions/App/Http/Controllers/Vpaa/GraduationController';
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

type Clearance = {
    id: number;
    student_name: string;
    student_email: string;
    status: string;
    status_label: string;
    registry_number: string | null;
    vpaa_approved_at: string | null;
    diploma_printed_at: string | null;
    tor_released_at: string | null;
    created_at: string;
};

type PageProps = {
    clearances: Clearance[];
};

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    pending: 'outline',
    in_review: 'default',
    approved: 'secondary',
    completed: 'default',
};

export default function GraduationAudit({ clearances }: PageProps) {
    return (
        <>
            <Head title="Graduation Audit" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Graduation Audit" description="View all graduation clearances" />

                <Card>
                    <CardHeader>
                        <CardTitle>All Clearances</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>Registry Number</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>VPAA Approved</TableHead>
                                    <TableHead>Diploma Printed</TableHead>
                                    <TableHead>TOR Released</TableHead>
                                    <TableHead>Created</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {clearances.map((c) => (
                                    <TableRow key={c.id}>
                                        <TableCell className="font-medium">{c.student_name}</TableCell>
                                        <TableCell className="font-mono text-sm">{c.registry_number ?? '-'}</TableCell>
                                        <TableCell>
                                            <Badge variant={statusVariant[c.status] ?? 'outline'}>
                                                {c.status_label}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>{c.vpaa_approved_at ?? '-'}</TableCell>
                                        <TableCell>{c.diploma_printed_at ?? '-'}</TableCell>
                                        <TableCell>{c.tor_released_at ?? '-'}</TableCell>
                                        <TableCell>{c.created_at}</TableCell>
                                    </TableRow>
                                ))}
                                {clearances.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={7} className="py-8 text-center text-muted-foreground">
                                            No clearances found.
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

GraduationAudit.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Graduation Audit', href: GraduationController.audit() },
    ],
};
