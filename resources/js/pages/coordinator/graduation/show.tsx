import { Head, router } from '@inertiajs/react';
import GraduationController from '@/actions/App/Http/Controllers/Coordinator/GraduationController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
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

type Document = {
    id: number;
    document_type: string;
    is_verified: boolean;
    verified_by: string | null;
    verified_at: string | null;
    file_path: string | null;
};

type Clearance = {
    id: number;
    student_name: string;
    student_email: string;
    status: string;
    status_label: string;
    registry_number: string | null;
    verification_progress: string;
};

type PageProps = {
    clearance: Clearance;
    documents: Document[];
};

const documentLabels: Record<string, string> = {
    endorsement_letter: 'Endorsement Letter',
    rubric_final_rating: 'Rubric for Final Rating',
    summary_requirements: 'Summary of Requirements',
    enrollment_form: 'Enrollment Form',
    equivalency_matrix_compliance: 'Equivalency Matrix & Compliance',
    panel_evaluation: 'Panel Evaluation',
    application_letter: 'Application Letter',
    ched_form: 'CHED Form',
    cv: 'Curriculum Vitae',
    psa_documents: 'PSA Documents',
    coe: 'Certificate of Employment',
    narrative_reports: 'Narrative Reports',
};

export default function GraduationShow({ clearance, documents }: PageProps) {
    const handleVerify = (documentId: number) => {
        router.put(GraduationController.verifyDocument.url(clearance.id, documentId));
    };

    const handleConfirm = () => {
        router.post(GraduationController.confirm.url(clearance.id));
    };

    const allVerified = documents.every((d) => d.is_verified);

    return (
        <>
            <Head title="Graduation Clearance" />

            <div className="flex flex-col gap-6 p-4">
                <Heading
                    title={`Clearance — ${clearance.student_name}`}
                    description={clearance.verification_progress}
                />

                <Card>
                    <CardHeader>
                        <CardTitle>Clearance Details</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <dl className="grid grid-cols-2 gap-4">
                            <div>
                                <dt className="text-sm text-muted-foreground">Student</dt>
                                <dd className="font-medium">{clearance.student_name}</dd>
                            </div>
                            <div>
                                <dt className="text-sm text-muted-foreground">Email</dt>
                                <dd>{clearance.student_email}</dd>
                            </div>
                            <div>
                                <dt className="text-sm text-muted-foreground">Status</dt>
                                <dd><Badge>{clearance.status_label}</Badge></dd>
                            </div>
                            <div>
                                <dt className="text-sm text-muted-foreground">Registry Number</dt>
                                <dd>{clearance.registry_number ?? '-'}</dd>
                            </div>
                        </dl>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Document Checklist</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Document</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Verified By</TableHead>
                                    <TableHead>Verified At</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {documents.map((doc) => (
                                    <TableRow key={doc.id}>
                                        <TableCell>{documentLabels[doc.document_type] ?? doc.document_type}</TableCell>
                                        <TableCell>
                                            <Badge variant={doc.is_verified ? 'default' : 'outline'}>
                                                {doc.is_verified ? 'Verified' : 'Pending'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell>{doc.verified_by ?? '-'}</TableCell>
                                        <TableCell>{doc.verified_at ?? '-'}</TableCell>
                                        <TableCell className="text-right">
                                            {!doc.is_verified && (
                                                <Button size="sm" onClick={() => handleVerify(doc.id)}>
                                                    Verify
                                                </Button>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>

                {clearance.status === 'pending' && allVerified && (
                    <div className="flex justify-end">
                        <Button onClick={handleConfirm}>
                            Confirm & Send to VPAA
                        </Button>
                    </div>
                )}
            </div>
        </>
    );
}

GraduationShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Graduation Pipeline', href: GraduationController.pipeline() },
        { title: 'Clearance', href: '' },
    ],
};
