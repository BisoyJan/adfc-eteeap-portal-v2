import { Head, Link } from '@inertiajs/react';
import { FileText, FolderOpen, ClipboardList } from 'lucide-react';
import { StatsCard } from '@/components/dashboard/stats-card';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

type AppInfo = { id: number; application_number: string; status: string; status_label: string; submitted_at: string | null };
type DocProgress = { total_required: number; uploaded_required: number; verified_required: number };
type PortfolioInfo = { id: number; status: string; status_value: string };

type PageProps = {
    application: AppInfo | null;
    documentProgress: DocProgress;
    portfolio: PortfolioInfo | null;
};

export default function ApplicantDashboard({
    application,
    documentProgress,
}: PageProps) {
    const uploadPercent = documentProgress.total_required > 0
        ? Math.round((documentProgress.uploaded_required / documentProgress.total_required) * 100)
        : 0;
    const verifyPercent = documentProgress.total_required > 0
        ? Math.round((documentProgress.verified_required / documentProgress.total_required) * 100)
        : 0;

    return (
        <>
            <Head title="Dashboard" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="Dashboard" description="Track your application status" />

                <div className="grid gap-4 md:grid-cols-3">
                    <StatsCard
                        title="Application"
                        value={application?.status_label ?? 'Not Started'}
                        icon={FileText}
                    />
                    <StatsCard
                        title="Documents Uploaded"
                        value={`${documentProgress.uploaded_required}/${documentProgress.total_required}`}
                        icon={FolderOpen}
                        description={`${uploadPercent}% complete`}
                    />
                    <StatsCard
                        title="Documents Verified"
                        value={`${documentProgress.verified_required}/${documentProgress.total_required}`}
                        icon={ClipboardList}
                        description={`${verifyPercent}% verified`}
                    />
                </div>

                {application && (
                    <Card>
                        <CardHeader>
                            <CardTitle>Application Summary</CardTitle>
                        </CardHeader>
                        <CardContent className="space-y-2">
                            <div className="flex items-center justify-between">
                                <span className="text-sm text-muted-foreground">Application #</span>
                                <span className="text-sm font-medium">{application.application_number}</span>
                            </div>
                            <div className="flex items-center justify-between">
                                <span className="text-sm text-muted-foreground">Status</span>
                                <Badge>{application.status_label}</Badge>
                            </div>
                            {application.submitted_at && (
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-muted-foreground">Submitted</span>
                                    <span className="text-sm">{application.submitted_at}</span>
                                </div>
                            )}
                        </CardContent>
                    </Card>
                )}

                {!application && (
                    <Card>
                        <CardContent className="py-8 text-center">
                            <p className="text-muted-foreground">No application yet.</p>
                            <Link
                                href="/applicant/application/edit"
                                className="mt-2 inline-flex items-center text-sm font-medium text-primary hover:underline"
                            >
                                Start your application
                            </Link>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}

ApplicantDashboard.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
    ],
};
