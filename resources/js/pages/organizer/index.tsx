import { Head } from '@inertiajs/react';
import { dashboard } from '@/routes/organizer';
import type { BreadcrumbItem } from '@/types';

export default function OrganizerDashboard() {
    return (
        <>
            <Head title="Organizer Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border">
                    <h1 className="text-lg font-semibold">Welcome, Organizer</h1>
                    <p className="text-muted-foreground text-sm">
                        This is your dashboard. Your competitions will appear here.
                    </p>
                </div>
            </div>
        </>
    );
}

OrganizerDashboard.layout = {
    breadcrumbs: [{ title: 'Organizer Dashboard', href: dashboard() }] as BreadcrumbItem[],
};
