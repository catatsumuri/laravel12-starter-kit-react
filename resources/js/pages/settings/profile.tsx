import ProfileController from '@/actions/App/Http/Controllers/Settings/ProfileController';
import AvatarUpload from '@/components/avatar-upload';
import DeleteUser from '@/components/delete-user';
import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { formatDateTime, formatRelativeTime } from '@/lib/utils';
import { edit } from '@/routes/profile';
import { send } from '@/routes/verification';
import { type BreadcrumbItem, type SharedData } from '@/types';
import { Form, Head, Link, usePage } from '@inertiajs/react';
import { useRef, useState } from 'react';
import { useTranslation } from 'react-i18next';

export default function Profile({
    mustVerifyEmail,
}: {
    mustVerifyEmail: boolean;
}) {
    const { auth } = usePage<SharedData>().props;
    const [avatarFile, setAvatarFile] = useState<File | null>(null);
    const avatarUploadRef = useRef<{ clearSelection: () => void }>(null);
    const { t } = useTranslation();
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: t('settings.profile.breadcrumb'),
            href: edit().url,
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('settings.profile.head_title')} />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title={t('settings.profile.section_title')}
                        description={t('settings.profile.section_description')}
                    />

                    {auth.user.last_login_at && (
                        <div className="rounded-md bg-muted p-3 text-sm text-muted-foreground">
                            Last login:{' '}
                            {formatDateTime(auth.user.last_login_at)}{' '}
                            <span className="text-muted-foreground/70">
                                ({formatRelativeTime(auth.user.last_login_at)})
                            </span>
                        </div>
                    )}

                    <Form
                        {...ProfileController.update.form()}
                        transform={(data) => {
                            const formData = new FormData();
                            Object.entries(data).forEach(([key, value]) => {
                                formData.append(key, value as string);
                            });
                            if (avatarFile) {
                                formData.append('avatar', avatarFile);
                            }
                            return formData;
                        }}
                        options={{
                            preserveScroll: true,
                            onSuccess: () => {
                                avatarUploadRef.current?.clearSelection();
                            },
                        }}
                        className="space-y-6"
                    >
                        {({ processing, errors }) => (
                            <>
                                <AvatarUpload
                                    ref={avatarUploadRef}
                                    currentAvatar={auth.user.avatar}
                                    userName={auth.user.name}
                                    onFileSelect={setAvatarFile}
                                    error={errors.avatar}
                                />

                                <div className="grid gap-2">
                                    <Label htmlFor="name">
                                        {t('common.name')}
                                    </Label>

                                    <Input
                                        id="name"
                                        className="mt-1 block w-full"
                                        defaultValue={auth.user.name}
                                        name="name"
                                        required
                                        autoComplete="name"
                                        placeholder={t(
                                            'common.name_placeholder',
                                        )}
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.name}
                                    />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="email">
                                        {t('common.email_address')}
                                    </Label>

                                    <Input
                                        id="email"
                                        type="email"
                                        className="mt-1 block w-full"
                                        defaultValue={auth.user.email}
                                        name="email"
                                        required
                                        autoComplete="username"
                                        placeholder={t(
                                            'common.email_placeholder',
                                        )}
                                    />

                                    <InputError
                                        className="mt-2"
                                        message={errors.email}
                                    />
                                </div>

                                {mustVerifyEmail &&
                                    auth.user.email_verified_at === null && (
                                        <div>
                                            <p className="-mt-4 text-sm text-muted-foreground">
                                                {t(
                                                    'settings.profile.email_unverified',
                                                )}{' '}
                                                <Link
                                                    href={send()}
                                                    as="button"
                                                    className="text-foreground underline decoration-neutral-300 underline-offset-4 transition-colors duration-300 ease-out hover:decoration-current! dark:decoration-neutral-500"
                                                >
                                                    {t(
                                                        'settings.profile.resend_verification',
                                                    )}
                                                </Link>
                                            </p>
                                        </div>
                                    )}

                                <Button
                                    disabled={processing}
                                    data-test="update-profile-button"
                                >
                                    {t('common.save')}
                                </Button>
                            </>
                        )}
                    </Form>
                </div>

                <DeleteUser />
            </SettingsLayout>
        </AppLayout>
    );
}
