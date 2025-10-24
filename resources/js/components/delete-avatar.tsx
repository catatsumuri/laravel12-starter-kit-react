import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Form } from '@inertiajs/react';

interface DeleteAvatarProps {
    hasAvatar: boolean;
}

export default function DeleteAvatar({ hasAvatar }: DeleteAvatarProps) {
    if (!hasAvatar) {
        return null;
    }

    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button
                    variant="destructive"
                    type="button"
                    data-test="delete-avatar-button"
                >
                    Remove avatar
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Remove profile picture?</DialogTitle>
                <DialogDescription>
                    Are you sure you want to remove your profile picture? This
                    action cannot be undone.
                </DialogDescription>

                <Form
                    {...ProfileController.deleteAvatar.form()}
                    options={{
                        preserveScroll: true,
                    }}
                    resetOnSuccess
                >
                    {({ processing }) => (
                        <DialogFooter className="gap-2">
                            <DialogClose asChild>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>

                            <Button
                                variant="destructive"
                                disabled={processing}
                                asChild
                            >
                                <button
                                    type="submit"
                                    data-test="confirm-delete-avatar-button"
                                >
                                    Remove
                                </button>
                            </Button>
                        </DialogFooter>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
