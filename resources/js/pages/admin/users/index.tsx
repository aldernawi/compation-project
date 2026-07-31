import { Head, Link, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { activate, create, destroy, edit, index, suspend } from '@/routes/admin/users';
import { translateUserRole } from '@/lib/translations';
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
            <Head title="المستخدمون" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">المستخدمون</h1>
                    <Button asChild>
                        <Link href={create()}>مستخدم جديد</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'الاسم', cell: (user: AdminUser) => user.name },
                        { header: 'البريد الإلكتروني', cell: (user: AdminUser) => user.email },
                        {
                            header: 'الدور',
                            cell: (user: AdminUser) => <Badge variant="secondary">{translateUserRole(user.role)}</Badge>,
                        },
                        {
                            header: 'الحالة',
                            cell: (user: AdminUser) =>
                                user.suspended_at ? <Badge variant="destructive">موقوف</Badge> : <Badge>نشط</Badge>,
                        },
                        {
                            header: 'إجراءات',
                            cell: (user: AdminUser) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ user: user.id })} className="text-sm underline">
                                        تعديل
                                    </Link>
                                    {user.suspended_at ? (
                                        <button
                                            type="button"
                                            className="text-sm underline"
                                            onClick={() => router.patch(activate.url({ user: user.id }))}
                                        >
                                            تفعيل
                                        </button>
                                    ) : (
                                        <button
                                            type="button"
                                            className="text-sm underline"
                                            onClick={() => router.patch(suspend.url({ user: user.id }))}
                                        >
                                            إيقاف
                                        </button>
                                    )}
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('حذف هذا المستخدم؟')) {
                                                router.delete(destroy.url({ user: user.id }));
                                            }
                                        }}
                                    >
                                        حذف
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
        { title: 'لوحة تحكم المدير', href: dashboard() },
        { title: 'المستخدمون', href: index() },
    ] as BreadcrumbItem[],
};
