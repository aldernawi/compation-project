import { Head, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { dashboard } from '@/routes/organizer';
import { index as competitionsIndex } from '@/routes/organizer/competitions';
import { index } from '@/routes/organizer/competitions/participants';
import { translateSubmissionStatus } from '@/lib/translations';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type ParticipantSubmission = {
    id: number;
    status: string;
    participant: { id: number; name: string; email: string } | null;
};

export default function ParticipantsIndex({
    competition,
    filters,
    submissions,
}: {
    competition: { id: number; title: string };
    filters: { search: string | null };
    submissions: LaravelPaginator<ParticipantSubmission>;
}) {
    return (
        <>
            <Head title={`المشاركون — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">المشاركون في {competition.title}</h1>

                <form
                    className="flex items-end gap-2"
                    onSubmit={(event) => {
                        event.preventDefault();
                        const form = new FormData(event.currentTarget);
                        router.get(index({ competition: competition.id }).url, {
                            search: form.get('search') || undefined,
                        });
                    }}
                >
                    <Input type="search" name="search" placeholder="ابحث بالاسم أو البريد الإلكتروني…" defaultValue={filters.search ?? ''} />
                    <Button type="submit">بحث</Button>
                </form>

                <DataTable
                    columns={[
                        { header: 'الاسم', cell: (s: ParticipantSubmission) => s.participant?.name ?? '—' },
                        { header: 'البريد الإلكتروني', cell: (s: ParticipantSubmission) => s.participant?.email ?? '—' },
                        {
                            header: 'حالة المشاركة',
                            cell: (s: ParticipantSubmission) => <Badge variant="secondary">{translateSubmissionStatus(s.status)}</Badge>,
                        },
                    ]}
                    paginator={submissions}
                />
            </div>
        </>
    );
}

ParticipantsIndex.layout = {
    breadcrumbs: [
        { title: 'لوحة تحكم المنظم', href: dashboard() },
        { title: 'مسابقاتي', href: competitionsIndex() },
        { title: 'المشاركون', href: '#' },
    ] as BreadcrumbItem[],
};
