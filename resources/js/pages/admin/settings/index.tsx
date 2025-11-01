import { Transition } from '@headlessui/react';
import { Form, Head, Link, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { useTranslation } from 'react-i18next';

import HeadingSmall from '@/components/heading-small';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app-layout';
import { update as adminSettingsUpdate } from '@/routes/admin/settings';
import { index as adminSettingsEnvironmentIndex } from '@/routes/admin/settings/environment';
import { type BreadcrumbItem } from '@/types';
import { AlertTriangle, FileText } from 'lucide-react';

interface AdminSettingsIndexProps {
    appName: string;
    appUrl: string;
    appDebug: boolean;
    appLocale: string;
    appFallbackLocale: string;
    awsAccessKeyId: string;
    awsSecretAccessKey: string;
    awsDefaultRegion: string;
    awsBucket: string;
    awsUsePathStyleEndpoint: boolean;
}

export default function AdminSettingsIndex({
    appName,
    appUrl,
    appDebug,
    appLocale,
    appFallbackLocale,
    awsAccessKeyId,
    awsSecretAccessKey,
    awsDefaultRegion,
    awsBucket,
    awsUsePathStyleEndpoint,
}: AdminSettingsIndexProps) {
    const { t } = useTranslation();
    const {
        props: { breadcrumbs },
    } = usePage<{ breadcrumbs?: BreadcrumbItem[] }>();
    const [showDebugWarning, setShowDebugWarning] = useState(false);
    const [appDebugValue, setAppDebugValue] = useState(appDebug);
    const [awsUsePathStyleEndpointValue, setAwsUsePathStyleEndpointValue] =
        useState(awsUsePathStyleEndpoint);

    function handleDebugChange(checked: boolean | 'indeterminate') {
        if (checked === true) {
            // Show warning when enabling debug mode
            setShowDebugWarning(true);
        } else {
            // Disable without warning
            setAppDebugValue(false);
        }
    }

    function confirmDebugEnable() {
        setAppDebugValue(true);
        setShowDebugWarning(false);
    }

    function cancelDebugEnable() {
        setShowDebugWarning(false);
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('admin.settings.head_title')} />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                <HeadingSmall
                    title={t('admin.settings.section_title')}
                    description={t('admin.settings.section_description')}
                />

                <div className="rounded-lg border bg-card p-6 shadow-sm">
                    <Form
                        action={adminSettingsUpdate()}
                        options={{
                            preserveScroll: true,
                        }}
                    >
                        {({ processing, errors, recentlySuccessful }) => (
                            <div className="space-y-6">
                                <div>
                                    <h3 className="mb-4 text-lg font-semibold">
                                        {t('admin.settings.application_settings')}
                                    </h3>

                                    <div className="space-y-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="app_name">
                                                {t('admin.settings.app_name')}
                                            </Label>

                                            <Input
                                                id="app_name"
                                                name="app_name"
                                                className="mt-1 block w-full"
                                                defaultValue={appName}
                                                required
                                                placeholder={t('admin.settings.app_name')}
                                            />

                                            <InputError
                                                className="mt-2"
                                                message={errors.app_name}
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="app_url">
                                                {t('admin.settings.app_url')}
                                            </Label>

                                            <Input
                                                id="app_url"
                                                name="app_url"
                                                type="url"
                                                className="mt-1 block w-full"
                                                defaultValue={appUrl}
                                                required
                                                placeholder="https://example.com"
                                            />

                                            <InputError
                                                className="mt-2"
                                                message={errors.app_url}
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="app_locale">
                                                {t('admin.settings.locale')}
                                            </Label>

                                            <Select
                                                name="app_locale"
                                                defaultValue={appLocale}
                                            >
                                                <SelectTrigger id="app_locale">
                                                    <SelectValue placeholder={t('admin.settings.locale_placeholder')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="ja">
                                                        {t('admin.settings.japanese')}
                                                    </SelectItem>
                                                    <SelectItem value="en">
                                                        {t('admin.settings.english')}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>

                                            <InputError
                                                className="mt-2"
                                                message={errors.app_locale}
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="app_fallback_locale">
                                                {t('admin.settings.fallback_locale')}
                                            </Label>

                                            <Select
                                                name="app_fallback_locale"
                                                defaultValue={appFallbackLocale}
                                            >
                                                <SelectTrigger id="app_fallback_locale">
                                                    <SelectValue placeholder={t('admin.settings.fallback_locale_placeholder')} />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="ja">
                                                        {t('admin.settings.japanese')}
                                                    </SelectItem>
                                                    <SelectItem value="en">
                                                        {t('admin.settings.english')}
                                                    </SelectItem>
                                                </SelectContent>
                                            </Select>

                                            <InputError
                                                className="mt-2"
                                                message={
                                                    errors.app_fallback_locale
                                                }
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div className="border-t pt-6">
                                    <h3 className="mb-4 text-lg font-semibold">
                                        {t('admin.settings.debug_settings')}
                                    </h3>

                                    <div className="space-y-4 rounded-lg border border-yellow-100 bg-yellow-50 p-4 dark:border-yellow-200/10 dark:bg-yellow-700/10">
                                        <div className="relative space-y-0.5 text-yellow-600 dark:text-yellow-100">
                                            <p className="font-medium">
                                                {t('admin.settings.debug_warning_title')}
                                            </p>
                                            <p className="text-sm">
                                                {t('admin.settings.debug_warning_message')}
                                            </p>
                                        </div>

                                        <div className="flex items-center space-x-2">
                                            <input
                                                type="hidden"
                                                name="app_debug"
                                                value="0"
                                            />
                                            <Checkbox
                                                id="app_debug"
                                                name="app_debug"
                                                value="1"
                                                checked={appDebugValue}
                                                onCheckedChange={
                                                    handleDebugChange
                                                }
                                            />
                                            <Label
                                                htmlFor="app_debug"
                                                className="cursor-pointer"
                                            >
                                                {t('admin.settings.enable_debug_mode')}
                                            </Label>
                                        </div>

                                        <InputError
                                            className="mt-2"
                                            message={errors.app_debug}
                                        />
                                    </div>
                                </div>

                                <Dialog
                                    open={showDebugWarning}
                                    onOpenChange={(open) => {
                                        if (!open) {
                                            cancelDebugEnable();
                                        }
                                    }}
                                >
                                    <DialogContent>
                                        <DialogTitle className="flex items-center gap-2">
                                            <AlertTriangle className="h-5 w-5 text-yellow-600" />
                                            {t('admin.settings.debug_modal_title')}
                                        </DialogTitle>
                                        <DialogDescription>
                                            {t('admin.settings.debug_modal_description')}
                                            <br />
                                            <br />
                                            <strong className="text-red-600">
                                                {t('admin.settings.debug_modal_warning')}
                                            </strong>
                                        </DialogDescription>

                                        <DialogFooter className="gap-2">
                                            <DialogClose asChild>
                                                <Button
                                                    variant="secondary"
                                                    onClick={cancelDebugEnable}
                                                >
                                                    {t('common.cancel')}
                                                </Button>
                                            </DialogClose>

                                            <Button
                                                variant="destructive"
                                                onClick={confirmDebugEnable}
                                            >
                                                {t('admin.settings.enable_debug_mode')}
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>

                                <div className="border-t pt-6">
                                    <h3 className="mb-4 text-lg font-semibold">
                                        {t('admin.settings.aws_settings')}
                                    </h3>

                                    <div className="space-y-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="aws_access_key_id">
                                                {t('admin.settings.aws_access_key_id')}
                                            </Label>

                                            <Input
                                                id="aws_access_key_id"
                                                name="aws_access_key_id"
                                                className="mt-1 block w-full"
                                                defaultValue={
                                                    awsAccessKeyId || ''
                                                }
                                                placeholder={t('admin.settings.aws_access_key_id')}
                                            />

                                            <InputError
                                                className="mt-2"
                                                message={
                                                    errors.aws_access_key_id
                                                }
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="aws_secret_access_key">
                                                {t('admin.settings.aws_secret_access_key')}
                                            </Label>

                                            <Input
                                                id="aws_secret_access_key"
                                                name="aws_secret_access_key"
                                                type="password"
                                                autoComplete="current-password"
                                                className="mt-1 block w-full"
                                                defaultValue={
                                                    awsSecretAccessKey || ''
                                                }
                                                placeholder={t('admin.settings.aws_secret_access_key')}
                                            />

                                            <InputError
                                                className="mt-2"
                                                message={
                                                    errors.aws_secret_access_key
                                                }
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="aws_default_region">
                                                {t('admin.settings.aws_default_region')}
                                            </Label>

                                            <Input
                                                id="aws_default_region"
                                                name="aws_default_region"
                                                className="mt-1 block w-full"
                                                defaultValue={
                                                    awsDefaultRegion || ''
                                                }
                                                placeholder="us-east-1"
                                            />

                                            <InputError
                                                className="mt-2"
                                                message={
                                                    errors.aws_default_region
                                                }
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="aws_bucket">
                                                {t('admin.settings.aws_bucket')}
                                            </Label>

                                            <Input
                                                id="aws_bucket"
                                                name="aws_bucket"
                                                className="mt-1 block w-full"
                                                defaultValue={awsBucket || ''}
                                                placeholder="my-bucket"
                                            />

                                            <InputError
                                                className="mt-2"
                                                message={errors.aws_bucket}
                                            />
                                        </div>

                                        <div className="flex items-center space-x-2">
                                            <input
                                                type="hidden"
                                                name="aws_use_path_style_endpoint"
                                                value="0"
                                            />
                                            <Checkbox
                                                id="aws_use_path_style_endpoint"
                                                name="aws_use_path_style_endpoint"
                                                value="1"
                                                checked={
                                                    awsUsePathStyleEndpointValue
                                                }
                                                onCheckedChange={(checked) =>
                                                    setAwsUsePathStyleEndpointValue(
                                                        checked === true,
                                                    )
                                                }
                                            />
                                            <Label
                                                htmlFor="aws_use_path_style_endpoint"
                                                className="cursor-pointer"
                                            >
                                                {t('admin.settings.use_path_style_endpoint')}
                                            </Label>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex items-center gap-4">
                                    <Button disabled={processing}>{t('common.save')}</Button>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-neutral-600">
                                            {t('common.saved')}
                                        </p>
                                    </Transition>
                                </div>
                            </div>
                        )}
                    </Form>
                </div>

                <Link
                    href={adminSettingsEnvironmentIndex().url}
                    className="group rounded-lg border bg-card p-6 shadow-sm transition-colors hover:bg-accent"
                >
                    <div className="flex items-start gap-4">
                        <div className="rounded-md bg-primary/10 p-2">
                            <FileText className="h-6 w-6 text-primary" />
                        </div>
                        <div className="flex-1">
                            <h3 className="text-lg font-semibold group-hover:text-primary">
                                {t('admin.settings.environment_config_title')}
                            </h3>
                            <p className="mt-1 text-sm text-muted-foreground">
                                {t('admin.settings.environment_config_description')}
                            </p>
                        </div>
                    </div>
                </Link>
            </div>
        </AppLayout>
    );
}
