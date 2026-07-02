import { Head, router } from '@inertiajs/react';
import { useState } from 'react';
import EnrollmentController from '@/actions/App/Http/Controllers/Coordinator/EnrollmentController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';

type EligibleApp = {
    id: number;
    application_number: string;
    applicant_name: string;
    applicant_email: string;
};

type Enrollment = {
    id: number;
    student_id_number: string;
    student_name: string;
    academic_term: string | null;
    enrollment_date: string;
    status: string;
    credited_count: number;
    enrolled_count: number;
    pending_count: number;
    completed_count: number;
};

type PageProps = {
    eligibleApplications: EligibleApp[];
    enrollments: Enrollment[];
};

export default function EnrollmentsIndex({ eligibleApplications, enrollments }: PageProps) {
    const [term, setTerm] = useState('');

    const handleEnroll = (applicationId: number) => {
        router.post(EnrollmentController.store(), {
            application_id: applicationId,
            academic_term: term || null,
        });
    };

    return (
        <>
            <Head title="Enrollments" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Enrollments" description="Convert approved applications into enrollments" />

                {eligibleApplications.length > 0 && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Eligible Applications</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div className="mb-4 flex items-center gap-2">
                                <label className="text-sm font-medium">Term:</label>
                                <Select value={term} onValueChange={setTerm}>
                                    <SelectTrigger className="w-48">
                                        <SelectValue placeholder="Select term (optional)" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value=" ">No term</SelectItem>
                                        <SelectItem value="AY 2026-2027 1st Sem">AY 2026-2027 1st Sem</SelectItem>
                                        <SelectItem value="AY 2026-2027 2nd Sem">AY 2026-2027 2nd Sem</SelectItem>
                                        <SelectItem value="AY 2026-2027 Summer">AY 2026-2027 Summer</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <Table>
                                <TableHeader>
                                    <TableRow>
                                        <TableHead>Applicant</TableHead>
                                        <TableHead>Application #</TableHead>
                                        <TableHead />
                                    </TableRow>
                                </TableHeader>
                                <TableBody>
                                    {eligibleApplications.map((app) => (
                                        <TableRow key={app.id}>
                                            <TableCell className="font-medium">{app.applicant_name}</TableCell>
                                            <TableCell>{app.application_number}</TableCell>
                                            <TableCell className="text-right">
                                                <Button onClick={() => handleEnroll(app.id)}>
                                                    Enroll
                                                </Button>
                                            </TableCell>
                                        </TableRow>
                                    ))}
                                </TableBody>
                            </Table>
                        </CardContent>
                    </Card>
                )}

                <Card>
                    <CardHeader>
                        <CardTitle>Current Enrollments</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Student</TableHead>
                                    <TableHead>ID Number</TableHead>
                                    <TableHead>Term</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead>Credited</TableHead>
                                    <TableHead>Enrolled</TableHead>
                                    <TableHead>Pending</TableHead>
                                    <TableHead>Completed</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {enrollments.map((e) => (
                                    <TableRow key={e.id}>
                                        <TableCell className="font-medium">{e.student_name}</TableCell>
                                        <TableCell>{e.student_id_number}</TableCell>
                                        <TableCell>{e.academic_term ?? '-'}</TableCell>
                                        <TableCell>{e.enrollment_date}</TableCell>
                                        <TableCell><Badge variant="secondary">{e.credited_count}</Badge></TableCell>
                                        <TableCell><Badge>{e.enrolled_count}</Badge></TableCell>
                                        <TableCell><Badge variant="outline">{e.pending_count}</Badge></TableCell>
                                        <TableCell><Badge variant="secondary">{e.completed_count}</Badge></TableCell>
                                    </TableRow>
                                ))}
                                {enrollments.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={8} className="py-8 text-center text-muted-foreground">
                                            No enrollments yet.
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

EnrollmentsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Enrollments', href: EnrollmentController.index() },
    ],
};
