import { Head } from '@inertiajs/react';
import InterviewController from '@/actions/App/Http/Controllers/Assessor/InterviewController';
import AssessmentCriteriaForm from '@/components/assessment-criteria-form';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { AssessmentFormRecord, DocumentChecklistItem, InterviewRecord } from '@/types';

type PageProps = {
    interview: InterviewRecord;
    checklist: DocumentChecklistItem[];
    myValidation: AssessmentFormRecord | null;
    myOral: AssessmentFormRecord | null;
};

export default function AssessorInterviewShow({ interview, checklist, myValidation, myOral }: PageProps) {
    return (
        <>
            <Head title={`Interview — ${interview.application?.user.name}`} />

            <div className="flex flex-col gap-6 p-4">
                <Heading title={`${interview.application?.user.name}`} description={`Status: ${interview.status}`} />

                <Card>
                    <CardHeader>
                        <CardTitle>Portfolio Checklist</CardTitle>
                    </CardHeader>
                    <CardContent className="space-y-2">
                        {checklist.map((item) => (
                            <div key={item.key} className="flex items-center justify-between rounded-lg border p-3 text-sm">
                                <span>{item.label}</span>
                                <Badge>{item.status ?? 'missing'}</Badge>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Validation Tool</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <AssessmentCriteriaForm actionUrl={InterviewController.submitValidation(interview.id).url} existing={myValidation} />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Oral Competency Form</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <AssessmentCriteriaForm actionUrl={InterviewController.submitOral(interview.id).url} existing={myOral} />
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AssessorInterviewShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'My Interviews', href: InterviewController.index() },
    ],
};
