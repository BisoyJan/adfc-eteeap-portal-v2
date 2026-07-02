import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
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

const statusVariant: Record<string, 'default' | 'secondary' | 'destructive' | 'outline'> = {
    credited: 'secondary',
    enrolled: 'default',
    pending: 'outline',
    completed: 'default',
    not_taken: 'destructive',
};

type SubjectChecklist = {
    id: number;
    code: string;
    title: string;
    units: number;
    category: string;
    year_level: number | null;
    semester: number | null;
    status: string;
    grade: { id: number; grade: string | null; grade_point: number | null; status: string } | null;
};

type PageProps = {
    subjects: SubjectChecklist[];
};

export default function CurriculumChecklist({ subjects }: PageProps) {
    const groupedByYear: Record<number, SubjectChecklist[]> = {};

    for (const s of subjects) {
        const yl = s.year_level ?? 0;

        if (!groupedByYear[yl]) {
            groupedByYear[yl] = [];
        }

        groupedByYear[yl].push(s);
    }

    return (
        <>
            <Head title="Curriculum Checklist" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Curriculum Checklist" description="Track your progress through the BSIT program" />

                {Object.entries(groupedByYear)
                    .sort(([a], [b]) => Number(a) - Number(b))
                    .map(([year, yearSubjects]) => (
                        <Card key={year}>
                            <CardHeader>
                                <CardTitle>Year {year === '0' ? 'N/A' : year}</CardTitle>
                            </CardHeader>
                            <CardContent>
                                <Table>
                                    <TableHeader>
                                        <TableRow>
                                            <TableHead>Code</TableHead>
                                            <TableHead>Title</TableHead>
                                            <TableHead>Category</TableHead>
                                            <TableHead>Units</TableHead>
                                            <TableHead>Status</TableHead>
                                            <TableHead>Grade</TableHead>
                                        </TableRow>
                                    </TableHeader>
                                    <TableBody>
                                        {yearSubjects.map((s) => (
                                            <TableRow key={s.id}>
                                                <TableCell className="font-mono text-sm">{s.code}</TableCell>
                                                <TableCell>{s.title}</TableCell>
                                                <TableCell className="text-sm text-muted-foreground">{s.category}</TableCell>
                                                <TableCell>{s.units}</TableCell>
                                                <TableCell>
                                                    <Badge variant={statusVariant[s.status] ?? 'outline'}>
                                                        {s.status.replace(/_/g, ' ')}
                                                    </Badge>
                                                </TableCell>
                                                <TableCell>
                                                    {s.grade ? (
                                                        <span>
                                                            {s.grade.grade ?? '-'}
                                                            {s.grade.grade_point ? ` (${s.grade.grade_point})` : ''}
                                                        </span>
                                                    ) : (
                                                        <span className="text-muted-foreground">-</span>
                                                    )}
                                                </TableCell>
                                            </TableRow>
                                        ))}
                                    </TableBody>
                                </Table>
                            </CardContent>
                        </Card>
                    ))}
            </div>
        </>
    );
}

CurriculumChecklist.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Curriculum Checklist', href: '' },
    ],
};
