import { Head } from '@inertiajs/react';
import { dashboard } from '@/routes/judge';
import type { BreadcrumbItem } from '@/types';

export default function JudgeDashboard() {
    return (
        <>
            <Head title="لوحة تحكم الحكم" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">مرحباً أيها الحَكَم</h1>
                    <p className="text-muted-foreground text-sm">
                        هذه لوحة التحكم الخاصة بك. ستظهر هنا المسابقات المعينة إليك.
                    </p>
                </div>
            </div>
        </>
    );
}

JudgeDashboard.layout = {
    breadcrumbs: [{ title: 'لوحة تحكم الحكم', href: dashboard() }] as BreadcrumbItem[],
};
