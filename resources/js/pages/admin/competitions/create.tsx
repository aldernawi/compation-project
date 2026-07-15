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

export default function CreateCompetition({
    organizers,
    competitionTypes,
}: {
    organizers: SelectOption[];
    competitionTypes: SelectOption[];
}) {
    return (
        <>
            <Head title="New Competition" />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">New Competition</h1>

                <Form {...CompetitionController.store.form()} className="max-w-xl space-y-6">
                    {({ processing, errors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="title">Title</Label>
                                <Input id="title" name="title" required />
                                <InputError message={errors.title} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="organizer_id">Organizer</Label>
                                <select
                                    id="organizer_id"
                                    name="organizer_id"
                                    required
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
                                <Label htmlFor="competition_type_id">Competition Type</Label>
                                <select
                                    id="competition_type_id"
                                    name="competition_type_id"
                                    required
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
                                <Label htmlFor="description">Description</Label>
                                <Input id="description" name="description" />
                                <InputError message={errors.description} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="terms">Terms</Label>
                                <Input id="terms" name="terms" />
                                <InputError message={errors.terms} />
                            </div>

                            <div className="grid grid-cols-2 gap-4">
                                <div className="grid gap-2">
                                    <Label htmlFor="starts_at">Starts At</Label>
                                    <Input id="starts_at" type="datetime-local" name="starts_at" required />
                                    <InputError message={errors.starts_at} />
                                </div>
                                <div className="grid gap-2">
                                    <Label htmlFor="ends_at">Ends At</Label>
                                    <Input id="ends_at" type="datetime-local" name="ends_at" required />
                                    <InputError message={errors.ends_at} />
                                </div>
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="status">Status</Label>
                                <select
                                    id="status"
                                    name="status"
                                    required
                                    defaultValue="upcoming"
                                    className="border-input flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs outline-none md:text-sm"
                                >
                                    <option value="upcoming">Upcoming</option>
                                    <option value="open">Open</option>
                                    <option value="closed">Closed</option>
                                    <option value="under_evaluation">Under Evaluation</option>
                                    <option value="finished">Finished</option>
                                </select>
                                <InputError message={errors.status} />
                            </div>

                            <div className="grid gap-2">
                                <Label htmlFor="evaluation_method">Evaluation Method</Label>
                                <Input id="evaluation_method" name="evaluation_method" defaultValue="average_score" required />
                                <InputError message={errors.evaluation_method} />
                            </div>

                            <div className="flex items-center gap-2">
                                <Checkbox id="requires_approval" name="requires_approval" defaultChecked value="1" />
                                <Label htmlFor="requires_approval">Requires organizer approval before accepting submissions</Label>
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

CreateCompetition.layout = {
    breadcrumbs: [
        { title: 'Admin Dashboard', href: dashboard() },
        { title: 'Competitions', href: index() },
        { title: 'New', href: CompetitionController.create.url() },
    ] as BreadcrumbItem[],
};
