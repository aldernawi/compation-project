import { Form, Head, router } from '@inertiajs/react';
import SubmissionController from '@/actions/App/Http/Controllers/Judge/SubmissionController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/judge';
import { index as competitionsIndex } from '@/routes/judge/competitions';
import { needsReview } from '@/routes/judge/submissions';
import type { BreadcrumbItem } from '@/types';

type EvaluableSubmission = {
    id: number;
    text_content: string | null;
    link_url: string | null;
    media_url: string | null;
    participant: { id: number; name: string } | null;
};

const IMAGE_EXTENSIONS = ['.jpg', '.jpeg', '.png', '.gif', '.webp'];

function isImageUrl(url: string): boolean {
    return IMAGE_EXTENSIONS.some((extension) => url.toLowerCase().endsWith(extension));
}

export default function EvaluateSubmission({
    submission,
    evaluation,
}: {
    submission: EvaluableSubmission;
    evaluation: { score: number; notes: string | null; status: string } | null;
}) {
    return (
        <>
            <Head title="تقييم المشاركة" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-lg font-semibold">تقييم المشاركة</h1>
                    <p className="text-muted-foreground text-sm">المشارك: {submission.participant?.name ?? '—'}</p>
                </div>

                <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    {submission.text_content && <p className="whitespace-pre-wrap text-sm">{submission.text_content}</p>}
                    {submission.link_url && (
                        <a href={submission.link_url} target="_blank" rel="noreferrer" className="text-sm underline">
                            {submission.link_url}
                        </a>
                    )}
                    {submission.media_url &&
                        (isImageUrl(submission.media_url) ? (
                            <img src={submission.media_url} alt="مرفق المشاركة" className="max-h-96 rounded-md" />
                        ) : (
                            <a href={submission.media_url} target="_blank" rel="noreferrer" className="text-sm underline">
                                عرض الملف المرفق
                            </a>
                        ))}
                </div>

                <Form
                    {...SubmissionController.storeEvaluation.form({ submission: submission.id })}
                    className="max-w-md space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="score">الدرجة (0-100)</Label>
                                <Input
                                    id="score"
                                    type="number"
                                    name="score"
                                    min={0}
                                    max={100}
                                    defaultValue={evaluation?.score}
                                    required
                                />
                                <InputError message={errors.score} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="notes">الملاحظات</Label>
                                <Input id="notes" name="notes" defaultValue={evaluation?.notes ?? ''} />
                                <InputError message={errors.notes} />
                            </div>

                            <div className="flex gap-2">
                                <Button type="submit" disabled={processing}>
                                    حفظ التقييم
                                </Button>
                                <Button
                                    type="button"
                                    variant="outline"
                                    onClick={() => {
                                        const notesField = document.getElementById('notes') as HTMLInputElement | null;
                                        router.patch(needsReview.url({ submission: submission.id }), {
                                            notes: notesField?.value,
                                        });
                                    }}
                                >
                                    وضع علامة يحتاج مراجعة
                                </Button>
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

EvaluateSubmission.layout = {
    breadcrumbs: [
        { title: 'لوحة تحكم الحكم', href: dashboard() },
        { title: 'المسابقات المعينة', href: competitionsIndex() },
        { title: 'تقييم', href: '#' },
    ] as BreadcrumbItem[],
};
