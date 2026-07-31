import { Head, Link, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { create, destroy, edit, index } from '@/routes/admin/competition-types';
import { translateSubmissionKind } from '@/lib/translations';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type AdminCompetitionType = {
    id: number;
    name: string;
    slug: string;
    submission_kind: 'image' | 'pdf' | 'video' | 'text' | 'link' | 'none';
};

export default function CompetitionTypesIndex({ competitionTypes }: { competitionTypes: LaravelPaginator<AdminCompetitionType> }) {
    return (
        <>
            <Head title="أنواع المسابقات" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">أنواع المسابقات</h1>
                    <Button asChild>
                        <Link href={create()}>نوع جديد</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'الاسم', cell: (type: AdminCompetitionType) => type.name },
                        { header: 'المعرّف', cell: (type: AdminCompetitionType) => type.slug },
                        {
                            header: 'نوع المشاركة',
                            cell: (type: AdminCompetitionType) => <Badge variant="secondary">{translateSubmissionKind(type.submission_kind)}</Badge>,
                        },
                        {
                            header: 'إجراءات',
                            cell: (type: AdminCompetitionType) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ competition_type: type.id })} className="text-sm underline">
                                        تعديل
                                    </Link>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('حذف هذا النوع من المسابقات؟')) {
                                                router.delete(destroy.url({ competition_type: type.id }));
                                            }
                                        }}
                                    >
                                        حذف
                                    </button>
                                </div>
                            ),
                        },
                    ]}
                    paginator={competitionTypes}
                />
            </div>
        </>
    );
}

CompetitionTypesIndex.layout = {
    breadcrumbs: [
        { title: 'لوحة تحكم المدير', href: dashboard() },
        { title: 'أنواع المسابقات', href: index() },
    ] as BreadcrumbItem[],
};
