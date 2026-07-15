import { Head } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { dashboard } from '@/routes/organizer';
import { index as competitionsIndex } from '@/routes/organizer/competitions';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type ParticipantSubmission = {
    id: number;
    status: string;
    participant: { id: number; name: string; email: string } | null;
};

export default function ParticipantsIndex({
    competition,
    submissions,
}: {
    competition: { id: number; title: string };
    submissions: LaravelPaginator<ParticipantSubmission>;
}) {
    return (
        <>
            <Head title={`Participants — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Participants in {competition.title}</h1>

                <DataTable
                    columns={[
                        { header: 'Name', cell: (s: ParticipantSubmission) => s.participant?.name ?? '—' },
                        { header: 'Email', cell: (s: ParticipantSubmission) => s.participant?.email ?? '—' },
                        {
                            header: 'Submission Status',
                            cell: (s: ParticipantSubmission) => <Badge variant="secondary">{s.status}</Badge>,
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
        { title: 'Organizer Dashboard', href: dashboard() },
        { title: 'My Competitions', href: competitionsIndex() },
        { title: 'Participants', href: '#' },
    ] as BreadcrumbItem[],
};
