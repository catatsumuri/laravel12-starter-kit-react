import { usePage } from '@inertiajs/react';
import { useCallback, useEffect, useState } from 'react';

export type Appearance = 'light' | 'dark' | 'system';

const prefersDark = () => {
    if (typeof window === 'undefined') {
        return false;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches;
};

const setCookie = (name: string, value: string, days = 365) => {
    if (typeof document === 'undefined') {
        return;
    }

    const maxAge = days * 24 * 60 * 60;
    document.cookie = `${name}=${value};path=/;max-age=${maxAge};SameSite=Lax`;
};

const applyTheme = (appearance: Appearance) => {
    const isDark =
        appearance === 'dark' || (appearance === 'system' && prefersDark());

    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
};

const mediaQuery = () => {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.matchMedia('(prefers-color-scheme: dark)');
};

const handleSystemThemeChange = (
    defaultAppearance: Appearance = 'system',
    appearanceSettingsEnabled: boolean = true,
) => {
    if (!appearanceSettingsEnabled) {
        applyTheme(defaultAppearance);
    } else {
        const currentAppearance = localStorage.getItem(
            'appearance',
        ) as Appearance;
        applyTheme(currentAppearance || defaultAppearance);
    }
};

export function initializeTheme(
    defaultAppearance: Appearance = 'system',
    appearanceSettingsEnabled: boolean = true,
) {
    // If appearance settings are disabled, always use default and ignore user preferences
    const savedAppearance = appearanceSettingsEnabled
        ? (localStorage.getItem('appearance') as Appearance) ||
          defaultAppearance
        : defaultAppearance;

    applyTheme(savedAppearance);

    // Add the event listener for system theme changes...
    mediaQuery()?.addEventListener('change', () =>
        handleSystemThemeChange(defaultAppearance, appearanceSettingsEnabled),
    );
}

export function useAppearance() {
    // Try to get Inertia page props, but handle case when outside Inertia context
    let defaultAppearance: Appearance = 'system';
    let appearanceSettingsEnabled = true;

    try {
        const { features } = usePage().props;
        defaultAppearance = features.defaultAppearance || 'system';
        appearanceSettingsEnabled = features.appearanceSettings ?? true;
    } catch (error) {
        // Not in Inertia context, use defaults or window feature flags
        const featureFlags =
            typeof window !== 'undefined'
                ? window.__FEATURE_FLAGS__
                : undefined;
        if (featureFlags) {
            defaultAppearance = featureFlags.defaultAppearance || 'system';
            appearanceSettingsEnabled = featureFlags.appearanceSettings ?? true;
        }
    }

    const [appearance, setAppearance] = useState<Appearance>(defaultAppearance);

    const updateAppearance = useCallback(
        (mode: Appearance) => {
            setAppearance(mode);

            // Only store preferences if appearance settings are enabled
            if (appearanceSettingsEnabled) {
                // Store in localStorage for client-side persistence...
                localStorage.setItem('appearance', mode);

                // Store in cookie for SSR...
                setCookie('appearance', mode);
            }

            applyTheme(mode);
        },
        [appearanceSettingsEnabled],
    );

    useEffect(() => {
        // If appearance settings are disabled, always use default and ignore saved preferences
        const savedAppearance = appearanceSettingsEnabled
            ? (localStorage.getItem('appearance') as Appearance | null)
            : null;

        updateAppearance(savedAppearance || defaultAppearance);

        const listener = () =>
            handleSystemThemeChange(
                defaultAppearance,
                appearanceSettingsEnabled,
            );
        mediaQuery()?.addEventListener('change', listener);

        return () => mediaQuery()?.removeEventListener('change', listener);
    }, [updateAppearance, defaultAppearance, appearanceSettingsEnabled]);

    return { appearance, updateAppearance } as const;
}
