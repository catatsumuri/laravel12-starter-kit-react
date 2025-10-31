import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { show } from '@/routes/admin/users';
import { type ActivityLog, type BreadcrumbItem, type User } from '@/types';
import { Head, Link, router } from '@inertiajs/react';
import { Activity, ArrowLeft, Clock, FileText } from 'lucide-react';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginatedActivities {
    data: ActivityLog[];
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    links: PaginationLink[];
}

interface Props {
    user: User;
    activities: PaginatedActivities;
}

export default function Activities({ user, activities }: Props) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Users',
            href: '/admin/users',
        },
        {
            title: user.name,
            href: show(user).url,
        },
        {
            title: 'Activity Log',
            href: `/admin/users/${user.id}/activities`,
        },
    ];

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
            <Head title={`Activity Log - ${user.name}`} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <div className="flex items-center gap-4">
                        <Button variant="outline" size="sm" asChild>
                            <Link href={show(user)}>
                                <ArrowLeft className="size-4" />
                            </Link>
                        </Button>
                        <div>
                            <h1 className="text-2xl font-semibold">
                                Activity Log
                            </h1>
                            <p className="text-sm text-muted-foreground">
                                Actions performed by {user.name}
                            </p>
                        </div>
                    </div>
                </div>

                <div className="rounded-lg border bg-card">
                    <div className="border-b bg-muted/50 p-4">
                        <div className="flex items-center justify-between">
                            <div className="flex items-center gap-2">
                                <Activity className="size-5" />
                                <span className="font-semibold">
                                    All Activities
                                </span>
                            </div>
                            <span className="text-sm text-muted-foreground">
                                {activities.total}{' '}
                                {activities.total === 1 ? 'entry' : 'entries'}
                            </span>
                        </div>
                    </div>

                    <div className="divide-y">
                        {activities.data.length > 0 ? (
                            activities.data.map((activity) => (
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

                                            {activity.properties && (
                                                <div className="rounded-md border bg-muted/50 p-3 text-sm">
                                                    <div className="grid gap-3 md:grid-cols-2">
                                                        {activity.properties
                                                            .old && (
                                                            <div className="space-y-1">
                                                                <p className="text-xs font-semibold text-destructive uppercase">
                                                                    Old Values
                                                                </p>
                                                                <div className="space-y-1 rounded bg-background p-2">
                                                                    {Object.entries(
                                                                        activity
                                                                            .properties
                                                                            .old,
                                                                    ).map(
                                                                        ([
                                                                            key,
                                                                            value,
                                                                        ]) => (
                                                                            <div
                                                                                key={
                                                                                    key
                                                                                }
                                                                                className="flex items-start gap-2"
                                                                            >
                                                                                <span className="font-medium text-muted-foreground">
                                                                                    {
                                                                                        key
                                                                                    }
                                                                                    :
                                                                                </span>
                                                                                <span className="flex-1 break-all">
                                                                                    {String(
                                                                                        value,
                                                                                    )}
                                                                                </span>
                                                                            </div>
                                                                        ),
                                                                    )}
                                                                </div>
                                                            </div>
                                                        )}
                                                        {activity.properties
                                                            .attributes && (
                                                            <div className="space-y-1">
                                                                <p className="text-xs font-semibold text-green-600 uppercase">
                                                                    {activity
                                                                        .properties
                                                                        .old
                                                                        ? 'New Values'
                                                                        : 'Values'}
                                                                </p>
                                                                <div className="space-y-1 rounded bg-background p-2">
                                                                    {Object.entries(
                                                                        activity
                                                                            .properties
                                                                            .attributes,
                                                                    ).map(
                                                                        ([
                                                                            key,
                                                                            value,
                                                                        ]) => (
                                                                            <div
                                                                                key={
                                                                                    key
                                                                                }
                                                                                className="flex items-start gap-2"
                                                                            >
                                                                                <span className="font-medium text-muted-foreground">
                                                                                    {
                                                                                        key
                                                                                    }
                                                                                    :
                                                                                </span>
                                                                                <span className="flex-1 break-all">
                                                                                    {String(
                                                                                        value,
                                                                                    )}
                                                                                </span>
                                                                            </div>
                                                                        ),
                                                                    )}
                                                                </div>
                                                            </div>
                                                        )}
                                                    </div>
                                                </div>
                                            )}
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
                                    This user has not performed any actions yet.
                                </p>
                            </div>
                        )}
                    </div>
                </div>

                {activities.last_page > 1 && (
                    <div className="flex items-center justify-center gap-1">
                        {activities.links.map((link, index) => (
                            <Button
                                key={index}
                                variant={link.active ? 'default' : 'outline'}
                                size="sm"
                                disabled={!link.url}
                                onClick={() => link.url && router.get(link.url)}
                                dangerouslySetInnerHTML={{ __html: link.label }}
                            />
                        ))}
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
