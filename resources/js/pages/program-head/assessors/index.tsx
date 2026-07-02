import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';

type Assessor = {
    id: number;
    name: string;
    email: string;
    roles: { id: number; name: string }[];
};

type PageProps = {
    assessors: Assessor[];
};

export default function AssessorsIndex({ assessors }: PageProps) {
    return (
        <>
            <Head title="Assessors" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Assessor Roster" description="Internal and external assessors available for panel duty" />

                <Card>
                    <CardContent className="space-y-2">
                        {assessors.map((assessor) => (
                            <div key={assessor.id} className="flex items-center justify-between rounded-lg border p-3 text-sm">
                                <div>
                                    <p className="font-medium">{assessor.name}</p>
                                    <p className="text-muted-foreground">{assessor.email}</p>
                                </div>
                                <div className="flex gap-1">
                                    {assessor.roles.map((role) => (
                                        <Badge key={role.id} variant="secondary">
                                            {role.name}
                                        </Badge>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AssessorsIndex.layout = {
    breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
};
