import { Head, Link } from '@inertiajs/react';
import ApplicationController from '@/actions/App/Http/Controllers/Coordinator/ApplicationController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { Application } from '@/types';

type Column = {
    status: string;
    label: string;
    applications: (Application & { user: { id: number; name: string; email: string } })[];
};

type PageProps = {
    columns: Column[];
};

export default function ApplicationsKanban({ columns }: PageProps) {
    return (
        <>
            <Head title="Applications" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Applications" description="Track every applicant through the ETEEAP pipeline" />

                <div className="grid gap-4 overflow-x-auto md:grid-cols-3 xl:grid-cols-6">
                    {columns.map((column) => (
                        <Card key={column.status} className="min-w-[220px]">
                            <CardHeader>
                                <CardTitle className="flex items-center justify-between text-sm">
                                    {column.label}
                                    <Badge variant="secondary">{column.applications.length}</Badge>
                                </CardTitle>
                            </CardHeader>
                            <CardContent className="space-y-2">
                                {column.applications.map((application) => (
                                    <Link
                                        key={application.id}
                                        href={ApplicationController.show(application.id)}
                                        className="block rounded-lg border p-3 text-sm transition-colors hover:bg-accent"
                                    >
                                        <p className="font-medium">{application.user.name}</p>
                                        <p className="text-muted-foreground">{application.application_number}</p>
                                    </Link>
                                ))}
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>
        </>
    );
}

ApplicationsKanban.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Applications', href: ApplicationController.index() },
    ],
};
