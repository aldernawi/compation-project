import { Form, Head } from '@inertiajs/react';
import NotificationController from '@/actions/App/Http/Controllers/Organizer/NotificationController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { dashboard } from '@/routes/organizer';
import { index as competitionsIndex } from '@/routes/organizer/competitions';
import type { BreadcrumbItem } from '@/types';

export default function CreateNotification({ competition }: { competition: { id: number; title: string } }) {
    return (
        <>
            <Head title={`Notify — ${competition.title}`} />
            <div className="flex h-full flex-1 flex-col gap-4 p-4">
                <h1 className="text-lg font-semibold">Notify Participants of {competition.title}</h1>

                <Form
                    {...NotificationController.store.form({ competition: competition.id })}
                    resetOnSuccess
                    className="max-w-md space-y-6"
                >
                    {({ processing, errors, recentlySuccessful }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="message">Message</Label>
                                <Input id="message" name="message" required />
                                <InputError message={errors.message} />
                            </div>

                            <Button type="submit" disabled={processing}>
                                Send
                            </Button>

                            {recentlySuccessful && <p className="text-sm text-green-600">Notification sent.</p>}
                        </>
                    )}
                </Form>
            </div>
        </>
    );
}

CreateNotification.layout = {
    breadcrumbs: [
        { title: 'Organizer Dashboard', href: dashboard() },
        { title: 'My Competitions', href: competitionsIndex() },
        { title: 'Notify', href: '#' },
    ] as BreadcrumbItem[],
};
