import { Head, Link, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { create, destroy, edit, index } from '@/routes/admin/competitions';
import { index as prizesIndex } from '@/routes/admin/competitions/prizes';
import { show as resultsShow } from '@/routes/admin/competitions/results';
import { translateCompetitionStatus } from '@/lib/translations';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type AdminCompetition = {
    id: number;
    title: string;
    status: 'upcoming' | 'open' | 'closed' | 'under_evaluation' | 'finished';
    organizer: { id: number; name: string } | null;
    competition_type: { id: number; name: string } | null;
};

export default function CompetitionsIndex({ competitions }: { competitions: LaravelPaginator<AdminCompetition> }) {
    return (
        <>
            <Head title="المسابقات" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">المسابقات</h1>
                    <Button asChild>
                        <Link href={create()}>مسابقة جديدة</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'العنوان', cell: (c: AdminCompetition) => c.title },
                        { header: 'المنظم', cell: (c: AdminCompetition) => c.organizer?.name ?? '—' },
                        { header: 'النوع', cell: (c: AdminCompetition) => c.competition_type?.name ?? '—' },
                        {
                            header: 'الحالة',
                            cell: (c: AdminCompetition) => <Badge variant="secondary">{translateCompetitionStatus(c.status)}</Badge>,
                        },
                        {
                            header: 'إجراءات',
                            cell: (c: AdminCompetition) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ competition: c.id })} className="text-sm underline">
                                        تعديل
                                    </Link>
                                    <Link href={prizesIndex({ competition: c.id })} className="text-sm underline">
                                        الجوائز
                                    </Link>
                                    <Link href={resultsShow({ competition: c.id })} className="text-sm underline">
                                        النتائج
                                    </Link>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('حذف هذه المسابقة؟')) {
                                                router.delete(destroy.url({ competition: c.id }));
                                            }
                                        }}
                                    >
                                        حذف
                                    </button>
                                </div>
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
        { title: 'لوحة تحكم المدير', href: dashboard() },
        { title: 'المسابقات', href: index() },
    ] as BreadcrumbItem[],
};
