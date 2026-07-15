import { Head, Link } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes/judge';
import { index as competitionsIndex } from '@/routes/judge/competitions';
import { evaluate } from '@/routes/judge/submissions';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type JudgeSubmission = {
    id: number;
    evaluated: boolean;
    participant: { id: number; name: string } | null;
};

export default function SubmissionsIndex({
    competition,
    submissions,
}: {
    competition: { id: number; title: string };
    submissions: LaravelPaginator<JudgeSubmission>;
}) {
    return (
        <>
            <Head title={`Evaluate — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Submissions for {competition.title}</h1>

                <DataTable
                    columns={[
                        { header: 'Participant', cell: (s: JudgeSubmission) => s.participant?.name ?? '—' },
                        {
                            header: 'Status',
                            cell: (s: JudgeSubmission) =>
                                s.evaluated ? <Badge>Evaluated</Badge> : <Badge variant="secondary">Not yet evaluated</Badge>,
                        },
                        {
                            header: 'Actions',
                            cell: (s: JudgeSubmission) => (
                                <Link href={evaluate({ submission: s.id })} className="text-sm underline">
                                    {s.evaluated ? 'Review Evaluation' : 'Evaluate'}
                                </Link>
                            ),
                        },
                    ]}
                    paginator={submissions}
                />
            </div>
        </>
    );
}

SubmissionsIndex.layout = {
    breadcrumbs: [
        { title: 'Judge Dashboard', href: dashboard() },
        { title: 'Assigned Competitions', href: competitionsIndex() },
        { title: 'Submissions', href: '#' },
    ] as BreadcrumbItem[],
};
