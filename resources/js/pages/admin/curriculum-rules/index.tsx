import { Head, useForm } from '@inertiajs/react';
import EquivalencyRuleController from '@/actions/App/Http/Controllers/Admin/EquivalencyRuleController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
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

type Rule = {
    id: number;
    rule_type: string;
    condition_key: string;
    condition_value: string;
    curriculum_subject: { id: number; code: string; title: string } | null;
    equivalent_credits: number;
    priority: number;
    is_active: boolean;
};

type PageProps = {
    rules: Rule[];
    subjects: { id: number; code: string; title: string }[];
};

export default function EquivalencyRuleBuilder({ rules, subjects }: PageProps) {
    const form = useForm({
        rule_type: 'direct',
        condition_key: '',
        condition_value: '',
        curriculum_subject_id: '',
        equivalent_credits: '',
        priority: '0',
        is_active: true,
    });

    function submit() {
        form.post(EquivalencyRuleController.store().url, { preserveScroll: true, onSuccess: () => form.reset() });
    }

    return (
        <>
            <Head title="Equivalency Rules" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="Equivalency Rules Engine" description="Configure auto-matching rules for subject equivalency" />

                <Card>
                    <CardHeader><CardTitle>Add Rule</CardTitle></CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label>Rule Type</Label>
                                <Select value={form.data.rule_type} onValueChange={(v) => form.setData('rule_type', v)}>
                                    <SelectTrigger><SelectValue /></SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="direct">Direct Mapping</SelectItem>
                                        <SelectItem value="experience">Experience-Based</SelectItem>
                                        <SelectItem value="certification">Certification-Based</SelectItem>
                                        <SelectItem value="training">Training-Based</SelectItem>
                                        <SelectItem value="prior_education">Prior Education</SelectItem>
                                    </SelectContent>
                                </Select>
                            </div>
                            <div className="space-y-1">
                                <Label>Target Subject</Label>
                                <Select value={form.data.curriculum_subject_id} onValueChange={(v) => form.setData('curriculum_subject_id', v)}>
                                    <SelectTrigger><SelectValue placeholder="Select subject" /></SelectTrigger>
                                    <SelectContent>
                                        {subjects.map((s) => (
                                            <SelectItem key={s.id} value={String(s.id)}>{s.code} — {s.title}</SelectItem>
                                        ))}
                                    </SelectContent>
                                </Select>
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label>Condition Key</Label>
                                <Input value={form.data.condition_key} onChange={(e) => form.setData('condition_key', e.target.value)} placeholder="e.g. years_experience" />
                                <InputError message={form.errors.condition_key} />
                            </div>
                            <div className="space-y-1">
                                <Label>Condition Value</Label>
                                <Input value={form.data.condition_value} onChange={(e) => form.setData('condition_value', e.target.value)} placeholder="e.g. 5" />
                                <InputError message={form.errors.condition_value} />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label>Equivalent Credits</Label>
                                <Input type="number" step="0.5" value={form.data.equivalent_credits} onChange={(e) => form.setData('equivalent_credits', e.target.value)} />
                            </div>
                            <div className="space-y-1">
                                <Label>Priority</Label>
                                <Input type="number" value={form.data.priority} onChange={(e) => form.setData('priority', e.target.value)} />
                            </div>
                        </div>

                        <div className="flex justify-end">
                            <Button onClick={submit} disabled={form.processing}>
                                Add Rule
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>Existing Rules</CardTitle></CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Type</TableHead>
                                    <TableHead>Condition</TableHead>
                                    <TableHead>Subject</TableHead>
                                    <TableHead>Credits</TableHead>
                                    <TableHead>Priority</TableHead>
                                    <TableHead>Active</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {rules.map((r) => (
                                    <TableRow key={r.id}>
                                        <TableCell className="text-sm">{r.rule_type.replace(/_/g, ' ')}</TableCell>
                                        <TableCell className="text-sm">{r.condition_key}: {r.condition_value}</TableCell>
                                        <TableCell className="text-sm">{r.curriculum_subject?.code ?? '-'}</TableCell>
                                        <TableCell>{r.equivalent_credits}</TableCell>
                                        <TableCell>{r.priority}</TableCell>
                                        <TableCell>{r.is_active ? 'Yes' : 'No'}</TableCell>
                                    </TableRow>
                                ))}
                                {rules.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={6} className="text-center text-muted-foreground py-8">
                                            No rules configured yet.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

EquivalencyRuleBuilder.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'Equivalency Rules', href: EquivalencyRuleController.index() },
    ],
};
