import { Head, router, useForm } from '@inertiajs/react';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { dashboard } from '@/routes/organizer';
import { index as competitionsIndex } from '@/routes/organizer/competitions';
import { destroy, store } from '@/routes/organizer/competitions/judges';
import type { BreadcrumbItem } from '@/types';

type JudgeUser = { id: number; name: string; email?: string };

export default function JudgesIndex({
    competition,
    judges,
    availableJudges,
}: {
    competition: { id: number; title: string };
    judges: JudgeUser[];
    availableJudges: JudgeUser[];
}) {
    const { data, setData, post, processing } = useForm({ judge_id: '' });

    return (
        <>
            <Head title={`الحكام — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">حكام {competition.title}</h1>

                <form
                    className="flex items-end gap-2"
                    onSubmit={(event) => {
                        event.preventDefault();
                        post(store.url({ competition: competition.id }), { preserveScroll: true });
                    }}
                >
                    <select
                        value={data.judge_id}
                        onChange={(event) => setData('judge_id', event.target.value)}
                        required
                        className="border-input h-9 rounded-md border bg-transparent px-2 text-sm"
                    >
                        <option value="">اختر حَكَماً…</option>
                        {availableJudges.map((judge) => (
                            <option key={judge.id} value={judge.id}>
                                {judge.name}
                            </option>
                        ))}
                    </select>
                    <Button type="submit" disabled={processing}>
                        تعيين حَكَم
                    </Button>
                </form>

                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead>الاسم</TableHead>
                            <TableHead>البريد الإلكتروني</TableHead>
                            <TableHead>إجراءات</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        {judges.map((judge) => (
                            <TableRow key={judge.id}>
                                <TableCell>{judge.name}</TableCell>
                                <TableCell>{judge.email}</TableCell>
                                <TableCell>
                                    <button
                                        type="button"
                                        className="text-destructive text-sm underline"
                                        onClick={() => {
                                            if (confirm('إزالة هذا الحَكَم من المسابقة؟')) {
                                                router.delete(destroy.url({ competition: competition.id, judge: judge.id }));
                                            }
                                        }}
                                    >
                                        إزالة
                                    </button>
                                </TableCell>
                            </TableRow>
                        ))}
                    </TableBody>
                </Table>
            </div>
        </>
    );
}

JudgesIndex.layout = {
    breadcrumbs: [
        { title: 'لوحة تحكم المنظم', href: dashboard() },
        { title: 'مسابقاتي', href: competitionsIndex() },
        { title: 'الحكام', href: '#' },
    ] as BreadcrumbItem[],
};
