import { Form, Head } from '@inertiajs/react';
import PrizeController from '@/actions/App/Http/Controllers/Admin/PrizeController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/admin';
import { index as competitionsIndex } from '@/routes/admin/competitions';
import type { BreadcrumbItem } from '@/types';

type EditablePrize = {
    id: number;
    title: string;
    description: string | null;
    winners_count: number;
    rank: number;
};

export default function EditPrize({
    competition,
    prize,
}: {
    competition: { id: number; title: string };
    prize: EditablePrize;
}) {
    return (
        <>
            <Head title={`تعديل الجائزة — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">تعديل جائزة {competition.title}</h1>

                <Form
                    {...PrizeController.update.form({ competition: competition.id, prize: prize.id })}
                    className="max-w-md space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="title">العنوان</Label>
                                <Input id="title" name="title" defaultValue={prize.title} required />
                                <InputError message={errors.title} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">الوصف</Label>
                                <Input id="description" name="description" defaultValue={prize.description ?? ''} />
                                <InputError message={errors.description} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="rank">المرتبة</Label>
                                <Input id="rank" type="number" name="rank" min={1} defaultValue={prize.rank} required />
                                <InputError message={errors.rank} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="winners_count">عدد الفائزين</Label>
                                <Input
                                    id="winners_count"
                                    type="number"
                                    name="winners_count"
                                    min={1}
                                    defaultValue={prize.winners_count}
                                    required
                                />
                                <InputError message={errors.winners_count} />
                            </div>

                            <Button type="submit" disabled={processing}>
                                حفظ
                            </Button>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

EditPrize.layout = {
    breadcrumbs: [
        { title: 'لوحة تحكم المدير', href: dashboard() },
        { title: 'المسابقات', href: competitionsIndex() },
        { title: 'تعديل الجائزة', href: '#' },
    ] as BreadcrumbItem[],
};
