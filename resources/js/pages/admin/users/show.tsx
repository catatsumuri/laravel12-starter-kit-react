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
import { destroy, edit, index, show } from '@/routes/admin/users';
import { type BreadcrumbItem, type User } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/react';
import {
    Activity,
    ArrowLeft,
    Calendar,
    Mail,
    Trash2,
    User as UserIcon,
} from 'lucide-react';
import { useTranslation } from 'react-i18next';

interface Props {
    user: User;
}

export default function Show({ user }: Props) {
    const { t } = useTranslation();
    const { props } = usePage();
    const auth = props.auth as { user: User };

    const handleDelete = () => {
        router.delete(destroy(user).url);
    };

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('admin.users.breadcrumb'),
            href: index().url,
        },
        {
            title: t('admin.users.breadcrumb_details'),
            href: show(user).url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`${t('admin.users.head_title_show')} ${user.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Button variant="outline" size="sm" asChild>
                            <Link href={index()}>
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <h1 className="text-2xl font-semibold">{t('admin.users.page_title_details')}</h1>
                    </div>
                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
                            <Link href={`/admin/users/${user.id}/activities`}>
                                <Activity className="mr-2 size-4" />
                                {t('admin.users.activity_log_button')}
                            </Link>
                        </Button>
                        <Button asChild>
                            <Link href={edit(user)}>{t('admin.users.edit_user')}</Link>
                        </Button>
                        <Dialog>
                            <DialogTrigger asChild>
                                <Button
                                    variant="destructive"
                                    disabled={user.id === auth.user.id}
                                >
                                    <Trash2 className="mr-2 size-4" />
                                    {t('admin.users.delete_button_short')}
                                </Button>
                            </DialogTrigger>
                            <DialogContent>
                                <DialogTitle>{t('admin.users.delete_user')}</DialogTitle>
                                <DialogDescription>
                                    {t('admin.users.delete_confirmation').replace('{name}', user.name)}
                                </DialogDescription>
                                <DialogFooter className="gap-2">
                                    <DialogClose asChild>
                                        <Button variant="secondary">
                                            {t('common.cancel')}
                                        </Button>
                                    </DialogClose>
                                    <Button
                                        variant="destructive"
                                        onClick={handleDelete}
                                    >
                                        {t('admin.users.delete_button')}
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
                                        {t('common.name')}
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
                                        {t('common.email')}
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
                                        {t('admin.users.created_at')}
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
                                            {t('admin.users.email_verified_at')}
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
