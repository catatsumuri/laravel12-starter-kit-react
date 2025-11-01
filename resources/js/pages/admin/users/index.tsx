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
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { create, destroy, edit, index, show } from '@/routes/admin/users';
import { type BreadcrumbItem, type User } from '@/types';
import { Form, Head, Link, router, usePage } from '@inertiajs/react';
import { Pencil, Plus, Search, Trash2 } from 'lucide-react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedUsers {
    data: User[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props {
    users: PaginatedUsers;
    filters: {
        search?: string;
    };
}

export default function Index({ users, filters }: Props) {
    const { t } = useTranslation();
    const { props } = usePage();
    const flash = props.flash as { success?: string; error?: string };
    const auth = props.auth as { user: User };
    const [userToDelete, setUserToDelete] = useState<User | null>(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('admin.users.breadcrumb'),
            href: index().url,
        },
    ];

    const handleDelete = () => {
        if (userToDelete) {
            router.delete(destroy(userToDelete).url);
            setUserToDelete(null);
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('admin.users.head_title_index')} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <h1 className="text-2xl font-semibold">{t('admin.users.page_title_index')}</h1>
                    <Button asChild>
                        <Link href={create()}>
                            <Plus className="mr-2 size-4" />
                            {t('admin.users.add_user')}
                        </Link>
                    </Button>
                </div>

                {flash?.success && (
                    <div className="rounded-md border border-green-200 bg-green-50 p-3 text-sm text-green-800">
                        {flash.success}
                    </div>
                )}

                {flash?.error && (
                    <div className="rounded-md border border-red-200 bg-red-50 p-3 text-sm text-red-800">
                        {flash.error}
                    </div>
                )}

                <Form action={index()} className="flex gap-2">
                    <div className="relative flex-1">
                        <Search className="absolute top-2.5 left-2 size-4 text-muted-foreground" />
                        <Input
                            type="text"
                            name="search"
                            placeholder={t('admin.users.search_placeholder')}
                            defaultValue={filters.search}
                            className="pl-8"
                        />
                    </div>
                    <Button type="submit">{t('admin.users.search_button')}</Button>
                </Form>

                <div className="overflow-hidden rounded-lg border">
                    <table className="w-full">
                        <thead className="bg-muted/50">
                            <tr>
                                <th className="px-4 py-3 text-left text-sm font-medium">
                                    {t('admin.users.table_name')}
                                </th>
                                <th className="px-4 py-3 text-left text-sm font-medium">
                                    {t('admin.users.table_email')}
                                </th>
                                <th className="px-4 py-3 text-left text-sm font-medium">
                                    {t('admin.users.table_role')}
                                </th>
                                <th className="px-4 py-3 text-left text-sm font-medium">
                                    {t('admin.users.table_created_at')}
                                </th>
                                <th className="px-4 py-3 text-right text-sm font-medium">
                                    {t('admin.users.table_actions')}
                                </th>
                            </tr>
                        </thead>
                        <tbody className="divide-y">
                            {users.data.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan={5}
                                        className="px-4 py-8 text-center text-sm text-muted-foreground"
                                    >
                                        {t('admin.users.no_users_found')}
                                    </td>
                                </tr>
                            ) : (
                                users.data.map((user) => (
                                    <tr
                                        key={user.id}
                                        className="hover:bg-muted/30"
                                    >
                                        <td className="px-4 py-3 text-sm">
                                            <Link
                                                href={show(user)}
                                                className="font-medium text-foreground hover:underline"
                                            >
                                                {user.name}
                                            </Link>
                                        </td>
                                        <td className="px-4 py-3 text-sm">
                                            {user.email}
                                        </td>
                                        <td className="px-4 py-3 text-sm">
                                            <span className="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-blue-700/10 ring-inset">
                                                {user.roles?.[0]?.name ||
                                                    'user'}
                                            </span>
                                        </td>
                                        <td className="px-4 py-3 text-sm">
                                            {new Date(
                                                user.created_at,
                                            ).toLocaleDateString()}
                                        </td>
                                        <td className="px-4 py-3 text-right">
                                            <div className="flex justify-end gap-2">
                                                <Button
                                                    variant="outline"
                                                    size="sm"
                                                    asChild
                                                >
                                                    <Link href={edit(user)}>
                                                        <Pencil className="size-4" />
                                                    </Link>
                                                </Button>
                                                <Dialog>
                                                    <DialogTrigger asChild>
                                                        <Button
                                                            variant="outline"
                                                            size="sm"
                                                            disabled={
                                                                user.id ===
                                                                auth.user.id
                                                            }
                                                            onClick={() =>
                                                                setUserToDelete(
                                                                    user,
                                                                )
                                                            }
                                                        >
                                                            <Trash2 className="size-4" />
                                                        </Button>
                                                    </DialogTrigger>
                                                    <DialogContent>
                                                        <DialogTitle>
                                                            {t('admin.users.delete_user')}
                                                        </DialogTitle>
                                                        <DialogDescription>
                                                            {t('admin.users.delete_confirmation', { name: userToDelete?.name }).replace('{name}', userToDelete?.name || '')}
                                                        </DialogDescription>
                                                        <DialogFooter className="gap-2">
                                                            <DialogClose
                                                                asChild
                                                            >
                                                                <Button variant="secondary">
                                                                    {t('common.cancel')}
                                                                </Button>
                                                            </DialogClose>
                                                            <Button
                                                                variant="destructive"
                                                                onClick={
                                                                    handleDelete
                                                                }
                                                            >
                                                                {t('admin.users.delete_button')}
                                                            </Button>
                                                        </DialogFooter>
                                                    </DialogContent>
                                                </Dialog>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {users.last_page > 1 && (
                    <div className="flex items-center justify-center gap-1">
                        {users.links.map((link, index) => (
                            <Button
                                key={index}
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url)}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
