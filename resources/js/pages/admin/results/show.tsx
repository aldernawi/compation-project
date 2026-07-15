import { Head, router } from '@inertiajs/react';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes/admin';
import { index as competitionsIndex } from '@/routes/admin/competitions';
import { assignPrize, publish } from '@/routes/admin/competitions/results';
import type { BreadcrumbItem } from '@/types';

type ResultPrize = { id: number; title: string; rank: number };

type ResultSubmission = {
    id: number;
    participant: { id: number; name: string } | null;
    status: string;
    average_score: number | null;
    prize: ResultPrize | null;
};

export default function ResultsShow({
    competition,
    prizes,
    submissions,
}: {
    competition: { id: number; title: string; results_published_at: string | null };
    prizes: ResultPrize[];
    submissions: ResultSubmission[];
}) {
    return (
        <>
            <Head title={`Results — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <div className="flex items-center justify-between">
                    <h1 className="text-lg font-semibold">Results for {competition.title}</h1>
                    <div className="flex items-center gap-3">
                        {competition.results_published_at ? (
                            <Badge>Published</Badge>
                        ) : (
                            <Badge variant="secondary">Not Published</Badge>
                        )}
                        <Button
                            onClick={() => {
                                if (confirm('Publish results and notify winners?')) {
                                    router.post(publish.url({ competition: competition.id }));
                                }
                            }}
                        >
                            Publish Results
                        </Button>
                    </div>
                </div>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>Participant</TableHead>
                            <TableHead>Status</TableHead>
                            <TableHead>Average Score</TableHead>
                            <TableHead>Prize</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {submissions.map((submission) => (
                            <TableRow key={submission.id}>
                                <TableCell>{submission.participant?.name ?? '—'}</TableCell>
                                <TableCell>{submission.status}</TableCell>
                                <TableCell>{submission.average_score ?? '—'}</TableCell>
                                <TableCell>
                                    <select
                                        defaultValue={submission.prize?.id ?? ''}
                                        onChange={(event) =>
                                            router.patch(
                                                assignPrize.url({ competition: competition.id, submission: submission.id }),
                                                { prize_id: event.target.value || null },
                                            )
                                        }
                                        className="border-input h-9 rounded-md border bg-transparent px-2 text-sm"
                                    >
                                        <option value="">No prize</option>
                                        {prizes.map((prize) => (
                                            <option key={prize.id} value={prize.id}>
                                                {prize.title}
                                            </option>
                                        ))}
                                    </select>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </>
    );
}

ResultsShow.layout = {
    breadcrumbs: [
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Competitions', href: competitionsIndex() },
        { title: 'Results', href: '#' },
    ] as BreadcrumbItem[],
};
