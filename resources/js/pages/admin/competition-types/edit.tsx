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
    submission_kind: 'image' | 'pdf' | 'video' | 'text' | 'link';
};

export default function EditCompetitionType({ competitionType }: { competitionType: EditableCompetitionType }) {
    return (
        <>
            <Head title="Edit Competition Type" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Edit Competition Type</h1>

                <Form
                    {...CompetitionTypeController.update.form({ competition_type: competitionType.id })}
                    className="max-w-md space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input id="name" name="name" defaultValue={competitionType.name} required />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">Description</Label>
                                <Input id="description" name="description" defaultValue={competitionType.description ?? ''} />
                                <InputError message={errors.description} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="submission_kind">Submission Kind</Label>
                                <select
                                    id="submission_kind"
                                    name="submission_kind"
                                    required
                                    defaultValue={competitionType.submission_kind}
                                    className="border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none md:text-sm"
                                >
                                    <option value="image">Image</option>
                                    <option value="pdf">PDF</option>
                                    <option value="video">Video</option>
                                    <option value="text">Text</option>
                                    <option value="link">Link</option>
                                </select>
                                <InputError message={errors.submission_kind} />
                            </div>

                            <Button type="submit" disabled={processing}>
                                Save
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
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Competition Types', href: index() },
        { title: 'Edit', href: '#' },
    ] as BreadcrumbItem[],
};
