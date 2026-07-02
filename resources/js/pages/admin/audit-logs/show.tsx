import { Head } from '@inertiajs/react';
import AuditLogController from '@/actions/App/Http/Controllers/Admin/AuditLogController';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';

type PageProps = {
    log: {
        id: number;
        user_id: number | null;
        action: string;
        target_type: string;
        target_id: number | null;
        old_values: Record<string, unknown> | null;
        new_values: Record<string, unknown> | null;
        ip_address: string | null;
        user_agent: string | null;
        created_at: string;
        actor: { id: number; name: string; email: string } | null;
    };
};

export default function AuditLogShow({ log }: PageProps) {
    const oldKeys = log.old_values ? Object.keys(log.old_values) : [];
    const newKeys = log.new_values ? Object.keys(log.new_values) : [];
    const allKeys = [...new Set([...oldKeys, ...newKeys])];

    return (
        <>
            <Head title="Audit Log Detail" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Audit Log Detail" />

                <div className="grid grid-cols-2 gap-4">
                    <Card>
                        <CardHeader><CardTitle>Overview</CardTitle></CardHeader>
                        <CardContent className="space-y-3">
                            <div>
                                <span className="text-sm text-muted-foreground">Action</span>
                                <p><Badge variant="secondary">{log.action.replace(/_/g, ' ')}</Badge></p>
                            </div>
                            <div>
                                <span className="text-sm text-muted-foreground">Target Type</span>
                                <p className="font-medium">{log.target_type.replace(/_/g, ' ')}</p>
                            </div>
                            {log.target_id && (
                                <div>
                                    <span className="text-sm text-muted-foreground">Target ID</span>
                                    <p className="font-medium">#{log.target_id}</p>
                                </div>
                            )}
                            <div>
                                <span className="text-sm text-muted-foreground">Actor</span>
                                <p className="font-medium">{log.actor ? `${log.actor.name} (${log.actor.email})` : 'System'}</p>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader><CardTitle>Metadata</CardTitle></CardHeader>
                        <CardContent className="space-y-3">
                            <div>
                                <span className="text-sm text-muted-foreground">IP Address</span>
                                <p className="font-mono text-sm">{log.ip_address ?? 'N/A'}</p>
                            </div>
                            <div>
                                <span className="text-sm text-muted-foreground">User Agent</span>
                                <p className="text-sm break-words">{log.user_agent ?? 'N/A'}</p>
                            </div>
                            <div>
                                <span className="text-sm text-muted-foreground">Timestamp</span>
                                <p className="font-medium">{log.created_at}</p>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                {(log.old_values || log.new_values) && (
                    <Card>
                        <CardHeader><CardTitle>Changes</CardTitle></CardHeader>
                        <CardContent>
                            <div className="grid grid-cols-2 gap-6">
                                <div>
                                    <h4 className="text-sm font-semibold text-destructive mb-2">Old Values</h4>
                                    {log.old_values ? (
                                        <pre className="bg-muted p-4 rounded-lg text-xs overflow-auto max-h-96 whitespace-pre-wrap">
                                            {JSON.stringify(log.old_values, null, 2)}
                                        </pre>
                                    ) : (
                                        <p className="text-sm text-muted-foreground italic">No old values recorded</p>
                                    )}
                                </div>
                                <div>
                                    <h4 className="text-sm font-semibold text-green-600 mb-2">New Values</h4>
                                    {log.new_values ? (
                                        <pre className="bg-muted p-4 rounded-lg text-xs overflow-auto max-h-96 whitespace-pre-wrap">
                                            {JSON.stringify(log.new_values, null, 2)}
                                        </pre>
                                    ) : (
                                        <p className="text-sm text-muted-foreground italic">No new values recorded</p>
                                    )}
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                )}
            </div>
        </>
    );
}

AuditLogShow.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Audit Logs', href: AuditLogController.index() },
        { title: 'Detail', href: '' },
    ],
};
