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
            <Head title="المسابقات المعينة" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">المسابقات المعينة</h1>

                <DataTable
                    columns={[
                        { header: 'العنوان', cell: (c: JudgeCompetition) => c.title },
                        { header: 'النوع', cell: (c: JudgeCompetition) => c.competition_type?.name ?? '—' },
                        {
                            header: 'إجراءات',
                            cell: (c: JudgeCompetition) => (
                                <Link href={submissionsIndex({ competition: c.id })} className="text-sm underline">
                                    تقييم المشاركات
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
        { title: 'لوحة تحكم الحكم', href: dashboard() },
        { title: 'المسابقات المعينة', href: index() },
    ] as BreadcrumbItem[],
};
