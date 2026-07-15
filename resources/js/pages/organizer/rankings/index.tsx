import { Head } from '@inertiajs/react';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes/organizer';
import { index as competitionsIndex } from '@/routes/organizer/competitions';
import type { BreadcrumbItem } from '@/types';

type Evaluation = {
    judge: { id: number; name: string } | null;
    score: number | null;
    notes: string | null;
};

type RankedSubmission = {
    id: number;
    participant: { id: number; name: string } | null;
    average_score: number | null;
    evaluations: Evaluation[];
};

export default function RankingsIndex({
    competition,
    submissions,
}: {
    competition: { id: number; title: string };
    submissions: RankedSubmission[];
}) {
    return (
        <>
            <Head title={`Rankings — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Rankings for {competition.title}</h1>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Rank</TableHead>
                            <TableHead>Participant</TableHead>
                            <TableHead>Average Score</TableHead>
                            <TableHead>Judge Scores</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {submissions.map((submission, index) => (
                            <TableRow key={submission.id}>
                                <TableCell>{index + 1}</TableCell>
                                <TableCell>{submission.participant?.name ?? '—'}</TableCell>
                                <TableCell>{submission.average_score ?? '—'}</TableCell>
                                <TableCell>
                                    {submission.evaluations.length === 0
                                        ? '—'
                                        : submission.evaluations
                                              .map((evaluation) => `${evaluation.judge?.name ?? 'Unknown'}: ${evaluation.score ?? '—'}`)
                                              .join(', ')}
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </>
    );
}

RankingsIndex.layout = {
    breadcrumbs: [
        { title: 'Organizer Dashboard', href: dashboard() },
        { title: 'My Competitions', href: competitionsIndex() },
        { title: 'Rankings', href: '#' },
    ] as BreadcrumbItem[],
};
