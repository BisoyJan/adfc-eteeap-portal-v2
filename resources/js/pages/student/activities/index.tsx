import { Head, useForm } from '@inertiajs/react';
import ActivityController from '@/actions/App/Http/Controllers/Student/ActivityController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    assigned: 'secondary',
    in_progress: 'default',
    submitted: 'outline',
    approved: 'default',
    rejected: 'destructive',
};

type Submission = {
    id: number;
    file_path: string | null;
    notes: string | null;
    submitted_at: string;
};

type Activity = {
    id: number;
    title: string;
    description: string | null;
    activity_type: string;
    status: string;
    score: number | null;
    feedback: string | null;
    due_date: string | null;
    adviser: { id: number; name: string; email: string };
    curriculum_subject: { id: number; code: string; title: string } | null;
    submissions: Submission[];
};

type PageProps = {
    activities: Activity[];
};

export default function StudentActivitiesIndex({ activities }: PageProps) {
    return (
        <>
            <Head title="My Activities" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="My Enhancement Activities" description="Complete the activities assigned by your adviser" />

                {activities.length === 0 ? (
                    <Card>
                        <CardContent className="py-8 text-center text-muted-foreground">
                            No activities assigned yet.
                        </CardContent>
                    </Card>
                ) : (
                    <div className="grid gap-4">
                        {activities.map((activity) => (
                            <ActivityCard key={activity.id} activity={activity} />
                        ))}
                    </div>
                )}
            </div>
        </>
    );
}

function ActivityCard({ activity: a }: { activity: Activity }) {
    const form = useForm<{ notes: string; file: File | null }>({ notes: '', file: null });

    function submitActivity() {
        form.post(ActivityController.submit(a.id).url, { preserveScroll: true, forceFormData: true });
    }

    return (
        <Card>
            <CardHeader>
                <div className="flex items-start justify-between">
                    <div>
                        <CardTitle className="text-lg">{a.title}</CardTitle>
                        <p className="text-xs text-muted-foreground">
                            {a.activity_type.replace(/_/g, ' ')}
                            {a.curriculum_subject && ` · ${a.curriculum_subject.code}`}
                        </p>
                    </div>
                    <div className="flex items-center gap-2">
                        {a.score !== null && <span className="text-sm font-medium">{a.score}/100</span>}
                        <Badge variant={statusVariant[a.status] ?? 'secondary'}>{a.status.replace(/_/g, ' ')}</Badge>
                    </div>
                </div>
            </CardHeader>
            <CardContent className="space-y-3">
                {a.description && <p className="text-sm text-muted-foreground">{a.description}</p>}
                <div className="flex items-center gap-4 text-xs text-muted-foreground">
                    <span>Adviser: {a.adviser.name}</span>
                    {a.due_date && <span>Due: {new Date(a.due_date).toLocaleDateString()}</span>}
                </div>

                {a.status === 'assigned' || a.status === 'in_progress' || a.status === 'rejected' ? (
                    <div className="space-y-2 rounded-lg border p-4">
                        <Textarea
                            placeholder="Add submission notes..."
                            value={form.data.notes}
                            onChange={(e) => form.setData('notes', e.target.value)}
                            rows={2}
                        />
                        <div className="flex items-center justify-between">
                            <input
                                type="file"
                                onChange={(e) => form.setData('file', e.target.files?.[0] ?? null)}
                                className="text-sm"
                            />
                            <Button size="sm" onClick={submitActivity} disabled={form.processing}>
                                Submit
                            </Button>
                        </div>
                    </div>
                ) : null}

                {a.feedback && (
                    <div className="rounded-lg bg-muted p-3 text-sm">
                        <strong>Feedback:</strong> {a.feedback}
                    </div>
                )}

                {a.submissions.length > 0 && (
                    <div className="space-y-1 text-xs text-muted-foreground">
                        {a.submissions.map((s) => (
                            <p key={s.id}>
                                Submitted {new Date(s.submitted_at).toLocaleString()}
                                {s.notes && ` — ${s.notes}`}
                            </p>
                        ))}
                    </div>
                )}
            </CardContent>
        </Card>
    );
}

StudentActivitiesIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'My Activities', href: ActivityController.index() },
    ],
};
