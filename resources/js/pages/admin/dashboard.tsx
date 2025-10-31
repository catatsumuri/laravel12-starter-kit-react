import { PlaceholderPattern } from '@/components/ui/placeholder-pattern';
import AppLayout from '@/layouts/app-layout';
import admin from '@/routes/admin';
import { type ActivityLog, type BreadcrumbItem } from '@/types';
import { Head, InfiniteScroll } from '@inertiajs/react';
import { Activity, Clock, FileText, User } from 'lucide-react';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Admin Dashboard',
        href: admin.dashboard.url(),
    },
];

interface PaginatedActivities {
    data: ActivityLog[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
}

interface Props {
    recentActivities: PaginatedActivities;
}

export default function AdminDashboard({ recentActivities }: Props) {
    const getActionBadgeColor = (description: string) => {
        switch (description) {
            case 'created':
                return 'bg-green-100 text-green-700 border-green-200';
            case 'updated':
                return 'bg-blue-100 text-blue-700 border-blue-200';
            case 'deleted':
                return 'bg-red-100 text-red-700 border-red-200';
            default:
                return 'bg-gray-100 text-gray-700 border-gray-200';
        }
    };

    const truncate = (str: string, maxLength: number): string => {
        if (str.length <= maxLength) return str;
        return str.substring(0, maxLength) + '...';
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Admin Dashboard" />
            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="mb-4">
                    <h1 className="text-2xl font-bold">Admin Dashboard</h1>
                    <p className="text-muted-foreground">
                        Welcome to the administrator dashboard
                    </p>
                </div>
                <div className="grid auto-rows-min gap-4 md:grid-cols-3">
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                    <div className="relative aspect-video overflow-hidden rounded-xl border border-sidebar-border/70 dark:border-sidebar-border">
                        <PlaceholderPattern className="absolute inset-0 size-full stroke-neutral-900/20 dark:stroke-neutral-100/20" />
                    </div>
                </div>

                {/* Recent Activity Log Section */}
                <div className="rounded-lg border bg-card">
                    <div className="border-b bg-muted/50 p-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <Activity className="size-5" />
                                <span className="font-semibold">
                                    Recent Activity
                                </span>
                            </div>
                            <span className="text-sm text-muted-foreground">
                                {recentActivities.total}{' '}
                                {recentActivities.total === 1
                                    ? 'entry'
                                    : 'entries'}
                            </span>
                        </div>
                    </div>

                    <InfiniteScroll
                        data="recentActivities"
                        manual
                        previous={({ loading, fetch, hasMore }) =>
                            hasMore && (
                                <button onClick={fetch}>
                                    {loading ? 'Loading...' : 'Load previous'}
                                </button>
                            )
                        }
                        next={({ loading, fetch, hasMore }) =>
                            hasMore && (
                                <button onClick={fetch}>
                                    {loading ? 'Loading...' : 'Load more'}
                                </button>
                            )
                        }
                    >
                        <div className="divide-y">
                            {recentActivities.data.length > 0 ? (
                                recentActivities.data.map((activity) => (
                                    <div
                                        key={activity.id}
                                        className="p-4 hover:bg-muted/30"
                                    >
                                        <div className="flex gap-4">
                                            <div className="flex-shrink-0">
                                                <div className="flex size-10 items-center justify-center rounded-full bg-primary/10">
                                                    <Activity className="size-5 text-primary" />
                                                </div>
                                            </div>
                                            <div className="flex-1 space-y-2">
                                                <div className="flex flex-wrap items-center gap-2">
                                                    <span className="font-mono text-xs text-muted-foreground">
                                                        #{activity.id}
                                                    </span>
                                                    <span
                                                        className={`inline-flex items-center rounded-md border px-2.5 py-0.5 text-xs font-semibold ${getActionBadgeColor(
                                                            activity.description,
                                                        )}`}
                                                    >
                                                        {activity.description.toUpperCase()}
                                                    </span>
                                                    <div className="flex items-center gap-1 text-xs text-muted-foreground">
                                                        <Clock className="size-3" />
                                                        {new Date(
                                                            activity.created_at,
                                                        ).toLocaleString()}
                                                    </div>
                                                    <div className="flex items-center gap-1 text-xs text-muted-foreground">
                                                        <User className="size-3" />
                                                        <span>
                                                            by{' '}
                                                            {activity.causer
                                                                ?.name ||
                                                                'System'}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div className="flex items-center gap-2 text-sm text-muted-foreground">
                                                    <FileText className="size-4" />
                                                    <span>
                                                        Target:{' '}
                                                        <span className="font-medium">
                                                            {activity.subject
                                                                ?.type ||
                                                                activity.subject_type}
                                                        </span>{' '}
                                                        #
                                                        {activity.subject?.id ||
                                                            activity.subject_id}
                                                        {activity.subject_label && (
                                                            <span className="text-foreground">
                                                                {' '}
                                                                -{' '}
                                                                {truncate(
                                                                    activity.subject_label,
                                                                    50,
                                                                )}
                                                            </span>
                                                        )}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="flex flex-col items-center justify-center py-12 text-center">
                                    <Activity className="size-12 text-muted-foreground/50" />
                                    <p className="mt-4 text-sm font-medium">
                                        No activity logs found
                                    </p>
                                    <p className="mt-1 text-sm text-muted-foreground">
                                        No actions have been recorded yet.
                                    </p>
                                </div>
                            )}
                        </div>
                    </InfiniteScroll>
                </div>
            </div>
        </AppLayout>
    );
}
