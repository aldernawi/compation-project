import { Head, Link } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes/judge';
import { index as competitionsIndex } from '@/routes/judge/competitions';
import { evaluate } from '@/routes/judge/submissions';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type JudgeSubmission = {
    id: number;
    evaluation_status: 'evaluated' | 'needs_review' | null;
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
            <Head title={`التقييم — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">مشاركات {competition.title}</h1>

                <DataTable
                    columns={[
                        { header: 'المشارك', cell: (s: JudgeSubmission) => s.participant?.name ?? '—' },
                        {
                            header: 'الحالة',
                            cell: (s: JudgeSubmission) => {
                                if (s.evaluation_status === 'evaluated') {
                                    return <Badge>تم التقييم</Badge>;
                                }

                                if (s.evaluation_status === 'needs_review') {
                                    return <Badge variant="destructive">يحتاج مراجعة</Badge>;
                                }

                                return <Badge variant="secondary">لم يتم التقييم بعد</Badge>;
                            },
                        },
                        {
                            header: 'إجراءات',
                            cell: (s: JudgeSubmission) => (
                                <Link href={evaluate({ submission: s.id })} className="text-sm underline">
                                    {s.evaluation_status ? 'مراجعة التقييم' : 'تقييم'}
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
        { title: 'لوحة تحكم الحكم', href: dashboard() },
        { title: 'المسابقات المعينة', href: competitionsIndex() },
        { title: 'المشاركات', href: '#' },
    ] as BreadcrumbItem[],
};
