import { Head, Link } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/organizer';
import { create, edit, index } from '@/routes/organizer/competitions';
import { index as judgesIndex } from '@/routes/organizer/competitions/judges';
import { create as notificationsCreate } from '@/routes/organizer/competitions/notifications';
import { index as participantsIndex } from '@/routes/organizer/competitions/participants';
import { index as rankingsIndex } from '@/routes/organizer/competitions/rankings';
import { index as submissionsIndex } from '@/routes/organizer/competitions/submissions';
import { translateCompetitionStatus } from '@/lib/translations';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type OrganizerCompetition = {
    id: number;
    title: string;
    status: 'upcoming' | 'open' | 'closed' | 'under_evaluation' | 'finished';
    competition_type: { id: number; name: string } | null;
};

export default function CompetitionsIndex({ competitions }: { competitions: LaravelPaginator<OrganizerCompetition> }) {
    return (
        <>
            <Head title="مسابقاتي" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">مسابقاتي</h1>
                    <Button asChild>
                        <Link href={create()}>مسابقة جديدة</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'العنوان', cell: (c: OrganizerCompetition) => c.title },
                        { header: 'النوع', cell: (c: OrganizerCompetition) => c.competition_type?.name ?? '—' },
                        {
                            header: 'الحالة',
                            cell: (c: OrganizerCompetition) => <Badge variant="secondary">{translateCompetitionStatus(c.status)}</Badge>,
                        },
                        {
                            header: 'إجراءات',
                            cell: (c: OrganizerCompetition) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ competition: c.id })} className="text-sm underline">
                                        تعديل
                                    </Link>
                                    <Link href={submissionsIndex({ competition: c.id })} className="text-sm underline">
                                        المشاركات
                                    </Link>
                                    <Link href={judgesIndex({ competition: c.id })} className="text-sm underline">
                                        الحكام
                                    </Link>
                                    <Link href={participantsIndex({ competition: c.id })} className="text-sm underline">
                                        المشاركون
                                    </Link>
                                    <Link href={rankingsIndex({ competition: c.id })} className="text-sm underline">
                                        الترتيب
                                    </Link>
                                    <Link href={notificationsCreate({ competition: c.id })} className="text-sm underline">
                                        إشعار
                                    </Link>
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
        { title: 'لوحة تحكم المنظم', href: dashboard() },
        { title: 'مسابقاتي', href: index() },
    ] as BreadcrumbItem[],
};
