import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index, store } from '@/routes/admin/users';
import { type BreadcrumbItem } from '@/types';
import { Form, Head, Link, usePage } from '@inertiajs/react';
import { ArrowLeft } from 'lucide-react';
import { useTranslation } from 'react-i18next';

export default function Create() {
    const { t } = useTranslation();
    const {
        props: { breadcrumbs },
    } = usePage<{ breadcrumbs?: BreadcrumbItem[] }>();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('admin.users.head_title_create')} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center gap-4">
                    <Button variant="outline" size="sm" asChild>
                        <Link href={index()}>
                            <ArrowLeft className="size-4" />
                        </Link>
                    </Button>
                    <h1 className="text-2xl font-semibold">{t('admin.users.page_title_create')}</h1>
                </div>

                <div className="max-w-2xl rounded-lg border p-6">
                    <Form action={store()} className="space-y-6">
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="name">{t('admin.users.name_label')}</Label>
                                    <Input
                                        id="name"
                                        name="name"
                                        type="text"
                                        placeholder={t('admin.users.name_placeholder')}
                                        required
                                        autoFocus
                                    />
                                    <InputError message={errors.name} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="email">{t('admin.users.email_label')}</Label>
                                    <Input
                                        id="email"
                                        name="email"
                                        type="email"
                                        placeholder={t('admin.users.email_placeholder')}
                                        required
                                    />
                                    <InputError message={errors.email} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="password">{t('admin.users.password_label')}</Label>
                                    <Input
                                        id="password"
                                        name="password"
                                        type="password"
                                        placeholder={t('admin.users.password_placeholder')}
                                        required
                                    />
                                    <InputError message={errors.password} />
                                </div>

                                <div className="flex gap-2">
                                    <Button type="submit" disabled={processing}>
                                        {t('admin.users.create_user')}
                                    </Button>
                                    <Button
                                        type="button"
                                        variant="outline"
                                        disabled={processing}
                                        asChild
                                    >
                                        <Link href={index()}>{t('common.cancel')}</Link>
                                    </Button>
                                </div>
                            </>
                        )}
                    </Form>
                </div>
            </div>
        </AppLayout>
    );
}
