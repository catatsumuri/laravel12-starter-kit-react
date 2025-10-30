import { Head } from '@inertiajs/react';

import HeadingSmall from '@/components/heading-small';
import AppLayout from '@/layouts/app-layout';
import { index as adminSettingsIndex } from '@/routes/admin/settings';
import { index as adminSettingsEnvironmentIndex } from '@/routes/admin/settings/environment';
import { type BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin',
        href: adminSettingsIndex().url,
    },
    {
        title: 'Settings',
        href: adminSettingsIndex().url,
    },
    {
        title: 'Environment',
        href: adminSettingsEnvironmentIndex().url,
    },
];

interface EnvironmentIndexProps {
    envVars: Record<string, string | boolean | null>;
    configVars: Record<string, string | boolean | null>;
    dbSettings: Record<string, string | boolean | null>;
}

function formatValue(value: string | boolean | null): string {
    if (value === null || value === undefined) {
        return '-';
    }
    if (typeof value === 'boolean') {
        return value ? 'true' : 'false';
    }
    return String(value);
}

interface SettingRow {
    name: string;
    envKey: string;
    configKey: string;
    dbKey: string;
}

const settingRows: SettingRow[] = [
    {
        name: 'APP_NAME',
        envKey: 'APP_NAME',
        configKey: 'app.name',
        dbKey: 'app.name',
    },
    {
        name: 'APP_ENV',
        envKey: 'APP_ENV',
        configKey: 'app.env',
        dbKey: '',
    },
    {
        name: 'APP_DEBUG',
        envKey: 'APP_DEBUG',
        configKey: 'app.debug',
        dbKey: 'app.debug',
    },
    {
        name: 'APP_URL',
        envKey: 'APP_URL',
        configKey: 'app.url',
        dbKey: 'app.url',
    },
    {
        name: 'APP_LOCALE',
        envKey: 'APP_LOCALE',
        configKey: 'app.locale',
        dbKey: 'app.locale',
    },
    {
        name: 'APP_FALLBACK_LOCALE',
        envKey: 'APP_FALLBACK_LOCALE',
        configKey: 'app.fallback_locale',
        dbKey: 'app.fallback_locale',
    },
    {
        name: 'APP_FAKER_LOCALE',
        envKey: 'APP_FAKER_LOCALE',
        configKey: 'app.faker_locale',
        dbKey: 'app.faker_locale',
    },
    {
        name: 'AWS_ACCESS_KEY_ID',
        envKey: 'AWS_ACCESS_KEY_ID',
        configKey: 'filesystems.disks.s3.key',
        dbKey: 'aws.access_key_id',
    },
    {
        name: 'AWS_SECRET_ACCESS_KEY',
        envKey: 'AWS_SECRET_ACCESS_KEY',
        configKey: 'filesystems.disks.s3.secret',
        dbKey: 'aws.secret_access_key',
    },
    {
        name: 'AWS_DEFAULT_REGION',
        envKey: 'AWS_DEFAULT_REGION',
        configKey: 'filesystems.disks.s3.region',
        dbKey: 'aws.default_region',
    },
    {
        name: 'AWS_BUCKET',
        envKey: 'AWS_BUCKET',
        configKey: 'filesystems.disks.s3.bucket',
        dbKey: 'aws.bucket',
    },
    {
        name: 'AWS_USE_PATH_STYLE_ENDPOINT',
        envKey: 'AWS_USE_PATH_STYLE_ENDPOINT',
        configKey: 'filesystems.disks.s3.use_path_style_endpoint',
        dbKey: 'aws.use_path_style_endpoint',
    },
];

export default function EnvironmentIndex({
    envVars,
    configVars,
    dbSettings,
}: EnvironmentIndexProps) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Environment & Configuration" />

            <div className="flex h-full flex-1 flex-col gap-6 overflow-x-auto rounded-xl p-6">
                <HeadingSmall
                    title="Environment & Configuration"
                    description="View environment variables, configuration values, and database settings"
                />

                <div className="rounded-lg border bg-card p-6 shadow-sm">
                    <div className="overflow-x-auto">
                        <table className="w-full text-sm">
                            <thead className="border-b">
                                <tr>
                                    <th className="pr-6 pb-3 text-left font-medium">
                                        Setting
                                    </th>
                                    <th className="px-8 pb-3 text-left font-semibold">
                                        Config
                                    </th>
                                    <th className="px-6 pb-3 text-left font-medium text-muted-foreground">
                                        .env
                                    </th>
                                    <th className="pb-3 pl-6 text-left font-medium text-muted-foreground">
                                        DB
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="divide-y">
                                {settingRows.map((row) => (
                                    <tr key={row.name} className="group">
                                        <td className="py-3 pr-6 font-mono text-xs font-semibold">
                                            {row.name}
                                        </td>
                                        <td className="px-8 py-3 font-mono text-base font-bold">
                                            {formatValue(
                                                configVars[row.configKey],
                                            )}
                                        </td>
                                        <td className="px-6 py-3 font-mono text-xs text-muted-foreground">
                                            {formatValue(envVars[row.envKey])}
                                        </td>
                                        <td className="py-3 pl-6 font-mono text-xs text-muted-foreground">
                                            {row.dbKey
                                                ? formatValue(
                                                      dbSettings[row.dbKey],
                                                  )
                                                : '-'}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    <div className="mt-6 rounded-md bg-muted p-4 text-sm text-muted-foreground">
                        <p className="font-semibold">Description:</p>
                        <ul className="mt-2 list-inside list-disc space-y-1">
                            <li>
                                <strong>Config</strong>: Current runtime value
                                used by the application
                            </li>
                            <li>
                                <strong>Setting</strong>: Configuration key name
                            </li>
                            <li>
                                <strong>.env</strong>: Default value defined in
                                environment file
                            </li>
                            <li>
                                <strong>DB</strong>: Override value stored in
                                database
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
