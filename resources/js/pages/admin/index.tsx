import { Head } from '@inertiajs/react';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';

export default function AdminDashboard() {
    return (
        <>
            <Head title="لوحة تحكم المدير" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">مرحباً أيها المدير</h1>
                    <p className="text-muted-foreground text-sm">
                        هذه لوحة التحكم الخاصة بك. ستظهر هنا إدارة الموارد.
                    </p>
                </div>
            </div>
        </>
    );
}

AdminDashboard.layout = {
    breadcrumbs: [{ title: 'لوحة تحكم المدير', href: dashboard() }] as BreadcrumbItem[],
};
