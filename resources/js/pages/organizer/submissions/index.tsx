import { Head, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes/organizer';
import { index as competitionsIndex } from '@/routes/organizer/competitions';
import { accept, reject } from '@/routes/organizer/competitions/submissions';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type OrganizerSubmission = {
    id: number;
    status: 'submitted' | 'under_review' | 'accepted' | 'rejected' | 'under_evaluation' | 'evaluated';
    participant: { id: number; name: string } | null;
};

export default function SubmissionsIndex({
    competition,
    submissions,
}: {
    competition: { id: number; title: string };
    submissions: LaravelPaginator<OrganizerSubmission>;
}) {
    return (
        <>
            <Head title={`Submissions — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Submissions for {competition.title}</h1>

                <DataTable
                    columns={[
                        { header: 'Participant', cell: (s: OrganizerSubmission) => s.participant?.name ?? '—' },
                        {
                            header: 'Status',
                            cell: (s: OrganizerSubmission) => <Badge variant="secondary">{s.status}</Badge>,
                        },
                        {
                            header: 'Actions',
                            cell: (s: OrganizerSubmission) => (
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        className="text-sm underline"
                                        onClick={() =>
                                            router.patch(accept.url({ competition: competition.id, submission: s.id }))
                                        }
                                    >
                                        Accept
                                    </button>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            const reason = prompt('Reason for rejection:');

                                            if (reason) {
                                                router.patch(
                                                    reject.url({ competition: competition.id, submission: s.id }),
                                                    { reason },
                                                );
                                            }
                                        }}
                                    >
                                        Reject
                                    </button>
                                </div>
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
        { title: 'Organizer Dashboard', href: dashboard() },
        { title: 'My Competitions', href: competitionsIndex() },
        { title: 'Submissions', href: '#' },
    ] as BreadcrumbItem[],
};
