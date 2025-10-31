import { InertiaLinkProps } from '@inertiajs/react';
import { type ClassValue, clsx } from 'clsx';
import { format, formatDistanceToNow } from 'date-fns';
import { ja } from 'date-fns/locale';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function formatDateTime(
    dateString: string | null | undefined,
    formatString: string = 'yyyy/M/d HH:mm',
): string {
    if (!dateString) return '';
    return format(new Date(dateString), formatString, { locale: ja });
}

export function formatRelativeTime(
    dateString: string | null | undefined,
): string {
    if (!dateString) return '';
    return formatDistanceToNow(new Date(dateString), {
        addSuffix: true,
        locale: ja,
    });
}

export function isSameUrl(
    url1: NonNullable<InertiaLinkProps['href']>,
    url2: NonNullable<InertiaLinkProps['href']>,
) {
    return resolveUrl(url1) === resolveUrl(url2);
}

export function resolveUrl(url: NonNullable<InertiaLinkProps['href']>): string {
    return typeof url === 'string' ? url : url.url;
}
