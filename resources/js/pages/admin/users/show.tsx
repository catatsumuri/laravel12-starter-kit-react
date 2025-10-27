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
import AppLayout from '@/layouts/app-layout';
import { show, edit, index, destroy } from '@/routes/admin/users';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import { ArrowLeft, Mail, User as UserIcon, Calendar, Trash2 } from 'lucide-react';

interface Props {
    user: User;
}

export default function Show({ user }: Props) {
    const { props } = usePage();
    const auth = props.auth as { user: User };

    const handleDelete = () => {
        router.delete(destroy(user).url);
    };

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Users',
            href: index().url,
        },
        {
            title: 'Details',
            href: show(user).url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`User: ${user.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Link href={index()}>
                            <Button variant="outline" size="sm">
                                <ArrowLeft className="size-4" />
                            </Button>
                        </Link>
                        <h1 className="text-2xl font-semibold">User Details</h1>
                    </div>
                    <div className="flex gap-2">
                        <Link href={edit(user)}>
                            <Button>Edit User</Button>
                        </Link>
                        <Dialog>
                            <DialogTrigger asChild>
                                <Button
                                    variant="destructive"
                                    disabled={user.id === auth.user.id}
                                >
                                    <Trash2 className="size-4 mr-2" />
                                    Delete
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogTitle>Delete User</DialogTitle>
                                <DialogDescription>
                                    Are you sure you want to delete {user.name}? This action cannot be undone.
                                </DialogDescription>
                                <DialogFooter className="gap-2">
                                    <DialogClose asChild>
                                        <Button variant="secondary">Cancel</Button>
                                    </DialogClose>
                                    <Button variant="destructive" onClick={handleDelete}>
                                        Delete User
                                    </Button>
                                </DialogFooter>
                            </DialogContent>
                        </Dialog>
                    </div>
                </div>

                <div className="max-w-2xl space-y-4">
                    <div className="rounded-lg border p-6">
                        <div className="space-y-4">
                            <div className="flex items-start gap-3">
                                <UserIcon className="mt-1 size-5 text-muted-foreground" />
                                <div className="flex-1">
                                    <p className="text-sm text-muted-foreground">
                                        Name
                                    </p>
                                    <p className="text-base font-medium">
                                        {user.name}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <Mail className="mt-1 size-5 text-muted-foreground" />
                                <div className="flex-1">
                                    <p className="text-sm text-muted-foreground">
                                        Email
                                    </p>
                                    <p className="text-base font-medium">
                                        {user.email}
                                    </p>
                                </div>
                            </div>

                            <div className="flex items-start gap-3">
                                <Calendar className="mt-1 size-5 text-muted-foreground" />
                                <div className="flex-1">
                                    <p className="text-sm text-muted-foreground">
                                        Created At
                                    </p>
                                    <p className="text-base font-medium">
                                        {new Date(
                                            user.created_at,
                                        ).toLocaleString()}
                                    </p>
                                </div>
                            </div>

                            {user.email_verified_at && (
                                <div className="flex items-start gap-3">
                                    <Calendar className="mt-1 size-5 text-muted-foreground" />
                                    <div className="flex-1">
                                        <p className="text-sm text-muted-foreground">
                                            Email Verified At
                                        </p>
                                        <p className="text-base font-medium">
                                            {new Date(
                                                user.email_verified_at,
                                            ).toLocaleString()}
                                        </p>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
