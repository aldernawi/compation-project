import { Form, Head } from '@inertiajs/react';
import UserController from '@/actions/App/Http/Controllers/Admin/UserController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/users';
import type { BreadcrumbItem } from '@/types';

type EditableUser = {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'organizer' | 'judge' | 'participant';
    can_manage_judges: boolean;
};

export default function EditUser({ user }: { user: EditableUser }) {
    return (
        <>
            <Head title="Edit User" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Edit User</h1>

                <Form {...UserController.update.form({ user: user.id })} className="max-w-md space-y-6">
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input id="name" name="name" defaultValue={user.name} required autoComplete="name" />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="email">Email</Label>
                                <Input id="email" type="email" name="email" defaultValue={user.email} required autoComplete="email" />
                                <InputError message={errors.email} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="role">Role</Label>
                                <select
                                    id="role"
                                    name="role"
                                    required
                                    defaultValue={user.role}
                                    className="border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none md:text-sm"
                                >
                                    <option value="admin">Admin</option>
                                    <option value="organizer">Organizer</option>
                                    <option value="judge">Judge</option>
                                    <option value="participant">Participant</option>
                                </select>
                                <InputError message={errors.role} />
                            </div>

                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="can_manage_judges"
                                    name="can_manage_judges"
                                    defaultChecked={user.can_manage_judges}
                                    value="1"
                                />
                                <Label htmlFor="can_manage_judges">Can manage judges (for organizers)</Label>
                            </div>

                            <div className="flex gap-2">
                                <Button type="submit" disabled={processing}>
                                    Save
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

EditUser.layout = {
    breadcrumbs: [
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Users', href: index() },
        { title: 'Edit', href: '#' },
    ] as BreadcrumbItem[],
};
