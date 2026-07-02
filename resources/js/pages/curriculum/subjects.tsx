import { Head } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

type Subject = {
    id: number;
    code: string;
    title: string;
    units: number;
    category: string;
    year_level: number | null;
    semester: number | null;
};

type PageProps = {
    subjects: Subject[];
};

export default function PublicCurriculumSubjects({ subjects }: PageProps) {
    return (
        <>
            <Head title="BSIT Curriculum" />

            <div className="mx-auto flex max-w-5xl flex-col gap-6 p-4">
                <div>
                    <h1 className="text-xl font-semibold tracking-tight">BSIT Curriculum</h1>
                    <p className="text-muted-foreground text-sm">Active subjects offered under the current curriculum.</p>
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle>Subjects</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Code</TableHead>
                                    <TableHead>Title</TableHead>
                                    <TableHead>Units</TableHead>
                                    <TableHead>Category</TableHead>
                                    <TableHead>Year</TableHead>
                                    <TableHead>Sem</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {subjects.map((subject) => (
                                    <TableRow key={subject.id}>
                                        <TableCell className="font-mono text-sm">{subject.code}</TableCell>
                                        <TableCell>{subject.title}</TableCell>
                                        <TableCell>{subject.units}</TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">{subject.category}</Badge>
                                        </TableCell>
                                        <TableCell>{subject.year_level ?? '-'}</TableCell>
                                        <TableCell>{subject.semester ?? '-'}</TableCell>
                                    </TableRow>
                                ))}
                                {subjects.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={6} className="text-muted-foreground py-8 text-center">
                                            No subjects published yet.
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
