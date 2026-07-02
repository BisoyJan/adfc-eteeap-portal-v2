import { Head, router, useForm } from '@inertiajs/react';
import type { ChangeEvent } from 'react';
import DocumentController from '@/actions/App/Http/Controllers/Applicant/DocumentController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { dashboard } from '@/routes';
import type { DocumentChecklistItem, DocumentProgress } from '@/types';

type PageProps = {
    checklist: DocumentChecklistItem[];
    progress: DocumentProgress;
};

function statusVariant(status: DocumentChecklistItem['status']) {
    if (status === 'verified') {
return 'default' as const;
}

    if (status === 'rejected') {
return 'destructive' as const;
}

    if (status === 'uploaded') {
return 'secondary' as const;
}

    return 'outline' as const;
}

function ChecklistRow({ item }: { item: DocumentChecklistItem }) {
    const form = useForm<{ document_type: string; file: File | null }>({
        document_type: item.key,
        file: null,
    });

    function handleFileChange(event: ChangeEvent<HTMLInputElement>) {
        form.setData('file', event.target.files?.[0] ?? null);
    }

    function upload() {
        form.post(DocumentController.store.url(), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => form.reset('file'),
        });
    }

    function remove() {
        if (!item.document) {
return;
}

        router.delete(DocumentController.destroy.url(item.document.id), { preserveScroll: true });
    }

    return (
        <div className="flex flex-col gap-3 rounded-lg border p-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p className="font-medium">
                    {item.label} {item.required && <span className="text-destructive">*</span>}
                </p>
                {item.document && <p className="text-muted-foreground text-sm">{item.document.file_name} (v{item.document.version})</p>}
                <InputError message={form.errors.file} />
            </div>

            <div className="flex items-center gap-2">
                <Badge variant={statusVariant(item.status)}>{item.status ?? 'missing'}</Badge>
                <input type="file" accept=".pdf,.jpg,.jpeg,.png" onChange={handleFileChange} className="text-sm" />
                <Button type="button" size="sm" disabled={!form.data.file || form.processing} onClick={upload}>
                    Upload
                </Button>
                {item.document && item.status !== 'verified' && (
                    <Button type="button" size="sm" variant="outline" onClick={remove}>
                        Remove
                    </Button>
                )}
            </div>
        </div>
    );
}

export default function DocumentsIndex({ checklist, progress }: PageProps) {
    return (
        <>
            <Head title="Documents" />

            <div className="flex flex-col gap-6 p-4">
                <Heading
                    title="Document Checklist"
                    description={`${progress.verified_required} of ${progress.total_required} required documents verified`}
                />

                <Card>
                    <CardContent className="space-y-3">
                        {checklist.map((item) => (
                            <ChecklistRow key={item.key} item={item} />
                        ))}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

DocumentsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Documents', href: DocumentController.index() },
    ],
};
