import { InertiaLinkProps } from '@inertiajs/react';
import { LucideIcon } from 'lucide-react';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    url: string;
    current?: boolean;
}

export interface NavGroup {
    title: string;
    items: NavItem[];
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon | null;
    isActive?: boolean;
}

export interface Features {
    twoFactorAuthentication: boolean;
    appearanceSettings: boolean;
    defaultAppearance: 'light' | 'dark' | 'system';
    registration: boolean;
    accountDeletion: boolean;
}

export interface SharedData {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    features: Features;
    sidebarOpen: boolean;
    breadcrumbs?: BreadcrumbItem[];
    notifications?: Notification[];
    [key: string]: unknown;
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    last_login_at?: string | null;
    two_factor_enabled?: boolean;
    created_at: string;
    updated_at: string;
    [key: string]: unknown; // This allows for additional properties...
}

export interface Notification {
    id: string;
    type: 'success' | 'error' | 'warning' | 'info';
    title: string;
    message: string;
    time: string;
    read: boolean;
}

export interface ActivityLogCauser {
    id: number;
    name: string;
    email: string;
}

export interface ActivityLogSubject {
    type: string;
    id: number;
    name?: string;
    email?: string;
}

export interface ActivityLog {
    id: number;
    description: string;
    properties: {
        attributes?: Record<string, unknown>;
        old?: Record<string, unknown>;
    };
    created_at: string;
    subject_type: string;
    subject_id: number;
    subject: ActivityLogSubject | null;
    subject_label?: string | null;
    causer?: ActivityLogCauser | null;
}
