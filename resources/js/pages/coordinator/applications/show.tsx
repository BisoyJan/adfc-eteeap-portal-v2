import { Head, router, useForm } from '@inertiajs/react';
import ApplicationController from '@/actions/App/Http/Controllers/Coordinator/ApplicationController';
import AssessmentController from '@/actions/App/Http/Controllers/Coordinator/AssessmentController';
import DocumentController from '@/actions/App/Http/Controllers/Coordinator/DocumentController';
import AssessmentCriteriaForm from '@/components/assessment-criteria-form';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';
import type { ApplicantProfile, Application, AssessmentFormRecord, DocumentChecklistItem, DocumentProgress, EducationRecord, ExperienceRecord } from '@/types';

type PageProps = {
    application: Application & {
        user: {
            id: number;
            name: string;
            email: string;
            applicantProfile: ApplicantProfile | null;
            experienceRecords: ExperienceRecord[];
            educationRecords: EducationRecord[];
        };
    };
    checklist: DocumentChecklistItem[];
    progress: DocumentProgress;
    firstAssessment: AssessmentFormRecord | null;
    secondAssessment: AssessmentFormRecord | null;
};

export default function ApplicationShow({ application, checklist, progress, firstAssessment, secondAssessment }: PageProps) {
    const reviewForm = useForm({
        decision: 'approve',
        notes: '',
        rejection_reason: '',
    });

    function submitReview() {
        reviewForm.put(ApplicationController.review.url(application.id), { preserveScroll: true });
    }

    function verifyDocument(documentId: number, decision: 'verify' | 'reject') {
        const rejection_reason = decision === 'reject' ? window.prompt('Rejection reason') ?? '' : '';

        router.put(DocumentController.verify.url(documentId), { decision, rejection_reason }, { preserveScroll: true });
    }

    return (
        <>
            <Head title={`Application ${application.application_number}`} />

            <div className="flex flex-col gap-6 p-4">
                <Heading
                    title={`${application.user.name} — ${application.application_number}`}
                    description={`Status: ${application.status.replace('_', ' ')}`}
                />

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>Profile</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-1 text-sm">
                            <p>Email: {application.user.email}</p>
                            <p>Birth date: {application.user.applicantProfile?.birth_date ?? '—'}</p>
                            <p>Highest education: {application.user.applicantProfile?.highest_education ?? '—'}</p>
                            <p>Citizenship: {application.user.applicantProfile?.citizenship ?? '—'}</p>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>Work Experience</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-2 text-sm">
                            {application.user.experienceRecords.map((record) => (
                                <p key={record.id}>
                                    {record.position} at {record.company} ({record.start_date} &ndash; {record.is_current ? 'Present' : record.end_date})
                                </p>
                            ))}
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>
                            Documents ({progress.verified_required}/{progress.total_required} required verified)
                        </CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-2">
                        {checklist.map((item) => (
                            <div key={item.key} className="flex items-center justify-between rounded-lg border p-3 text-sm">
                                <div>
                                    <p className="font-medium">{item.label}</p>
                                    {item.document && <p className="text-muted-foreground">{item.document.file_name}</p>}
                                </div>
                                <div className="flex items-center gap-2">
                                    <Badge>{item.status ?? 'missing'}</Badge>
                                    {item.document && item.status !== 'verified' && (
                                        <>
                                            <Button size="sm" onClick={() => verifyDocument(item.document!.id, 'verify')}>
                                                Verify
                                            </Button>
                                            <Button size="sm" variant="outline" onClick={() => verifyDocument(item.document!.id, 'reject')}>
                                                Reject
                                            </Button>
                                        </>
                                    )}
                                </div>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                <div className="grid gap-6 lg:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle>F.1 Initial Screening</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <AssessmentCriteriaForm
                                actionUrl={AssessmentController.submitFirst(application.id).url}
                                existing={firstAssessment}
                            />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle>F.2 Portfolio Evaluation</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <AssessmentCriteriaForm
                                actionUrl={AssessmentController.submitSecond(application.id).url}
                                existing={secondAssessment}
                            />
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Review Decision</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex gap-2">
                            {(['approve', 'reject', 'request_revision'] as const).map((decision) => (
                                <Button
                                    key={decision}
                                    type="button"
                                    variant={reviewForm.data.decision === decision ? 'default' : 'outline'}
                                    onClick={() => reviewForm.setData('decision', decision)}
                                >
                                    {decision.replace('_', ' ')}
                                </Button>
                            ))}
                        </div>

                        <Textarea
                            placeholder="Notes"
                            value={reviewForm.data.notes}
                            onChange={(e) => reviewForm.setData('notes', e.target.value)}
                        />
                        <InputError message={reviewForm.errors.notes} />

                        {reviewForm.data.decision === 'reject' && (
                            <>
                                <Textarea
                                    placeholder="Rejection reason"
                                    value={reviewForm.data.rejection_reason}
                                    onChange={(e) => reviewForm.setData('rejection_reason', e.target.value)}
                                />
                                <InputError message={reviewForm.errors.rejection_reason} />
                            </>
                        )}

                        <Button onClick={submitReview} disabled={reviewForm.processing}>
                            Submit decision
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ApplicationShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Applications', href: ApplicationController.index() },
    ],
};
