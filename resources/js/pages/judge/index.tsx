import { Head } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { dashboard } from '@/routes/judge';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [{ title: 'Judge Dashboard', href: dashboard() }];

export default function JudgeDashboard() {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Judge Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">Welcome, Judge</h1>
                    <p className="text-muted-foreground text-sm">
                        This is your dashboard. Competitions assigned to you will appear here.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
