import { Form, Head } from '@inertiajs/react';
import CompetitionTypeController from '@/actions/App/Http/Controllers/Admin/CompetitionTypeController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/admin';
import { index } from '@/routes/admin/competition-types';
import type { BreadcrumbItem } from '@/types';

export default function CreateCompetitionType() {
    return (
        <>
            <Head title="New Competition Type" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">New Competition Type</h1>

                <Form {...CompetitionTypeController.store.form()} className="max-w-md space-y-6">
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Name</Label>
                                <Input id="name" name="name" required />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="description">Description</Label>
                                <Input id="description" name="description" />
                                <InputError message={errors.description} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="submission_kind">Submission Kind</Label>
                                <select
                                    id="submission_kind"
                                    name="submission_kind"
                                    required
                                    defaultValue="image"
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
                                Create
                            </Button>
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

CreateCompetitionType.layout = {
    breadcrumbs: [
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Competition Types', href: index() },
        { title: 'New', href: CompetitionTypeController.create.url() },
    ] as BreadcrumbItem[],
};
