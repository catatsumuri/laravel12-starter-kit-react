import AppearanceTabs from '@/components/appearance-tabs';
import HeadingSmall from '@/components/heading-small';
import AppLayout from '@/layouts/app-layout';
import SettingsLayout from '@/layouts/settings/layout';
import { type BreadcrumbItem } from '@/types';
import { Head, usePage } from '@inertiajs/react';
import { useTranslation } from 'react-i18next';

export default function Appearance() {
    const { t } = useTranslation();
    const {
        props: { breadcrumbs },
    } = usePage<{ breadcrumbs?: BreadcrumbItem[] }>();

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={t('settings.appearance.head_title')} />

            <SettingsLayout>
                <div className="space-y-6">
                    <HeadingSmall
                        title={t('settings.appearance.section_title')}
                        description={t(
                            'settings.appearance.section_description',
                        )}
                    />
                    <AppearanceTabs />
                </div>
            </SettingsLayout>
        </AppLayout>
    );
}
