import { Head, Link, router } from '@inertiajs/react';
import { Bell } from 'lucide-react';
import NotificationController from '@/actions/App/Http/Controllers/NotificationController';
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
import type { AppNotification } from '@/types';

type PageProps = {
    notifications: {
        data: AppNotification[];
        current_page: number;
        last_page: number;
        total: number;
        from: number;
        to: number;
        links: { url: string | null; label: string; active: boolean }[];
    };
};

export default function NotificationsIndex({ notifications: data }: PageProps) {
    const handleMarkRead = (id: number) => {
        router.put(NotificationController.markRead.url(id));
    };

    const handleMarkAllRead = () => {
        router.put(NotificationController.markAllRead.url());
    };

    const unreadCount = data.data.filter((n) => !n.read_at).length;

    return (
        <>
            <Head title="Notifications" />

            <div className="flex flex-col gap-6 p-4">
                <div className="flex items-center justify-between">
                    <Heading title="Notifications" description="View and manage your notifications" />
                    {unreadCount > 0 && (
                        <Button variant="outline" size="sm" onClick={handleMarkAllRead}>
                            Mark all read
                        </Button>
                    )}
                </div>

                <Card>
                    <CardHeader>
                        <CardTitle className="flex items-center gap-2">
                            <Bell className="size-5" />
                            All Notifications
                            {unreadCount > 0 && (
                                <Badge variant="destructive">{unreadCount} unread</Badge>
                            )}
                        </CardTitle>
                    </CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead className="w-8" />
                                    <TableHead>Title</TableHead>
                                    <TableHead>Message</TableHead>
                                    <TableHead>Date</TableHead>
                                    <TableHead />
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {data.data.map((n) => (
                                    <TableRow key={n.id} className={n.read_at ? '' : 'bg-muted/30'}>
                                        <TableCell>
                                            <span
                                                className={`block size-2 rounded-full ${n.read_at ? 'bg-transparent' : 'bg-primary'}`}
                                            />
                                        </TableCell>
                                        <TableCell className="font-medium">{n.title}</TableCell>
                                        <TableCell className="max-w-xs truncate text-muted-foreground">
                                            {n.body ?? '—'}
                                        </TableCell>
                                        <TableCell className="whitespace-nowrap text-sm text-muted-foreground">
                                            {n.created_at}
                                        </TableCell>
                                        <TableCell className="text-right">
                                            {!n.read_at && (
                                                <Button
                                                    variant="ghost"
                                                    size="sm"
                                                    onClick={() => handleMarkRead(n.id)}
                                                >
                                                    Mark read
                                                </Button>
                                            )}
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {data.data.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={5} className="py-8 text-center text-muted-foreground">
                                            No notifications yet.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>

                        {data.last_page > 1 && (
                            <div className="mt-4 flex items-center justify-center gap-2">
                                {data.links.map((link, i) => (
                                    link.url ? (
                                        <Link
                                            key={i}
                                            href={link.url}
                                            className={`inline-flex items-center justify-center rounded-md px-3 py-1.5 text-sm ${
                                                link.active
                                                    ? 'bg-primary text-primary-foreground'
                                                    : 'text-muted-foreground hover:bg-accent'
                                            }`}
                                            preserveState
                                        >
                                            {link.label}
                                        </Link>
                                    ) : (
                                        <span key={i} className="px-3 py-1.5 text-sm text-muted-foreground">
                                            {link.label}
                                        </span>
                                    )
                                ))}
                            </div>
                        )}
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

NotificationsIndex.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Notifications', href: NotificationController.index() },
    ],
};
