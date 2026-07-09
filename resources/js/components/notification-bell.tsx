import { Link, router, usePage } from '@inertiajs/react';
import { Bell } from 'lucide-react';
import NotificationController from '@/actions/App/Http/Controllers/NotificationController';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import type { AppNotification } from '@/types';

export function NotificationBell() {
    const { unreadNotificationsCount, recentNotifications } = usePage().props;

    const handleMarkRead = (id: number, e: React.MouseEvent) => {
        e.preventDefault();
        e.stopPropagation();
        router.put(NotificationController.markRead.url(id));
    };

    const handleMarkAllRead = () => {
        router.put(NotificationController.markAllRead.url());
    };

    return (
        <DropdownMenu>
            <DropdownMenuTrigger asChild>
                <Button variant="ghost" size="icon" className="relative">
                    <Bell className="size-5" />
                    {unreadNotificationsCount > 0 && (
                        <span className="absolute -top-0.5 -right-0.5 flex size-4 items-center justify-center rounded-full bg-destructive text-[10px] font-medium text-destructive-foreground">
                            {unreadNotificationsCount > 9 ? '9+' : unreadNotificationsCount}
                        </span>
                    )}
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end" className="w-80">
                <div className="flex items-center justify-between px-2 py-1.5">
                    <span className="text-sm font-medium">Notifications</span>
                    {unreadNotificationsCount > 0 && (
                        <button onClick={handleMarkAllRead} className="text-xs text-muted-foreground hover:text-foreground">
                            Mark all read
                        </button>
                    )}
                </div>
                <DropdownMenuSeparator />
                {!recentNotifications ? (
                    <div className="px-2 py-4 text-center text-sm text-muted-foreground">
                        <div className="mx-auto mb-2 size-4 animate-pulse rounded bg-muted" />
                        Loading...
                    </div>
                ) : recentNotifications.length === 0 ? (
                    <div className="px-2 py-4 text-center text-sm text-muted-foreground">
                        No notifications yet.
                    </div>
                ) : (
                    recentNotifications.map((n: AppNotification) => (
                        <DropdownMenuItem key={n.id} className="flex flex-col items-start gap-0.5 py-2">
                            <div className="flex w-full items-start justify-between gap-2">
                                <span className={`text-sm ${n.read_at ? '' : 'font-semibold'}`}>
                                    {n.title}
                                </span>
                                {!n.read_at && (
                                    <button
                                        onClick={(e) => handleMarkRead(n.id, e)}
                                        className="shrink-0 text-[10px] text-muted-foreground hover:text-foreground"
                                    >
                                        Read
                                    </button>
                                )}
                            </div>
                            {n.body && (
                                <span className="line-clamp-2 text-xs text-muted-foreground">
                                    {n.body}
                                </span>
                            )}
                        </DropdownMenuItem>
                    ))
                )}
                <DropdownMenuSeparator />
                <Link href={NotificationController.index()}>
                    <div className="px-2 py-1.5 text-center text-sm font-medium hover:bg-accent cursor-pointer rounded-b-md">
                        View all notifications
                    </div>
                </Link>
            </DropdownMenuContent>
        </DropdownMenu>
    );
}
