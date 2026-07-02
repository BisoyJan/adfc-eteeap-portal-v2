import { Head, useForm, usePage } from '@inertiajs/react';
import SystemController from '@/actions/App/Http/Controllers/Admin/SystemController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';

type PageProps = {
    health: {
        db: boolean;
        queue: string;
        cache: boolean;
        storage: boolean;
        php_version: string;
        laravel_version: string;
        environment: string;
    };
    config: {
        min_age: string;
        min_experience_years: string;
        adviser_max_students: string;
        student_max_subjects: string;
        panel_min_assessors: string;
    };
};

function HealthBadge({ ok }: { ok: boolean }) {
    return ok ? <Badge variant="default" className="bg-green-600">Healthy</Badge> : <Badge variant="destructive">Unhealthy</Badge>;
}

export default function SystemHealth({ health, config }: PageProps) {
    const form = useForm({
        min_age: config.min_age,
        min_experience_years: config.min_experience_years,
        adviser_max_students: config.adviser_max_students,
        student_max_subjects: config.student_max_subjects,
        panel_min_assessors: config.panel_min_assessors,
    });

    function submit() {
        form.put(SystemController['update-config']().url, { preserveScroll: true });
    }

    return (
        <>
            <Head title="System" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="System Health" description="Monitor and configure the ETEEAP platform" />

                <div className="grid grid-cols-2 gap-4">
                    <Card>
                        <CardHeader><CardTitle>Database</CardTitle></CardHeader>
                        <CardContent>
                            <HealthBadge ok={health.db} />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Queue</CardTitle></CardHeader>
                        <CardContent>
                            <p className="text-sm text-muted-foreground mb-1">Driver: {health.queue}</p>
                            {health.queue === 'sync' ? (
                                <Badge variant="secondary">Sync (no queue worker needed)</Badge>
                            ) : (
                                <HealthBadge ok={true} />
                            )}
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Cache</CardTitle></CardHeader>
                        <CardContent>
                            <HealthBadge ok={health.cache} />
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Storage</CardTitle></CardHeader>
                        <CardContent>
                            <HealthBadge ok={health.storage} />
                        </CardContent>
                    </Card>
                </div>

                <Card>
                    <CardHeader><CardTitle>Application Info</CardTitle></CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-3 gap-4">
                            <div>
                                <span className="text-sm text-muted-foreground">PHP Version</span>
                                <p className="font-medium">{health.php_version}</p>
                            </div>
                            <div>
                                <span className="text-sm text-muted-foreground">Laravel Version</span>
                                <p className="font-medium">{health.laravel_version}</p>
                            </div>
                            <div>
                                <span className="text-sm text-muted-foreground">Environment</span>
                                <p className="font-medium">{health.environment}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>System Configuration</CardTitle></CardHeader>
                    <CardContent>
                        <form onSubmit={(e) => { e.preventDefault(); submit(); }} className="space-y-4">
                            <div className="grid grid-cols-2 gap-4">
                                <div className="space-y-1">
                                    <Label htmlFor="min_age">Minimum Age</Label>
                                    <Input id="min_age" type="number" value={form.data.min_age} onChange={(e) => form.setData('min_age', e.target.value)} />
                                    <InputError message={form.errors.min_age} />
                                </div>
                                <div className="space-y-1">
                                    <Label htmlFor="min_experience_years">Minimum Experience (Years)</Label>
                                    <Input id="min_experience_years" type="number" value={form.data.min_experience_years} onChange={(e) => form.setData('min_experience_years', e.target.value)} />
                                    <InputError message={form.errors.min_experience_years} />
                                </div>
                                <div className="space-y-1">
                                    <Label htmlFor="adviser_max_students">Max Students per Adviser</Label>
                                    <Input id="adviser_max_students" type="number" value={form.data.adviser_max_students} onChange={(e) => form.setData('adviser_max_students', e.target.value)} />
                                    <InputError message={form.errors.adviser_max_students} />
                                </div>
                                <div className="space-y-1">
                                    <Label htmlFor="student_max_subjects">Max Subjects per Student</Label>
                                    <Input id="student_max_subjects" type="number" value={form.data.student_max_subjects} onChange={(e) => form.setData('student_max_subjects', e.target.value)} />
                                    <InputError message={form.errors.student_max_subjects} />
                                </div>
                                <div className="space-y-1">
                                    <Label htmlFor="panel_min_assessors">Minimum Panel Assessors</Label>
                                    <Input id="panel_min_assessors" type="number" value={form.data.panel_min_assessors} onChange={(e) => form.setData('panel_min_assessors', e.target.value)} />
                                    <InputError message={form.errors.panel_min_assessors} />
                                </div>
                            </div>

                            <div className="flex justify-end">
                                <Button type="submit" disabled={form.processing}>
                                    Save Configuration
                                </Button>
                            </div>
                        </form>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

SystemHealth.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'System', href: SystemController['health']() },
    ],
};
