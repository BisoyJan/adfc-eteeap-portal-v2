import { Head, Link } from '@inertiajs/react';
import RubricController from '@/actions/App/Http/Controllers/Adviser/RubricController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';

type Rubric = {
    id: number;
    title: string;
    curriculum_subject: { id: number; code: string; title: string } | null;
    max_score: number | null;
    is_template: boolean;
    created_at: string;
};

type PageProps = {
    rubrics: Rubric[];
};

export default function RubricsIndex({ rubrics }: PageProps) {
    return (
        <>
            <Head title="Rubrics" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Rubrics" description="Manage scoring rubrics for enhancement activities" />

                <div className="flex justify-end">
                    <Link href={RubricController.create()}>
                        <Button>Create Rubric</Button>
                    </Link>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>All Rubrics</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Max Score</TableHead>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Created</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {rubrics.map((r) => (
                                    <TableRow key={r.id}>
                                        <TableCell className="font-medium">{r.title}</TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {r.curriculum_subject?.code ?? '-'}
                                        </TableCell>
                                        <TableCell>{r.max_score ?? '-'}</TableCell>
                                        <TableCell>
                                            <Badge variant={r.is_template ? 'secondary' : 'default'}>
                                                {r.is_template ? 'Template' : 'Custom'}
                                            </Badge>
                                        </TableCell>
                                        <TableCell className="text-sm">{new Date(r.created_at).toLocaleDateString()}</TableCell>
                                    </TableRow>
                                ))}
                                {rubrics.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="text-center text-muted-foreground py-8">
                                            No rubrics yet.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

RubricsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Rubrics', href: RubricController.index() },
    ],
};
