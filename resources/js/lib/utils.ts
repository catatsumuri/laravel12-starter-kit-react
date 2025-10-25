import { type ClassValue, clsx } from 'clsx';
import { format, formatDistanceToNow } from 'date-fns';
import { ja } from 'date-fns/locale';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

/**
 * Format a date string to a localized format
 * @param dateString - ISO 8601 date string
 * @param formatString - Format pattern
 * @returns Formatted date string
 */
export function formatDateTime(
    dateString: string | null | undefined,
    formatString: string = 'yyyy/M/d HH:mm',
): string {
    if (!dateString) return '';
    return format(new Date(dateString), formatString, { locale: ja });
}

/**
 * Format a date string to relative time
 * @param dateString - ISO 8601 date string
 * @returns Relative time string
 */
export function formatRelativeTime(
    dateString: string | null | undefined,
): string {
    if (!dateString) return '';
    return formatDistanceToNow(new Date(dateString), {
        addSuffix: true,
        locale: ja,
    });
}
