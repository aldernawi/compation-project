import { Head, Link } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { dashboard } from '@/routes/judge';
import { index } from '@/routes/judge/competitions';
import { index as submissionsIndex } from '@/routes/judge/competitions/submissions';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type JudgeCompetition = {
    id: number;
    title: string;
    competition_type: { id: number; name: string } | null;
};

export default function CompetitionsIndex({ competitions }: { competitions: LaravelPaginator<JudgeCompetition> }) {
    return (
        <>
            <Head title="Assigned Competitions" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Assigned Competitions</h1>

                <DataTable
                    columns={[
                        { header: 'Title', cell: (c: JudgeCompetition) => c.title },
                        { header: 'Type', cell: (c: JudgeCompetition) => c.competition_type?.name ?? '—' },
                        {
                            header: 'Actions',
                            cell: (c: JudgeCompetition) => (
                                <Link href={submissionsIndex({ competition: c.id })} className="text-sm underline">
                                    Evaluate Submissions
                                </Link>
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
        { title: 'Judge Dashboard', href: dashboard() },
        { title: 'Assigned Competitions', href: index() },
    ] as BreadcrumbItem[],
};
