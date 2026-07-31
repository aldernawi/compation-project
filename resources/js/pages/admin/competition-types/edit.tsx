import { Form, Head } from '@inertiajs/react';
import CompetitionTypeController from '@/actions/App/Http/Controllers/Admin/CompetitionTypeController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/competition-types';
import type { BreadcrumbItem } from '@/types';

type EditableCompetitionType = {
    id: number;
    name: string;
    description: string | null;
    submission_kind: 'image' | 'pdf' | 'video' | 'text' | 'link' | 'none';
};

export default function EditCompetitionType({ competitionType }: { competitionType: EditableCompetitionType }) {
    return (
        <>
            <Head title="تعديل نوع المسابقة" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">تعديل نوع المسابقة</h1>

                <Form
                    {...CompetitionTypeController.update.form({ competition_type: competitionType.id })}
                    className="max-w-md space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">الاسم</Label>
                                <Input id="name" name="name" defaultValue={competitionType.name} required />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">الوصف</Label>
                                <Input id="description" name="description" defaultValue={competitionType.description ?? ''} />
                                <InputError message={errors.description} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="submission_kind">نوع المشاركة</Label>
                                <select
                                    id="submission_kind"
                                    name="submission_kind"
                                    required
                                    defaultValue={competitionType.submission_kind}
                                    className="border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none md:text-sm"
                                >
                                    <option value="image">صورة</option>
                                    <option value="pdf">PDF</option>
                                    <option value="video">فيديو</option>
                                    <option value="text">نص</option>
                                    <option value="link">رابط</option>
                                    <option value="none">تسجيل فقط</option>
                                </select>
                                <InputError message={errors.submission_kind} />
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

EditCompetitionType.layout = {
    breadcrumbs: [
        { title: 'لوحة تحكم المدير', href: dashboard() },
        { title: 'أنواع المسابقات', href: index() },
        { title: 'تعديل', href: '#' },
    ] as BreadcrumbItem[],
};
