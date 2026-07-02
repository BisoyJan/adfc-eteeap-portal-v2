import { Head, router } from '@inertiajs/react';
import EquivalencyController from '@/actions/App/Http/Controllers/QaOfficer/EquivalencyController';
import EquivalencyMatrixEditor from '@/components/equivalency-matrix-editor';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { Application, EquivalencyMatrixRecord } from '@/types';

type PageProps = {
    application: Application & { user: { id: number; name: string; email: string } };
    initialMatrix: EquivalencyMatrixRecord;
    finalMatrix: EquivalencyMatrixRecord;
};

export default function QaEquivalencyShow({ application, initialMatrix, finalMatrix }: PageProps) {
    function approve() {
        router.post(EquivalencyController.approve(application.id).url, {}, { preserveScroll: true });
    }

    return (
        <>
            <Head title={`Final Matrix — ${application.application_number}`} />

            <div className="flex flex-col gap-6 p-4">
                <Heading title={`${application.user.name} — Final Equivalency Matrix`} description={`Status: ${finalMatrix.status}`} />

                <Card>
                    <CardHeader>
                        <CardTitle>Initial Matrix (Reference)</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <EquivalencyMatrixEditor actionUrl="" initialItems={initialMatrix.items ?? []} disabled />
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Final Matrix</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <EquivalencyMatrixEditor
                            actionUrl={EquivalencyController.store(application.id).url}
                            initialItems={finalMatrix.items ?? []}
                            disabled={finalMatrix.status === 'approved'}
                        />
                    </CardContent>
                </Card>

                {finalMatrix.status !== 'approved' && (
                    <div className="flex justify-end">
                        <Button onClick={approve}>Approve final matrix</Button>
                    </div>
                )}
            </div>
        </>
    );
}

QaEquivalencyShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Pending Validations', href: EquivalencyController.pending() },
    ],
};
