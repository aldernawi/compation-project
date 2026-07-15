import { Head, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes/organizer';
import { index as competitionsIndex } from '@/routes/organizer/competitions';
import { accept, index, reject } from '@/routes/organizer/competitions/submissions';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type SubmissionStatus = 'submitted' | 'under_review' | 'accepted' | 'rejected' | 'under_evaluation' | 'evaluated';

type OrganizerSubmission = {
    id: number;
    status: SubmissionStatus;
    participant: { id: number; name: string } | null;
};

const STATUSES: SubmissionStatus[] = ['submitted', 'under_review', 'accepted', 'rejected', 'under_evaluation', 'evaluated'];

export default function SubmissionsIndex({
    competition,
    filters,
    submissions,
}: {
    competition: { id: number; title: string };
    filters: { status: string | null };
    submissions: LaravelPaginator<OrganizerSubmission>;
}) {
    return (
        <>
            <Head title={`Submissions — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Submissions for {competition.title}</h1>

                <select
                    value={filters.status ?? ''}
                    onChange={(event) =>
                        router.get(index({ competition: competition.id }).url, {
                            status: event.target.value || undefined,
                        })
                    }
                    className="border-input h-9 w-fit rounded-md border bg-transparent px-2 text-sm"
                >
                    <option value="">All statuses</option>
                    {STATUSES.map((status) => (
                        <option key={status} value={status}>
                            {status}
                        </option>
                    ))}
                </select>

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
