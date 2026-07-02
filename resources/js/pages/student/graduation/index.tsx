import { Head, router, useForm } from '@inertiajs/react';
import type { ChangeEvent } from 'react';
import GraduationController from '@/actions/App/Http/Controllers/Student/GraduationController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
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

type Clearance = {
    id: number;
    status: string;
    status_label: string;
    registry_number: string | null;
    vpaa_approved_at: string | null;
    tor_released_at: string | null;
    has_clearance_form: boolean;
    verification_progress: string;
};

type Document = {
    id: number;
    document_type: string;
    is_verified: boolean;
};

type PageProps = {
    clearance: Clearance | null;
    documents: Document[];
    canDownloadDiploma: boolean;
    canDownloadTor: boolean;
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

export default function StudentGraduation({ clearance, documents, canDownloadDiploma, canDownloadTor }: PageProps) {
    const form = useForm<{ file: File | null }>({
        file: null,
    });

    const handleFileChange = (event: ChangeEvent<HTMLInputElement>) => {
        form.setData('file', event.target.files?.[0] ?? null);
    };

    const handleSubmit = () => {
        form.post(GraduationController.submitClearance.url(), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => form.reset('file'),
        });
    };

    return (
        <>
            <Head title="Graduation Clearance" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Graduation Clearance" description="Track your graduation clearance progress" />

                {!clearance ? (
                    <Card>
                        <CardContent className="py-8 text-center text-muted-foreground">
                            You do not have an active graduation clearance yet. Please check with your coordinator.
                        </CardContent>
                    </Card>
                ) : (
                    <>
                        <Card>
                            <CardHeader>
                                <CardTitle>Clearance Status</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <dl className="grid grid-cols-2 gap-4">
                                    <div>
                                        <dt className="text-sm text-muted-foreground">Status</dt>
                                        <dd><Badge>{clearance.status_label}</Badge></dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm text-muted-foreground">Registry Number</dt>
                                        <dd>{clearance.registry_number ?? 'Pending'}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm text-muted-foreground">Documents Verified</dt>
                                        <dd>{clearance.verification_progress}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm text-muted-foreground">VPAA Approved</dt>
                                        <dd>{clearance.vpaa_approved_at ?? 'Pending'}</dd>
                                    </div>
                                    <div>
                                        <dt className="text-sm text-muted-foreground">TOR Released</dt>
                                        <dd>{clearance.tor_released_at ?? 'Pending'}</dd>
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
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </CardContent>
                        </Card>

                        {clearance.status === 'approved' && !clearance.has_clearance_form && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Submit Clearance Form</CardTitle>
                                </CardHeader>
                                <CardContent>
                                    <div className="flex items-end gap-4">
                                        <div className="flex-1">
                                            <input
                                                type="file"
                                                accept=".pdf,.jpg,.jpeg,.png"
                                                onChange={handleFileChange}
                                                className="text-sm"
                                            />
                                            <InputError message={form.errors.file} />
                                        </div>
                                        <Button
                                            disabled={!form.data.file || form.processing}
                                            onClick={handleSubmit}
                                        >
                                            Submit & Release TOR
                                        </Button>
                                    </div>
                                </CardContent>
                            </Card>
                        )}

                        {(canDownloadDiploma || canDownloadTor) && (
                            <Card>
                                <CardHeader>
                                    <CardTitle>Downloads</CardTitle>
                                </CardHeader>
                                <CardContent className="flex gap-4">
                                    {canDownloadDiploma && (
                                        <Button variant="outline" onClick={() => router.get(GraduationController.downloadDiploma.url())}>
                                            Download Diploma
                                        </Button>
                                    )}
                                    {canDownloadTor && (
                                        <Button variant="outline" onClick={() => router.get(GraduationController.downloadTor.url())}>
                                            Download TOR
                                        </Button>
                                    )}
                                </CardContent>
                            </Card>
                        )}
                    </>
                )}
            </div>
        </>
    );
}

StudentGraduation.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Graduation Clearance', href: GraduationController.show() },
    ],
};
