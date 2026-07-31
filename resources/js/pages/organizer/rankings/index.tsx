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
            <Head title={`الترتيب — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">ترتيب {competition.title}</h1>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>المرتبة</TableHead>
                            <TableHead>المشارك</TableHead>
                            <TableHead>متوسط الدرجة</TableHead>
                            <TableHead>درجات الحكام</TableHead>
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
                                              .map((evaluation) => `${evaluation.judge?.name ?? 'غير معروف'}: ${evaluation.score ?? '—'}`)
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
        { title: 'لوحة تحكم المنظم', href: dashboard() },
        { title: 'مسابقاتي', href: competitionsIndex() },
        { title: 'الترتيب', href: '#' },
    ] as BreadcrumbItem[],
};
