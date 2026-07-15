import { Head, Link, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { create, destroy, edit, index } from '@/routes/admin/competitions';
import { index as prizesIndex } from '@/routes/admin/competitions/prizes';
import { show as resultsShow } from '@/routes/admin/competitions/results';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type AdminCompetition = {
    id: number;
    title: string;
    status: 'upcoming' | 'open' | 'closed' | 'under_evaluation' | 'finished';
    organizer: { id: number; name: string } | null;
    competition_type: { id: number; name: string } | null;
};

export default function CompetitionsIndex({ competitions }: { competitions: LaravelPaginator<AdminCompetition> }) {
    return (
        <>
            <Head title="Competitions" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">Competitions</h1>
                    <Button asChild>
                        <Link href={create()}>New Competition</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'Title', cell: (c: AdminCompetition) => c.title },
                        { header: 'Organizer', cell: (c: AdminCompetition) => c.organizer?.name ?? '—' },
                        { header: 'Type', cell: (c: AdminCompetition) => c.competition_type?.name ?? '—' },
                        {
                            header: 'Status',
                            cell: (c: AdminCompetition) => <Badge variant="secondary">{c.status}</Badge>,
                        },
                        {
                            header: 'Actions',
                            cell: (c: AdminCompetition) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ competition: c.id })} className="text-sm underline">
                                        Edit
                                    </Link>
                                    <Link href={prizesIndex({ competition: c.id })} className="text-sm underline">
                                        Prizes
                                    </Link>
                                    <Link href={resultsShow({ competition: c.id })} className="text-sm underline">
                                        Results
                                    </Link>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('Delete this competition?')) {
                                                router.delete(destroy.url({ competition: c.id }));
                                            }
                                        }}
                                    >
                                        Delete
                                    </button>
                                </div>
                            ),
                        },
                    ]}
                    paginator={competitions}
                />
            </div>
        </>
    );
}

CompetitionsIndex.layout = {
    breadcrumbs: [
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Competitions', href: index() },
    ] as BreadcrumbItem[],
};
