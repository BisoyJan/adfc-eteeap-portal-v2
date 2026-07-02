import type { LucideIcon } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';

type StatsCardProps = {
    title: string;
    value: string | number;
    icon?: LucideIcon;
    description?: string;
    trend?: { value: string; positive: boolean };
};

export function StatsCard({ title, value, icon: Icon, description, trend }: StatsCardProps) {
    return (
        <Card>
            <CardContent className="p-6">
                <div className="flex items-start justify-between">
                    <div className="space-y-1">
                        <p className="text-sm font-medium text-muted-foreground">{title}</p>
                        <p className="text-3xl font-bold tracking-tight">{value}</p>
                        {description && (
                            <p className="text-xs text-muted-foreground">{description}</p>
                        )}
                        {trend && (
                            <p className={`text-xs ${trend.positive ? 'text-green-600' : 'text-red-600'}`}>
                                {trend.value}
                            </p>
                        )}
                    </div>
                    {Icon && (
                        <div className="rounded-lg bg-primary/10 p-2 text-primary">
                            <Icon className="size-5" />
                        </div>
                    )}
                </div>
            </CardContent>
        </Card>
    );
}
