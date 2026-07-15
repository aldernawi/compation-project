import { Head } from '@inertiajs/react';
import { dashboard } from '@/routes/admin';
import type { BreadcrumbItem } from '@/types';

export default function AdminDashboard() {
    return (
        <>
            <Head title="Admin Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">Welcome, Admin</h1>
                    <p className="text-muted-foreground text-sm">
                        This is your dashboard. Resource management will appear here.
                    </p>
                </div>
            </div>
        </>
    );
}

AdminDashboard.layout = {
    breadcrumbs: [{ title: 'Admin Dashboard', href: dashboard() }] as BreadcrumbItem[],
};
