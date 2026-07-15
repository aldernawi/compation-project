import { Form, Head } from '@inertiajs/react';
import SubmissionController from '@/actions/App/Http/Controllers/Judge/SubmissionController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/judge';
import { index as competitionsIndex } from '@/routes/judge/competitions';
import type { BreadcrumbItem } from '@/types';

type EvaluableSubmission = {
    id: number;
    text_content: string | null;
    link_url: string | null;
    participant: { id: number; name: string } | null;
};

export default function EvaluateSubmission({
    submission,
    evaluation,
}: {
    submission: EvaluableSubmission;
    evaluation: { score: number; notes: string | null } | null;
}) {
    return (
        <>
            <Head title="Evaluate Submission" />
            <div className="flex h-full flex-1 flex-col gap-6 p-4">
                <div>
                    <h1 className="text-lg font-semibold">Evaluate Submission</h1>
                    <p className="text-muted-foreground text-sm">Participant: {submission.participant?.name ?? '—'}</p>
                </div>

                <div className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                    {submission.text_content && <p className="whitespace-pre-wrap text-sm">{submission.text_content}</p>}
                    {submission.link_url && (
                        <a href={submission.link_url} target="_blank" rel="noreferrer" className="text-sm underline">
                            {submission.link_url}
                        </a>
                    )}
                </div>

                <Form
                    {...SubmissionController.storeEvaluation.form({ submission: submission.id })}
                    className="max-w-md space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="score">Score (0-100)</Label>
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
                                <Label htmlFor="notes">Notes</Label>
                                <Input id="notes" name="notes" defaultValue={evaluation?.notes ?? ''} />
                                <InputError message={errors.notes} />
                            </div>

                            <Button type="submit" disabled={processing}>
                                Save Evaluation
                            </Button>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

EvaluateSubmission.layout = {
    breadcrumbs: [
        { title: 'Judge Dashboard', href: dashboard() },
        { title: 'Assigned Competitions', href: competitionsIndex() },
        { title: 'Evaluate', href: '#' },
    ] as BreadcrumbItem[],
};
