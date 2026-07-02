import { Head, useForm } from '@inertiajs/react';
import ActivityController from '@/actions/App/Http/Controllers/Adviser/ActivityController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { dashboard } from '@/routes';

type Submission = {
    id: number;
    file_path: string | null;
    notes: string | null;
    submitted_at: string;
};

type Activity = {
    id: number;
    student: { id: number; name: string; email: string };
    curriculum_subject: { id: number; code: string; title: string } | null;
    activity_type: string;
    title: string;
    description: string | null;
    status: string;
    score: number | null;
    feedback: string | null;
    due_date: string | null;
    submissions: Submission[];
};

type PageProps = {
    activity: Activity;
};

export default function ActivityShow({ activity }: PageProps) {
    const scoreForm = useForm({
        score: String(activity.score ?? ''),
        feedback: activity.feedback ?? '',
    });

    function submitScore() {
        scoreForm.put(ActivityController.score(activity.id).url, { preserveScroll: true });
    }

    return (
        <>
            <Head title={activity.title} />

            <div className="flex flex-col gap-6 p-4">
                <Heading title={activity.title} description={`For ${activity.student.name} — ${activity.activity_type.replace(/_/g, ' ')}`} />

                <div className="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader><CardTitle>Details</CardTitle></CardHeader>
                        <CardContent className="space-y-2 text-sm">
                            <p><strong>Student:</strong> {activity.student.name}</p>
                            <p><strong>Subject:</strong> {activity.curriculum_subject ? `${activity.curriculum_subject.code} - ${activity.curriculum_subject.title}` : 'N/A'}</p>
                            <p><strong>Type:</strong> {activity.activity_type.replace(/_/g, ' ')}</p>
                            <p><strong>Status:</strong> <Badge>{activity.status.replace(/_/g, ' ')}</Badge></p>
                            <p><strong>Due:</strong> {activity.due_date ? new Date(activity.due_date).toLocaleDateString() : 'No due date'}</p>
                            {activity.description && (
                                <div>
                                    <strong>Description:</strong>
                                    <p className="mt-1 text-muted-foreground">{activity.description}</p>
                                </div>
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Submissions</CardTitle></CardHeader>
                        <CardContent>
                            {activity.submissions.length === 0 ? (
                                <p className="text-sm text-muted-foreground">No submissions yet.</p>
                            ) : (
                                <div className="space-y-2">
                                    {activity.submissions.map((sub) => (
                                        <div key={sub.id} className="rounded-lg border p-3 text-sm">
                                            <p className="text-muted-foreground">
                                                Submitted {new Date(sub.submitted_at).toLocaleString()}
                                            </p>
                                            {sub.notes && <p className="mt-1">{sub.notes}</p>}
                                            {sub.file_path && (
                                                <a href={dashboard().url} className="mt-1 block text-blue-600 underline">
                                                    View attachment
                                                </a>
                                            )}
                                        </div>
                                    ))}
                                </div>
                            )}
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader><CardTitle>Score & Feedback</CardTitle></CardHeader>
                    <CardContent className="space-y-4">
                        <div className="w-48 space-y-1">
                            <Label htmlFor="score">Score (0–100)</Label>
                            <Input
                                id="score"
                                type="number"
                                min="0"
                                max="100"
                                value={scoreForm.data.score}
                                onChange={(e) => scoreForm.setData('score', e.target.value)}
                            />
                            <InputError message={scoreForm.errors.score} />
                        </div>
                        <div className="space-y-1">
                            <Label htmlFor="feedback">Feedback</Label>
                            <Textarea
                                id="feedback"
                                value={scoreForm.data.feedback}
                                onChange={(e) => scoreForm.setData('feedback', e.target.value)}
                                rows={3}
                            />
                            <InputError message={scoreForm.errors.feedback} />
                        </div>
                        <div className="flex justify-end">
                            <Button onClick={submitScore} disabled={scoreForm.processing}>
                                Submit Score
                            </Button>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ActivityShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Activities', href: ActivityController.index() },
        { title: 'Activity Detail', href: '#' },
    ],
};
