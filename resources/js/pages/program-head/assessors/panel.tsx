import { Head, useForm } from '@inertiajs/react';
import AssessorController from '@/actions/App/Http/Controllers/ProgramHead/AssessorController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes';
import type { Application, InterviewRecord } from '@/types';

type Assessor = {
    id: number;
    name: string;
    email: string;
    roles: { id: number; name: string }[];
};

type PageProps = {
    application: Application & { user: { id: number; name: string; email: string } };
    assessors: Assessor[];
    interview: InterviewRecord | null;
};

export default function PanelForm({ application, assessors, interview }: PageProps) {
    const form = useForm<{ panelist_ids: number[] }>({
        panelist_ids: interview?.panelists?.map((p) => p.id) ?? [],
    });

    function toggle(id: number, checked: boolean) {
        form.setData('panelist_ids', checked ? [...form.data.panelist_ids, id] : form.data.panelist_ids.filter((existing) => existing !== id));
    }

    function submit() {
        form.post(AssessorController.assignPanel(application.id).url, { preserveScroll: true });
    }

    return (
        <>
            <Head title={`Assign Panel — ${application.application_number}`} />

            <div className="flex flex-col gap-6 p-4">
                <Heading title={`Assign Panel — ${application.user.name}`} description="Select at least 3 assessors, including one internal and one external" />

                <InputError message={form.errors.panelist_ids} />

                <Card>
                    <CardContent className="space-y-2">
                        {assessors.map((assessor) => (
                            <div key={assessor.id} className="flex items-center gap-2 rounded-lg border p-3">
                                <Checkbox
                                    id={`assessor-${assessor.id}`}
                                    checked={form.data.panelist_ids.includes(assessor.id)}
                                    onCheckedChange={(checked) => toggle(assessor.id, checked === true)}
                                />
                                <Label htmlFor={`assessor-${assessor.id}`}>
                                    {assessor.name} &middot; {assessor.roles.map((r) => r.name).join(', ')}
                                </Label>
                            </div>
                        ))}
                    </CardContent>
                </Card>

                <div className="flex justify-end">
                    <Button onClick={submit} disabled={form.processing}>
                        Save panel
                    </Button>
                </div>
            </div>
        </>
    );
}

PanelForm.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
