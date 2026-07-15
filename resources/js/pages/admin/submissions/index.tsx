import { Head, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes/admin';
import { accept, destroy, index, reject } from '@/routes/admin/submissions';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type AdminSubmission = {
    id: number;
    status: 'submitted' | 'under_review' | 'accepted' | 'rejected' | 'under_evaluation' | 'evaluated';
    competition: { id: number; title: string } | null;
    participant: { id: number; name: string } | null;
};

export default function SubmissionsIndex({ submissions }: { submissions: LaravelPaginator<AdminSubmission> }) {
    return (
        <>
            <Head title="Submissions" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Submissions</h1>

                <DataTable
                    columns={[
                        { header: 'Competition', cell: (s: AdminSubmission) => s.competition?.title ?? '—' },
                        { header: 'Participant', cell: (s: AdminSubmission) => s.participant?.name ?? '—' },
                        {
                            header: 'Status',
                            cell: (s: AdminSubmission) => <Badge variant="secondary">{s.status}</Badge>,
                        },
                        {
                            header: 'Actions',
                            cell: (s: AdminSubmission) => (
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        className="text-sm underline"
                                        onClick={() => router.patch(accept.url({ submission: s.id }))}
                                    >
                                        Accept
                                    </button>
                                    <button
                                        type="button"
                                        className="text-sm underline"
                                        onClick={() => router.patch(reject.url({ submission: s.id }))}
                                    >
                                        Reject
                                    </button>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('Delete this submission?')) {
                                                router.delete(destroy.url({ submission: s.id }));
                                            }
                                        }}
                                    >
                                        Delete
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
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Submissions', href: index() },
    ] as BreadcrumbItem[],
};
