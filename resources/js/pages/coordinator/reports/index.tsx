import { Head } from '@inertiajs/react';
import { FileDown, FileSpreadsheet } from 'lucide-react';
import ReportController from '@/actions/App/Http/Controllers/Coordinator/ReportController';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

type ReportConfig = Record<string, string>;

type PageProps = {
    reports: ReportConfig;
};

export default function CoordinatorReports({ reports }: PageProps) {
    return (
        <>
            <Head title="Reports" />
            <div className="flex flex-col gap-6 p-4">
                <Heading title="Reports" description="Download program reports" />

                <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {Object.entries(reports).map(([key, label]) => (
                        <Card key={key}>
                            <CardHeader>
                                <CardTitle className="text-base">{label}</CardTitle>
                            </CardHeader>
                            <CardContent className="flex gap-2">
                                <a
                                    href={ReportController.show.url(key)}
                                    download
                                    className="inline-flex"
                                >
                                    <Button variant="outline" size="sm">
                                        <FileDown className="mr-1 size-4" />
                                        PDF
                                    </Button>
                                </a>
                                <a
                                    href={`${ReportController.show.url(key)}?format=excel`}
                                    download
                                    className="inline-flex"
                                >
                                    <Button variant="outline" size="sm">
                                        <FileSpreadsheet className="mr-1 size-4" />
                                        Excel
                                    </Button>
                                </a>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            </div>
        </>
    );
}

CoordinatorReports.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Reports', href: ReportController.index() },
    ],
};
