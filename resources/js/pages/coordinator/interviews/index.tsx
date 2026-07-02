import { Head, useForm } from '@inertiajs/react';
import InterviewController from '@/actions/App/Http/Controllers/Coordinator/InterviewController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import type { InterviewRecord } from '@/types';

type PageProps = {
    interviews: InterviewRecord[];
};

function ScheduleForm({ interview }: { interview: InterviewRecord }) {
    const form = useForm({
        scheduled_at: interview.scheduled_at ?? '',
        location: interview.location ?? '',
    });

    function submit() {
        form.put(InterviewController.schedule(interview.id).url, { preserveScroll: true });
    }

    return (
        <div className="flex flex-col gap-2 sm:flex-row sm:items-end">
            <div className="grid gap-2">
                <Label>Scheduled at</Label>
                <Input type="datetime-local" value={form.data.scheduled_at} onChange={(e) => form.setData('scheduled_at', e.target.value)} />
                <InputError message={form.errors.scheduled_at} />
            </div>
            <div className="grid gap-2">
                <Label>Location</Label>
                <Input value={form.data.location} onChange={(e) => form.setData('location', e.target.value)} />
            </div>
            <Button type="button" onClick={submit} disabled={form.processing}>
                Schedule
            </Button>
        </div>
    );
}

export default function InterviewsIndex({ interviews }: PageProps) {
    return (
        <>
            <Head title="Panel Interviews" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Panel Interviews" description="Schedule F.3 panel interviews once assessors have been assigned" />

                <Card>
                    <CardContent className="space-y-4">
                        {interviews.map((interview) => (
                            <div key={interview.id} className="space-y-2 rounded-lg border p-3">
                                <div className="flex items-center justify-between">
                                    <p className="font-medium">{interview.application?.user.name}</p>
                                    <Badge>{interview.status}</Badge>
                                </div>
                                <p className="text-muted-foreground text-sm">Panelists: {interview.panelists?.map((p) => p.name).join(', ')}</p>
                                {interview.status !== 'completed' && interview.status !== 'cancelled' && <ScheduleForm interview={interview} />}
                            </div>
                        ))}
                        {interviews.length === 0 && <p className="text-muted-foreground text-sm">No interviews yet.</p>}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

InterviewsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Interviews', href: InterviewController.index() },
    ],
};
