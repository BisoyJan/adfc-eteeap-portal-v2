import { Head, Link } from '@inertiajs/react';
import InterviewController from '@/actions/App/Http/Controllers/Assessor/InterviewController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { InterviewRecord } from '@/types';

type PageProps = {
    interviews: InterviewRecord[];
};

export default function AssessorInterviewsIndex({ interviews }: PageProps) {
    return (
        <>
            <Head title="My Interviews" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="My Panel Interviews" description="Applicants assigned to your assessment panel" />

                <Card>
                    <CardContent className="space-y-2">
                        {interviews.map((interview) => (
                            <Link
                                key={interview.id}
                                href={InterviewController.show(interview.id)}
                                className="flex items-center justify-between rounded-lg border p-3 text-sm hover:bg-accent"
                            >
                                <div>
                                    <p className="font-medium">{interview.application?.user.name}</p>
                                    <p className="text-muted-foreground">{interview.scheduled_at ?? 'Not yet scheduled'}</p>
                                </div>
                                <Badge>{interview.status}</Badge>
                            </Link>
                        ))}
                        {interviews.length === 0 && <p className="text-muted-foreground text-sm">No interviews assigned yet.</p>}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AssessorInterviewsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'My Interviews', href: InterviewController.index() },
    ],
};
