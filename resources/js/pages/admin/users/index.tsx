import React from 'react';
import { Head, router, useForm } from '@inertiajs/react';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from '@/components/ui/table';
import { dashboard } from '@/routes';

type User = {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    suspended_at: string | null;
    suspended_reason: string | null;
    created_at: string;
    roles: { id: number; name: string; display_name: string }[];
};

type Role = {
    id: number;
    name: string;
    display_name: string;
};

type PageProps = {
    users: { data: User[] };
    roles: Role[];
    filters: { role?: string; is_active?: string };
};

export default function UserManagement({ users, roles, filters }: PageProps) {
    const form = useForm({
        name: '',
        email: '',
        password: '',
        roles: [] as string[],
    });

    function submit() {
        form.post(UserController.store().url, {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    }

    function toggleRole(roleName: string) {
        const current = form.data.roles;
        if (current.includes(roleName)) {
            form.setData('roles', current.filter((r) => r !== roleName));
        } else {
            form.setData('roles', [...current, roleName]);
        }
    }

    return (
        <>
            <Head title="User Management" />

            <div className="flex flex-col gap-6 p-4">
                <Heading title="User Management" description="Create and manage staff accounts" />

                <Card>
                    <CardHeader><CardTitle>Create Staff User</CardTitle></CardHeader>
                    <CardContent className="space-y-4">
                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label>Name</Label>
                                <Input
                                    value={form.data.name}
                                    onChange={(e) => form.setData('name', e.target.value)}
                                    placeholder="Full name"
                                />
                                <InputError message={form.errors.name} />
                            </div>
                            <div className="space-y-1">
                                <Label>Email</Label>
                                <Input
                                    value={form.data.email}
                                    onChange={(e) => form.setData('email', e.target.value)}
                                    placeholder="email@example.com"
                                />
                                <InputError message={form.errors.email} />
                            </div>
                        </div>

                        <div className="grid grid-cols-2 gap-4">
                            <div className="space-y-1">
                                <Label>Password</Label>
                                <Input
                                    type="password"
                                    value={form.data.password}
                                    onChange={(e) => form.setData('password', e.target.value)}
                                    placeholder="Min. 8 characters"
                                />
                                <InputError message={form.errors.password} />
                            </div>
                            <div className="space-y-1">
                                <Label>Roles</Label>
                                <div className="flex flex-wrap gap-3 rounded-md border p-3">
                                    {roles.map((role) => (
                                        <label
                                            key={role.id}
                                            className="flex items-center gap-2 text-sm cursor-pointer"
                                        >
                                            <Checkbox
                                                checked={form.data.roles.includes(role.name)}
                                                onCheckedChange={() => toggleRole(role.name)}
                                            />
                                            {role.display_name}
                                        </label>
                                    ))}
                                </div>
                                <InputError message={form.errors.roles} />
                            </div>
                        </div>

                        <div className="flex justify-end">
                            <Button onClick={submit} disabled={form.processing}>
                                Create User
                            </Button>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader><CardTitle>All Users</CardTitle></CardHeader>
                    <CardContent>
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Name</TableHead>
                                    <TableHead>Email</TableHead>
                                    <TableHead>Roles</TableHead>
                                    <TableHead>Status</TableHead>
                                    <TableHead>Created At</TableHead>
                                    <TableHead>Actions</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                {users.data.map((user) => (
                                    <TableRow key={user.id}>
                                        <TableCell className="text-sm font-medium">{user.name}</TableCell>
                                        <TableCell className="text-sm">{user.email}</TableCell>
                                        <TableCell>
                                            <div className="flex flex-wrap gap-1">
                                                {user.roles.map((role) => (
                                                    <Badge key={role.id} variant="secondary">
                                                        {role.display_name}
                                                    </Badge>
                                                ))}
                                            </div>
                                        </TableCell>
                                        <TableCell>
                                            {user.is_active ? (
                                                <Badge variant="default">Active</Badge>
                                            ) : (
                                                <Badge variant="destructive">Suspended</Badge>
                                            )}
                                        </TableCell>
                                        <TableCell className="text-sm text-muted-foreground">
                                            {new Date(user.created_at).toLocaleDateString()}
                                        </TableCell>
                                        <TableCell>
                                            <div className="flex items-center gap-2">
                                                <RoleDialog user={user} roles={roles} />
                                                {user.is_active ? (
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() => {
                                                            const reason = prompt('Suspension reason (optional):');
                                                            router.put(
                                                                UserController.suspend(user.id).url,
                                                                { reason },
                                                                { preserveScroll: true },
                                                            );
                                                        }}
                                                    >
                                                        Suspend
                                                    </Button>
                                                ) : (
                                                    <Button
                                                        variant="outline"
                                                        size="sm"
                                                        onClick={() =>
                                                            router.put(
                                                                UserController.unsuspend(user.id).url,
                                                                {},
                                                                { preserveScroll: true },
                                                            )
                                                        }
                                                    >
                                                        Unsuspend
                                                    </Button>
                                                )}
                                            </div>
                                        </TableCell>
                                    </TableRow>
                                ))}
                                {users.data.length === 0 && (
                                    <TableRow>
                                        <TableCell colSpan={6} className="text-center text-muted-foreground py-8">
                                            No users found.
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

function RoleDialog({ user, roles }: { user: User; roles: Role[] }) {
    const [open, setOpen] = React.useState(false);
    const [selected, setSelected] = React.useState<string[]>(
        user.roles.map((r) => r.name),
    );

    React.useEffect(() => {
        setSelected(user.roles.map((r) => r.name));
    }, [user, open]);

    function toggle(roleName: string) {
        setSelected((prev) =>
            prev.includes(roleName)
                ? prev.filter((r) => r !== roleName)
                : [...prev, roleName],
        );
    }

    function save() {
        router.put(
            UserController.updateRoles(user.id).url,
            { roles: selected },
            { preserveScroll: true, onSuccess: () => setOpen(false) },
        );
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="outline" size="sm">Roles</Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Update Roles — {user.name}</DialogTitle>
                </DialogHeader>
                <div className="flex flex-col gap-3 py-4">
                    {roles.map((role) => (
                        <label
                            key={role.id}
                            className="flex items-center gap-2 text-sm cursor-pointer"
                        >
                            <Checkbox
                                checked={selected.includes(role.name)}
                                onCheckedChange={() => toggle(role.name)}
                            />
                            {role.display_name}
                        </label>
                    ))}
                </div>
                <div className="flex justify-end">
                    <Button onClick={save}>Save</Button>
                </div>
            </DialogContent>
        </Dialog>
    );
}

UserManagement.layout = {
    breadcrumbs: [
        { title: 'Dashboard', href: dashboard() },
        { title: 'User Management', href: UserController.index() },
    ],
};
