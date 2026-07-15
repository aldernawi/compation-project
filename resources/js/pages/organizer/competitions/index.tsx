import { Head, Link } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/organizer';
import { create, edit, index } from '@/routes/organizer/competitions';
import { index as judgesIndex } from '@/routes/organizer/competitions/judges';
import { index as participantsIndex } from '@/routes/organizer/competitions/participants';
import { index as rankingsIndex } from '@/routes/organizer/competitions/rankings';
import { index as submissionsIndex } from '@/routes/organizer/competitions/submissions';
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
            <Head title="My Competitions" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">My Competitions</h1>
                    <Button asChild>
                        <Link href={create()}>New Competition</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'Title', cell: (c: OrganizerCompetition) => c.title },
                        { header: 'Type', cell: (c: OrganizerCompetition) => c.competition_type?.name ?? '—' },
                        {
                            header: 'Status',
                            cell: (c: OrganizerCompetition) => <Badge variant="secondary">{c.status}</Badge>,
                        },
                        {
                            header: 'Actions',
                            cell: (c: OrganizerCompetition) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ competition: c.id })} className="text-sm underline">
                                        Edit
                                    </Link>
                                    <Link href={submissionsIndex({ competition: c.id })} className="text-sm underline">
                                        Submissions
                                    </Link>
                                    <Link href={judgesIndex({ competition: c.id })} className="text-sm underline">
                                        Judges
                                    </Link>
                                    <Link href={participantsIndex({ competition: c.id })} className="text-sm underline">
                                        Participants
                                    </Link>
                                    <Link href={rankingsIndex({ competition: c.id })} className="text-sm underline">
                                        Rankings
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
        { title: 'Organizer Dashboard', href: dashboard() },
        { title: 'My Competitions', href: index() },
    ] as BreadcrumbItem[],
};
