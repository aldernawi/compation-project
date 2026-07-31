import { Form, Head } from '@inertiajs/react';
import CompetitionController from '@/actions/App/Http/Controllers/Admin/CompetitionController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/competitions';
import type { BreadcrumbItem } from '@/types';

type SelectOption = { id: number; name: string };

type EditableCompetition = {
    id: number;
    organizer_id: number;
    competition_type_id: number;
    title: string;
    description: string | null;
    terms: string | null;
    starts_at: string;
    ends_at: string;
    status: 'upcoming' | 'open' | 'closed' | 'under_evaluation' | 'finished';
    requires_approval: boolean;
    evaluation_method: string;
};

function toDatetimeLocal(value: string): string {
    return value.slice(0, 16);
}

export default function EditCompetition({
    competition,
    organizers,
    competitionTypes,
}: {
    competition: EditableCompetition;
    organizers: SelectOption[];
    competitionTypes: SelectOption[];
}) {
    return (
        <>
            <Head title="تعديل المسابقة" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">تعديل المسابقة</h1>

                <Form {...CompetitionController.update.form({ competition: competition.id })} className="max-w-xl space-y-6">
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="title">العنوان</Label>
                                <Input id="title" name="title" defaultValue={competition.title} required />
                                <InputError message={errors.title} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="organizer_id">المنظم</Label>
                                <select
                                    id="organizer_id"
                                    name="organizer_id"
                                    required
                                    defaultValue={competition.organizer_id}
                                    className="border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none md:text-sm"
                                >
                                    {organizers.map((organizer) => (
                                        <option key={organizer.id} value={organizer.id}>
                                            {organizer.name}
                                        </option>
                                    ))}
                                </select>
                                <InputError message={errors.organizer_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="competition_type_id">نوع المسابقة</Label>
                                <select
                                    id="competition_type_id"
                                    name="competition_type_id"
                                    required
                                    defaultValue={competition.competition_type_id}
                                    className="border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none md:text-sm"
                                >
                                    {competitionTypes.map((type) => (
                                        <option key={type.id} value={type.id}>
                                            {type.name}
                                        </option>
                                    ))}
                                </select>
                                <InputError message={errors.competition_type_id} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">الوصف</Label>
                                <Input id="description" name="description" defaultValue={competition.description ?? ''} />
                                <InputError message={errors.description} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="terms">الشروط</Label>
                                <Input id="terms" name="terms" defaultValue={competition.terms ?? ''} />
                                <InputError message={errors.terms} />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="starts_at">تاريخ البداية</Label>
                                    <Input
                                        id="starts_at"
                                        type="datetime-local"
                                        name="starts_at"
                                        defaultValue={toDatetimeLocal(competition.starts_at)}
                                        required
                                    />
                                    <InputError message={errors.starts_at} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="ends_at">تاريخ النهاية</Label>
                                    <Input
                                        id="ends_at"
                                        type="datetime-local"
                                        name="ends_at"
                                        defaultValue={toDatetimeLocal(competition.ends_at)}
                                        required
                                    />
                                    <InputError message={errors.ends_at} />
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="status">الحالة</Label>
                                <select
                                    id="status"
                                    name="status"
                                    required
                                    defaultValue={competition.status}
                                    className="border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none md:text-sm"
                                >
                                    <option value="upcoming">قادمة</option>
                                    <option value="open">مفتوحة</option>
                                    <option value="closed">مغلقة</option>
                                    <option value="under_evaluation">قيد التقييم</option>
                                    <option value="finished">منتهية</option>
                                </select>
                                <InputError message={errors.status} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="evaluation_method">طريقة التقييم</Label>
                                <Input
                                    id="evaluation_method"
                                    name="evaluation_method"
                                    defaultValue={competition.evaluation_method}
                                    required
                                />
                                <InputError message={errors.evaluation_method} />
                            </div>

                            <div className="flex items-center gap-2">
                                <Checkbox
                                    id="requires_approval"
                                    name="requires_approval"
                                    defaultChecked={competition.requires_approval}
                                    value="1"
                                />
                                <Label htmlFor="requires_approval">يتطلب موافقة المنظم قبل قبول المشاركات</Label>
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

EditCompetition.layout = {
    breadcrumbs: [
        { title: 'لوحة تحكم المدير', href: dashboard() },
        { title: 'المسابقات', href: index() },
        { title: 'تعديل', href: '#' },
    ] as BreadcrumbItem[],
};
