import { Head, Link } from '@inertiajs/react';
import EquivalencyController from '@/actions/App/Http/Controllers/QaOfficer/EquivalencyController';
import Heading from '@/components/heading';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { Application } from '@/types';

type PageProps = {
    applications: (Application & { user: { id: number; name: string; email: string } })[];
};

export default function ValidationsPending({ applications }: PageProps) {
    return (
        <>
            <Head title="Pending Validations" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Pending Final Matrices" description="Applications whose panel interview is complete" />

                <Card>
                    <CardContent className="space-y-2">
                        {applications.map((application) => (
                            <Link
                                key={application.id}
                                href={EquivalencyController.show(application.id)}
                                className="block rounded-lg border p-3 text-sm hover:bg-accent"
                            >
                                <p className="font-medium">{application.user.name}</p>
                                <p className="text-muted-foreground">{application.application_number}</p>
                            </Link>
                        ))}
                        {applications.length === 0 && <p className="text-muted-foreground text-sm">No applications awaiting a final matrix.</p>}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ValidationsPending.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Pending Validations', href: EquivalencyController.pending() },
    ],
};
