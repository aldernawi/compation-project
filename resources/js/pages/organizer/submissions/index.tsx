import { Head, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes/organizer';
import { index as competitionsIndex } from '@/routes/organizer/competitions';
import { accept, index, reject } from '@/routes/organizer/competitions/submissions';
import { translateSubmissionStatus, submissionStatusLabels } from '@/lib/translations';
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
            <Head title={`المشاركات — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">مشاركات {competition.title}</h1>

                <select
                    value={filters.status ?? ''}
                    onChange={(event) =>
                        router.get(index({ competition: competition.id }).url, {
                            status: event.target.value || undefined,
                        })
                    }
                    className="border-input h-9 w-fit rounded-md border bg-transparent px-2 text-sm"
                >
                    <option value="">جميع الحالات</option>
                    {STATUSES.map((status) => (
                        <option key={status} value={status}>
                            {submissionStatusLabels[status] ?? status}
                        </option>
                    ))}
                </select>

                <DataTable
                    columns={[
                        { header: 'المشارك', cell: (s: OrganizerSubmission) => s.participant?.name ?? '—' },
                        {
                            header: 'الحالة',
                            cell: (s: OrganizerSubmission) => <Badge variant="secondary">{translateSubmissionStatus(s.status)}</Badge>,
                        },
                        {
                            header: 'إجراءات',
                            cell: (s: OrganizerSubmission) => (
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        className="text-sm underline"
                                        onClick={() =>
                                            router.patch(accept.url({ competition: competition.id, submission: s.id }))
                                        }
                                    >
                                        قبول
                                    </button>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            const reason = prompt('سبب الرفض:');

                                            if (reason) {
                                                router.patch(
                                                    reject.url({ competition: competition.id, submission: s.id }),
                                                    { reason },
                                                );
                                            }
                                        }}
                                    >
                                        رفض
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
        { title: 'لوحة تحكم المنظم', href: dashboard() },
        { title: 'مسابقاتي', href: competitionsIndex() },
        { title: 'المشاركات', href: '#' },
    ] as BreadcrumbItem[],
};
