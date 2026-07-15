import { Head, Link, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { activate, create, destroy, edit, index, suspend } from '@/routes/admin/users';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type AdminUser = {
    id: number;
    name: string;
    email: string;
    role: 'admin' | 'organizer' | 'judge' | 'participant';
    suspended_at: string | null;
};

export default function UsersIndex({ users }: { users: LaravelPaginator<AdminUser> }) {
    return (
        <>
            <Head title="Users" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">Users</h1>
                    <Button asChild>
                        <Link href={create()}>New User</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'Name', cell: (user: AdminUser) => user.name },
                        { header: 'Email', cell: (user: AdminUser) => user.email },
                        {
                            header: 'Role',
                            cell: (user: AdminUser) => <Badge variant="secondary">{user.role}</Badge>,
                        },
                        {
                            header: 'Status',
                            cell: (user: AdminUser) =>
                                user.suspended_at ? <Badge variant="destructive">Suspended</Badge> : <Badge>Active</Badge>,
                        },
                        {
                            header: 'Actions',
                            cell: (user: AdminUser) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ user: user.id })} className="text-sm underline">
                                        Edit
                                    </Link>
                                    {user.suspended_at ? (
                                        <button
                                            type="button"
                                            className="text-sm underline"
                                            onClick={() => router.patch(activate.url({ user: user.id }))}
                                        >
                                            Activate
                                        </button>
                                    ) : (
                                        <button
                                            type="button"
                                            className="text-sm underline"
                                            onClick={() => router.patch(suspend.url({ user: user.id }))}
                                        >
                                            Suspend
                                        </button>
                                    )}
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('Delete this user?')) {
                                                router.delete(destroy.url({ user: user.id }));
                                            }
                                        }}
                                    >
                                        Delete
                                    </button>
                                </div>
                            ),
                        },
                    ]}
                    paginator={users}
                />
            </div>
        </>
    );
}

UsersIndex.layout = {
    breadcrumbs: [
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Users', href: index() },
    ] as BreadcrumbItem[],
};
