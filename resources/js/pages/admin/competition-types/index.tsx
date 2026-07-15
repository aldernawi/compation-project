import { Head, Link, router } from '@inertiajs/react';
import { DataTable } from '@/components/data-table';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes/admin';
import { create, destroy, edit, index } from '@/routes/admin/competition-types';
import type { BreadcrumbItem, LaravelPaginator } from '@/types';

type AdminCompetitionType = {
    id: number;
    name: string;
    slug: string;
    submission_kind: 'image' | 'pdf' | 'video' | 'text' | 'link';
};

export default function CompetitionTypesIndex({ competitionTypes }: { competitionTypes: LaravelPaginator<AdminCompetitionType> }) {
    return (
        <>
            <Head title="Competition Types" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">Competition Types</h1>
                    <Button asChild>
                        <Link href={create()}>New Type</Link>
                    </Button>
                </div>

                <DataTable
                    columns={[
                        { header: 'Name', cell: (type: AdminCompetitionType) => type.name },
                        { header: 'Slug', cell: (type: AdminCompetitionType) => type.slug },
                        {
                            header: 'Submission Kind',
                            cell: (type: AdminCompetitionType) => <Badge variant="secondary">{type.submission_kind}</Badge>,
                        },
                        {
                            header: 'Actions',
                            cell: (type: AdminCompetitionType) => (
                                <div className="flex gap-2">
                                    <Link href={edit({ competition_type: type.id })} className="text-sm underline">
                                        Edit
                                    </Link>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('Delete this competition type?')) {
                                                router.delete(destroy.url({ competition_type: type.id }));
                                            }
                                        }}
                                    >
                                        Delete
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
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Competition Types', href: index() },
    ] as BreadcrumbItem[],
};
