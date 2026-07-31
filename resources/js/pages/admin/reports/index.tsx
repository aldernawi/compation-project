import { Head, router } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
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
    filters,
    stats,
    mostParticipatedCompetitions,
    submissionsByType,
}: {
    filters: { from: string | null; to: string | null };
    stats: Stats;
    mostParticipatedCompetitions: CompetitionParticipation[];
    submissionsByType: SubmissionsByType[];
}) {
    return (
        <>
            <Head title="التقارير" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <h1 className="text-lg font-semibold">التقارير والإحصائيات</h1>

                <form
                    className="flex items-end gap-2"
                    onSubmit={(event) => {
                        event.preventDefault();
                        const form = new FormData(event.currentTarget);
                        router.get(index().url, {
                            from: form.get('from') || undefined,
                            to: form.get('to') || undefined,
                        });
                    }}
                >
                    <div className="grid gap-2">
                        <Label htmlFor="from">من</Label>
                        <Input id="from" type="date" name="from" defaultValue={filters.from ?? ''} />
                    </div>
                    <div className="grid gap-2">
                        <Label htmlFor="to">إلى</Label>
                        <Input id="to" type="date" name="to" defaultValue={filters.to ?? ''} />
                    </div>
                    <Button type="submit">تصفية</Button>
                </form>

                <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
                    {[
                        { label: 'المسابقات', value: stats.competitions_count },
                        { label: 'المشاركون', value: stats.participants_count },
                        { label: 'المشاركات', value: stats.submissions_count },
                        { label: 'الفائزون', value: stats.winners_count },
                    ].map((stat) => (
                        <div key={stat.label} className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                            <p className="text-muted-foreground text-sm">{stat.label}</p>
                            <p className="text-2xl font-semibold">{stat.value}</p>
                        </div>
                    ))}
                </div>

                <div>
                    <h2 className="mb-2 text-base font-semibold">أكثر المسابقات مشاركة</h2>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>المسابقة</TableHead>
                                <TableHead>المشاركات</TableHead>
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
                    <h2 className="mb-2 text-base font-semibold">المشاركات حسب نوع المسابقة</h2>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead>النوع</TableHead>
                                <TableHead>العدد</TableHead>
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
        { title: 'لوحة تحكم المدير', href: dashboard() },
        { title: 'التقارير', href: index() },
    ] as BreadcrumbItem[],
};
