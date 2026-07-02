import { router } from '@inertiajs/react';
import { useState } from 'react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import type { CompetencyLevel, EquivalencyMatrixItemRecord, RecommendedAction } from '@/types';

type Row = {
    subject_code: string;
    subject_title: string;
    units: number;
    competency_level: CompetencyLevel;
    evidence_source: string;
    equivalent_credits: number;
    recommended_action: RecommendedAction;
    notes: string;
};

type Props = {
    actionUrl: string;
    initialItems: EquivalencyMatrixItemRecord[];
    disabled?: boolean;
};

function toRow(item?: EquivalencyMatrixItemRecord): Row {
    return {
        subject_code: item?.subject_code ?? '',
        subject_title: item?.subject_title ?? '',
        units: item?.units ?? 3,
        competency_level: item?.competency_level ?? 'none',
        evidence_source: item?.evidence_source ?? '',
        equivalent_credits: item?.equivalent_credits ?? 0,
        recommended_action: item?.recommended_action ?? 'enroll',
        notes: item?.notes ?? '',
    };
}

export default function EquivalencyMatrixEditor({ actionUrl, initialItems, disabled }: Props) {
    const [rows, setRows] = useState<Row[]>(initialItems.length > 0 ? initialItems.map(toRow) : [toRow()]);
    const [processing, setProcessing] = useState(false);

    function updateRow(index: number, field: keyof Row, value: string | number) {
        setRows(rows.map((row, i) => (i === index ? { ...row, [field]: value } : row)));
    }

    function addRow() {
        setRows([...rows, toRow()]);
    }

    function removeRow(index: number) {
        setRows(rows.filter((_, i) => i !== index));
    }

    function save() {
        setProcessing(true);
        router.put(
            actionUrl,
            { items: rows },
            {
                preserveScroll: true,
                onFinish: () => setProcessing(false),
            },
        );
    }

    return (
        <div className="space-y-4">
            {rows.map((row, index) => (
                <div key={index} className="grid gap-2 rounded-lg border p-3 sm:grid-cols-4">
                    <div>
                        <Label>Subject code</Label>
                        <Input disabled={disabled} value={row.subject_code} onChange={(e) => updateRow(index, 'subject_code', e.target.value)} />
                    </div>
                    <div className="sm:col-span-2">
                        <Label>Subject title</Label>
                        <Input disabled={disabled} value={row.subject_title} onChange={(e) => updateRow(index, 'subject_title', e.target.value)} />
                    </div>
                    <div>
                        <Label>Units</Label>
                        <Input
                            disabled={disabled}
                            type="number"
                            value={row.units}
                            onChange={(e) => updateRow(index, 'units', Number(e.target.value))}
                        />
                    </div>
                    <div>
                        <Label>Competency</Label>
                        <select
                            disabled={disabled}
                            className="border-input h-9 w-full rounded-md border bg-transparent px-3 text-sm"
                            value={row.competency_level}
                            onChange={(e) => updateRow(index, 'competency_level', e.target.value)}
                        >
                            <option value="full">Full</option>
                            <option value="partial">Partial</option>
                            <option value="none">None</option>
                        </select>
                    </div>
                    <div>
                        <Label>Equivalent credits</Label>
                        <Input
                            disabled={disabled}
                            type="number"
                            step="0.5"
                            value={row.equivalent_credits}
                            onChange={(e) => updateRow(index, 'equivalent_credits', Number(e.target.value))}
                        />
                    </div>
                    <div>
                        <Label>Recommended action</Label>
                        <select
                            disabled={disabled}
                            className="border-input h-9 w-full rounded-md border bg-transparent px-3 text-sm"
                            value={row.recommended_action}
                            onChange={(e) => updateRow(index, 'recommended_action', e.target.value)}
                        >
                            <option value="credit">Credit</option>
                            <option value="enhance">Enhance</option>
                            <option value="enroll">Enroll</option>
                        </select>
                    </div>
                    <div className="sm:col-span-4">
                        <Label>Evidence source</Label>
                        <Textarea
                            disabled={disabled}
                            value={row.evidence_source}
                            onChange={(e) => updateRow(index, 'evidence_source', e.target.value)}
                        />
                    </div>
                    <div className="sm:col-span-4">
                        <Label>Notes</Label>
                        <Textarea disabled={disabled} value={row.notes} onChange={(e) => updateRow(index, 'notes', e.target.value)} />
                    </div>
                    {!disabled && (
                        <div className="sm:col-span-4">
                            <Button type="button" variant="outline" size="sm" onClick={() => removeRow(index)}>
                                Remove subject
                            </Button>
                        </div>
                    )}
                </div>
            ))}

            {!disabled && (
                <div className="flex gap-2">
                    <Button type="button" variant="outline" onClick={addRow}>
                        Add subject
                    </Button>
                    <Button type="button" onClick={save} disabled={processing}>
                        Save matrix
                    </Button>
                </div>
            )}
        </div>
    );
}
