import { Head, Link, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { index as competitionsIndex } from '@/routes/admin/competitions';
import { create, destroy, edit } from '@/routes/admin/competitions/prizes';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type AdminPrize = {
    id: number;
    title: string;
    winners_count: number;
    rank: number;
};

export default function PrizesIndex({
    competition,
    prizes,
}: {
    competition: { id: number; title: string };
    prizes: LaravelPaginator<AdminPrize>;
}) {
    return (
        <>
            <Head title={`Prizes — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">Prizes for {competition.title}</h1>
                    <Button asChild>
                        <Link href={create({ competition: competition.id })}>New Prize</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'Rank', cell: (prize: AdminPrize) => prize.rank },
                        { header: 'Title', cell: (prize: AdminPrize) => prize.title },
                        { header: 'Winners', cell: (prize: AdminPrize) => prize.winners_count },
                        {
                            header: 'Actions',
                            cell: (prize: AdminPrize) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ competition: competition.id, prize: prize.id })} className="text-sm underline">
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('Delete this prize?')) {
                                                router.delete(destroy.url({ competition: competition.id, prize: prize.id }));
                                            }
                                        }}
                                    >
                                        Delete
                                    </button>
                                </div>
                            ),
                        },
                    ]}
                    paginator={prizes}
                />
            </div>
        </>
    );
}

PrizesIndex.layout = {
    breadcrumbs: [
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Competitions', href: competitionsIndex() },
        { title: 'Prizes', href: '#' },
    ] as BreadcrumbItem[],
};
