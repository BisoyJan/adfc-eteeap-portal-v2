import { Head, Link, router } from '@inertiajs/react';
import AssessorController from '@/actions/App/Http/Controllers/ProgramHead/AssessorController';
import EquivalencyController from '@/actions/App/Http/Controllers/ProgramHead/EquivalencyController';
import EquivalencyMatrixEditor from '@/components/equivalency-matrix-editor';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { Application, EquivalencyMatrixRecord } from '@/types';

type PageProps = {
    application: Application & { user: { id: number; name: string; email: string } };
    matrix: EquivalencyMatrixRecord;
};

export default function EquivalencyShow({ application, matrix }: PageProps) {
    function submitMatrix() {
        router.post(EquivalencyController.submit(application.id).url, {}, { preserveScroll: true });
    }

    return (
        <>
            <Head title={`Equivalency — ${application.application_number}`} />

            <div className="flex flex-col gap-6 p-4">
                <Heading title={`${application.user.name} — Initial Equivalency Matrix`} description={`Status: ${matrix.status}`} />

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center justify-between">
                            Subjects
                            <Link href={AssessorController.panelForm(application.id)} className="text-sm underline">
                                Assign assessment panel
                            </Link>
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <EquivalencyMatrixEditor
                            actionUrl={EquivalencyController.store(application.id).url}
                            initialItems={matrix.items ?? []}
                            disabled={matrix.status !== 'draft'}
                        />
                    </CardContent>
                </Card>

                {matrix.status === 'draft' && (
                    <div className="flex justify-end">
                        <Button onClick={submitMatrix}>Submit matrix</Button>
                    </div>
                )}
            </div>
        </>
    );
}

EquivalencyShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Equivalency Queue', href: EquivalencyController.pending() },
    ],
};
