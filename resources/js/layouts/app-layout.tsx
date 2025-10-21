import AppLayoutTemplate from '@/layouts/app/app-sidebar-layout';
import { type BreadcrumbItem } from '@/types';
import { usePage } from '@inertiajs/react';
import { type ReactNode } from 'react';

interface AppLayoutProps {
    children: ReactNode;
}

export default ({ children, ...props }: AppLayoutProps) => {
    const {
        props: { breadcrumbs },
    } = usePage<{ breadcrumbs?: BreadcrumbItem[] }>();

    return (
        <AppLayoutTemplate breadcrumbs={breadcrumbs} {...props}>
            {children}
        </AppLayoutTemplate>
    );
};
