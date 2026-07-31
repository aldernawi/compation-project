import { Head, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes/admin';
import { accept, destroy, index, reject } from '@/routes/admin/submissions';
import { translateSubmissionStatus } from '@/lib/translations';
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
            <Head title="المشاركات" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">المشاركات</h1>

                <DataTable
                    columns={[
                        { header: 'المسابقة', cell: (s: AdminSubmission) => s.competition?.title ?? '—' },
                        { header: 'المشارك', cell: (s: AdminSubmission) => s.participant?.name ?? '—' },
                        {
                            header: 'الحالة',
                            cell: (s: AdminSubmission) => <Badge variant="secondary">{translateSubmissionStatus(s.status)}</Badge>,
                        },
                        {
                            header: 'إجراءات',
                            cell: (s: AdminSubmission) => (
                                <div className="flex gap-2">
                                    <button
                                        type="button"
                                        className="text-sm underline"
                                        onClick={() => router.patch(accept.url({ submission: s.id }))}
                                    >
                                        قبول
                                    </button>
                                    <button
                                        type="button"
                                        className="text-sm underline"
                                        onClick={() => router.patch(reject.url({ submission: s.id }))}
                                    >
                                        رفض
                                    </button>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('حذف هذه المشاركة؟')) {
                                                router.delete(destroy.url({ submission: s.id }));
                                            }
                                        }}
                                    >
                                        حذف
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
        { title: 'لوحة تحكم المدير', href: dashboard() },
        { title: 'المشاركات', href: index() },
    ] as BreadcrumbItem[],
};
