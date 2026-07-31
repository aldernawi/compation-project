import { Head } from '@inertiajs/react';
import { dashboard } from '@/routes/organizer';
import type { BreadcrumbItem } from '@/types';

export default function OrganizerDashboard() {
    return (
        <>
            <Head title="لوحة تحكم المنظم" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">مرحباً أيها المنظم</h1>
                    <p className="text-muted-foreground text-sm">
                        هذه لوحة التحكم الخاصة بك. ستظهر هنا مسابقاتك.
                    </p>
                </div>
            </div>
        </>
    );
}

OrganizerDashboard.layout = {
    breadcrumbs: [{ title: 'لوحة تحكم المنظم', href: dashboard() }] as BreadcrumbItem[],
};
