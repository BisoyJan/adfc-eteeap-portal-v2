import { Bar, BarChart, ResponsiveContainer, Tooltip, XAxis, YAxis } from 'recharts';

type DataPoint = {
    name: string;
    value: number;
    fill?: string;
};

type PipelineChartProps = {
    data: DataPoint[];
    title?: string;
};

const DEFAULT_COLORS = [
    'hsl(var(--chart-1))',
    'hsl(var(--chart-2))',
    'hsl(var(--chart-3))',
    'hsl(var(--chart-4))',
    'hsl(var(--chart-5))',
];

export function PipelineChart({ data, title }: PipelineChartProps) {
    const chartData = data.map((d, i) => ({
        ...d,
        fill: d.fill ?? DEFAULT_COLORS[i % DEFAULT_COLORS.length],
    }));

    return (
        <div className="space-y-3">
            {title && <p className="text-sm font-medium text-muted-foreground">{title}</p>}
            <ResponsiveContainer width="100%" height={250}>
                <BarChart data={chartData} margin={{ top: 5, right: 5, bottom: 5, left: -15 }}>
                    <XAxis
                        dataKey="name"
                        tick={{ fontSize: 12 }}
                        axisLine={false}
                        tickLine={false}
                    />
                    <YAxis
                        allowDecimals={false}
                        tick={{ fontSize: 12 }}
                        axisLine={false}
                        tickLine={false}
                    />
                    <Tooltip
                        contentStyle={{
                            background: 'hsl(var(--popover))',
                            border: '1px solid hsl(var(--border))',
                            borderRadius: '6px',
                            fontSize: '13px',
                        }}
                    />
                    <Bar dataKey="value" radius={[4, 4, 0, 0]} />
                </BarChart>
            </ResponsiveContainer>
        </div>
    );
}
