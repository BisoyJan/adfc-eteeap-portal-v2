import { Head, useForm } from '@inertiajs/react';
import PortfolioController from '@/actions/App/Http/Controllers/Applicant/PortfolioController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { DocumentProgress, Portfolio } from '@/types';

type PageProps = {
    portfolio: Portfolio;
    progress: DocumentProgress;
};

export default function PortfolioShow({ portfolio, progress }: PageProps) {
    const form = useForm({});
    const canSubmit = progress.verified_required >= progress.total_required && portfolio.status !== 'submitted';

    function submit() {
        form.post(PortfolioController.submit.url(), { preserveScroll: true });
    }

    return (
        <>
            <Head title="Portfolio" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="E-Portfolio" description={`Status: ${portfolio.status}`} />

                <InputError message={(form.errors as { portfolio?: string }).portfolio} />

                <Card>
                    <CardContent className="space-y-3">
                        <p className="text-sm">
                            {progress.verified_required} of {progress.total_required} required documents verified.
                        </p>

                        {(portfolio.documents ?? []).map((document) => (
                            <div key={document.id} className="flex items-center justify-between rounded-lg border p-3">
                                <span>{document.file_name}</span>
                                <Badge>{document.status}</Badge>
                            </div>
                        ))}

                        <Button disabled={!canSubmit || form.processing} onClick={submit}>
                            Submit portfolio
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

PortfolioShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Portfolio', href: PortfolioController.show() },
    ],
};
