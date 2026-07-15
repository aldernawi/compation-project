import { Head } from '@inertiajs/react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/reports';
import type { BreadcrumbItem } from '@/types';

type Stats = {
    competitions_count: number;
    participants_count: number;
    submissions_count: number;
    winners_count: number;
};

type CompetitionParticipation = {
    id: number;
    title: string;
    submissions_count: number;
};

type SubmissionsByType = {
    type: string;
    count: number;
};

export default function ReportsIndex({
    stats,
    mostParticipatedCompetitions,
    submissionsByType,
}: {
    stats: Stats;
    mostParticipatedCompetitions: CompetitionParticipation[];
    submissionsByType: SubmissionsByType[];
}) {
    return (
        <>
            <Head title="Reports" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <h1 className="text-lg font-semibold">Reports & Statistics</h1>

                <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                    {[
                        { label: 'Competitions', value: stats.competitions_count },
                        { label: 'Participants', value: stats.participants_count },
                        { label: 'Submissions', value: stats.submissions_count },
                        { label: 'Winners', value: stats.winners_count },
                    ].map((stat) => (
                        <div key={stat.label} className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                            <p className="text-muted-foreground text-sm">{stat.label}</p>
                            <p className="text-2xl font-semibold">{stat.value}</p>
                        </div>
                    ))}
                </div>

                <div>
                    <h2 className="mb-2 text-base font-semibold">Most Participated Competitions</h2>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Competition</TableHead>
                                <TableHead>Submissions</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {mostParticipatedCompetitions.map((competition) => (
                                <TableRow key={competition.id}>
                                    <TableCell>{competition.title}</TableCell>
                                    <TableCell>{competition.submissions_count}</TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>

                <div>
                    <h2 className="mb-2 text-base font-semibold">Submissions by Competition Type</h2>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>Type</TableHead>
                                <TableHead>Count</TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {submissionsByType.map((row) => (
                                <TableRow key={row.type}>
                                    <TableCell>{row.type}</TableCell>
                                    <TableCell>{row.count}</TableCell>
                                </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </div>
            </div>
        </>
    );
}

ReportsIndex.layout = {
    breadcrumbs: [
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Reports', href: index() },
    ] as BreadcrumbItem[],
};
