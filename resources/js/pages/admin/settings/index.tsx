import { Transition } from '@headlessui/react';
import { Form, Head, Link } from '@inertiajs/react';
import { useState } from 'react';

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
import {
    index as adminSettingsIndex,
    update as adminSettingsUpdate,
} from '@/routes/admin/settings';
import { index as adminSettingsEnvironmentIndex } from '@/routes/admin/settings/environment';
import { type BreadcrumbItem } from '@/types';
import { AlertTriangle, FileText } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: adminSettingsIndex().url,
    },
    {
        title: 'Settings',
        href: adminSettingsIndex().url,
    },
];

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
            <Head title="Admin Settings" />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                <HeadingSmall
                    title="Admin Settings"
                    description="Manage your application settings"
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
                                        Application Settings
                                    </h3>

                                    <div className="space-y-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="app_name">
                                                Application Name
                                            </Label>

                                            <Input
                                                id="app_name"
                                                name="app_name"
                                                className="mt-1 block w-full"
                                                defaultValue={appName}
                                                required
                                                placeholder="Application Name"
                                            />

                                            <InputError
                                                className="mt-2"
                                                message={errors.app_name}
                                            />
                                        </div>

                                        <div className="grid gap-2">
                                            <Label htmlFor="app_url">
                                                Application URL
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
                                                Locale
                                            </Label>

                                            <Select
                                                name="app_locale"
                                                defaultValue={appLocale}
                                            >
                                                <SelectTrigger id="app_locale">
                                                    <SelectValue placeholder="Select locale" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="ja">
                                                        Japanese (ja)
                                                    </SelectItem>
                                                    <SelectItem value="en">
                                                        English (en)
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
                                                Fallback Locale
                                            </Label>

                                            <Select
                                                name="app_fallback_locale"
                                                defaultValue={appFallbackLocale}
                                            >
                                                <SelectTrigger id="app_fallback_locale">
                                                    <SelectValue placeholder="Select fallback locale" />
                                                </SelectTrigger>
                                                <SelectContent>
                                                    <SelectItem value="ja">
                                                        Japanese (ja)
                                                    </SelectItem>
                                                    <SelectItem value="en">
                                                        English (en)
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
                                        Debug Settings
                                    </h3>

                                    <div className="space-y-4 rounded-lg border border-yellow-100 bg-yellow-50 p-4 dark:border-yellow-200/10 dark:bg-yellow-700/10">
                                        <div className="relative space-y-0.5 text-yellow-600 dark:text-yellow-100">
                                            <p className="font-medium">
                                                Warning
                                            </p>
                                            <p className="text-sm">
                                                Enable debug mode to display
                                                detailed error messages. Only
                                                use in development environments.
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
                                                Enable Debug Mode
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
                                            Enable Debug Mode?
                                        </DialogTitle>
                                        <DialogDescription>
                                            Enabling debug mode will display
                                            detailed error messages and stack
                                            traces. This should only be enabled
                                            in development environments.
                                            <br />
                                            <br />
                                            <strong className="text-red-600">
                                                Warning: Never enable debug mode
                                                in production as it can expose
                                                sensitive information.
                                            </strong>
                                        </DialogDescription>

                                        <DialogFooter className="gap-2">
                                            <DialogClose asChild>
                                                <Button
                                                    variant="secondary"
                                                    onClick={cancelDebugEnable}
                                                >
                                                    Cancel
                                                </Button>
                                            </DialogClose>

                                            <Button
                                                variant="destructive"
                                                onClick={confirmDebugEnable}
                                            >
                                                Enable Debug Mode
                                            </Button>
                                        </DialogFooter>
                                    </DialogContent>
                                </Dialog>

                                <div className="border-t pt-6">
                                    <h3 className="mb-4 text-lg font-semibold">
                                        AWS Settings
                                    </h3>

                                    <div className="space-y-4">
                                        <div className="grid gap-2">
                                            <Label htmlFor="aws_access_key_id">
                                                AWS Access Key ID
                                            </Label>

                                            <Input
                                                id="aws_access_key_id"
                                                name="aws_access_key_id"
                                                className="mt-1 block w-full"
                                                defaultValue={
                                                    awsAccessKeyId || ''
                                                }
                                                placeholder="AWS Access Key ID"
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
                                                AWS Secret Access Key
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
                                                placeholder="AWS Secret Access Key"
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
                                                AWS Default Region
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
                                                AWS Bucket
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
                                                Use Path Style Endpoint
                                            </Label>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex items-center gap-4">
                                    <Button disabled={processing}>Save</Button>

                                    <Transition
                                        show={recentlySuccessful}
                                        enter="transition ease-in-out"
                                        enterFrom="opacity-0"
                                        leave="transition ease-in-out"
                                        leaveTo="opacity-0"
                                    >
                                        <p className="text-sm text-neutral-600">
                                            Saved
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
                                Environment & Configuration
                            </h3>
                            <p className="mt-1 text-sm text-muted-foreground">
                                View environment variables, configuration
                                values, and database settings
                            </p>
                        </div>
                    </div>
                </Link>
            </div>
        </AppLayout>
    );
}
