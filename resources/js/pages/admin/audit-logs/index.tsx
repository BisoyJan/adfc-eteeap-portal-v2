import { Head, Link, router } from '@inertiajs/react';
import AuditLogController from '@/actions/App/Http/Controllers/Admin/AuditLogController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';

type Actor = { id: number; name: string; email: string };

type AuditLog = {
    id: number;
    user_id: number | null;
    action: string;
    target_type: string;
    target_id: number | null;
    ip_address: string | null;
    user_agent: string | null;
    created_at: string;
    actor: Actor | null;
};

type PageProps = {
    logs: {
        data: AuditLog[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
    filters: {
        date_from?: string;
        date_to?: string;
        actor?: string;
        action?: string;
        target_type?: string;
    };
    actionTypes: string[];
    targetTypes: string[];
};

function applyFilter(key: string, value: string) {
    const params = new URLSearchParams(window.location.search);
    if (value) {
        params.set(key, value);
    } else {
        params.delete(key);
    }
    params.delete('page');
    router.get(AuditLogController.index().url + '?' + params.toString(), {}, { preserveState: true });
}

export default function AuditLogIndex({ logs, filters, actionTypes, targetTypes }: PageProps) {
    return (
        <>
            <Head title="Audit Logs" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Audit Logs" description="Track all system activity" />

                <Card>
                    <CardHeader><CardTitle>Filters</CardTitle></CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-5 gap-4">
                            <div className="space-y-1">
                                <Label>Date From</Label>
                                <Input
                                    type="date"
                                    defaultValue={filters.date_from ?? ''}
                                    onChange={(e) => applyFilter('date_from', e.target.value)}
                                />
                            </div>
                            <div className="space-y-1">
                                <Label>Date To</Label>
                                <Input
                                    type="date"
                                    defaultValue={filters.date_to ?? ''}
                                    onChange={(e) => applyFilter('date_to', e.target.value)}
                                />
                            </div>
                            <div className="space-y-1">
                                <Label>Action</Label>
                                <Select
                                    value={filters.action ?? ''}
                                    onValueChange={(v) => applyFilter('action', v)}
                                >
                                    <SelectTrigger><SelectValue placeholder="All actions" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All actions</SelectItem>
                                        {actionTypes.map((a) => (
                                            <SelectItem key={a} value={a}>{a.replace(/_/g, ' ')}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-1">
                                <Label>Target Type</Label>
                                <Select
                                    value={filters.target_type ?? ''}
                                    onValueChange={(v) => applyFilter('target_type', v)}
                                >
                                    <SelectTrigger><SelectValue placeholder="All types" /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="">All types</SelectItem>
                                        {targetTypes.map((t) => (
                                            <SelectItem key={t} value={t}>{t.replace(/_/g, ' ')}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-1">
                                <Label>&nbsp;</Label>
                                <Button
                                    variant="outline"
                                    className="w-full"
                                    onClick={() => router.get(AuditLogController.index().url, {}, { preserveState: true })}
                                >
                                    Clear
                                </Button>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardContent className="pt-6">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Timestamp</TableHead>
                                    <TableHead>Actor</TableHead>
                                    <TableHead>Action</TableHead>
                                    <TableHead>Target</TableHead>
                                    <TableHead>IP Address</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {logs.data.map((log) => (
                                    <TableRow key={log.id}>
                                        <TableCell className="text-sm whitespace-nowrap">{log.created_at}</TableCell>
                                        <TableCell className="text-sm">{log.actor?.name ?? 'System'}</TableCell>
                                        <TableCell>
                                            <Badge variant="secondary">{log.action.replace(/_/g, ' ')}</Badge>
                                        </TableCell>
                                        <TableCell className="text-sm">
                                            {log.target_type.replace(/_/g, ' ')}
                                            {log.target_id ? ` #${log.target_id}` : ''}
                                        </TableCell>
                                        <TableCell className="text-sm font-mono">{log.ip_address}</TableCell>
                                        <TableCell>
                                            <Link
                                                href={AuditLogController.show(log.id).url}
                                                className="text-sm text-blue-600 hover:underline"
                                            >
                                                View
                                            </Link>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {logs.data.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={6} className="text-center text-muted-foreground py-8">
                                            No audit logs found.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        {logs.last_page > 1 && (
                            <div className="flex justify-center gap-2 mt-6">
                                {logs.links.map((link, i) => (
                                    <Link
                                        key={i}
                                        href={link.url ?? '#'}
                                        className={`px-3 py-1 rounded text-sm border ${link.active ? 'bg-primary text-primary-foreground border-primary' : 'hover:bg-accent'} ${!link.url ? 'pointer-events-none opacity-50' : ''}`}
                                        preserveState
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

AuditLogIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Audit Logs', href: AuditLogController.index() },
    ],
};
