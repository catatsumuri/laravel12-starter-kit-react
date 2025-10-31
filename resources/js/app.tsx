import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { toast } from 'sonner';
import { Toaster } from './components/ui/sonner';
import { initializeTheme, type Appearance } from './hooks/use-appearance';
import { configureI18n } from './lib/i18n';
import { type SharedData } from './types';

declare global {
    interface Window {
        __FEATURE_FLAGS__?: {
            defaultAppearance: Appearance;
            appearanceSettings: boolean;
        };
    }
}

const initialDocumentTitle =
    typeof document !== 'undefined'
        ? document.querySelector('title[inertia]')?.textContent?.trim()
        : null;
const defaultAppName =
    initialDocumentTitle || import.meta.env.VITE_APP_NAME || 'Laravel';

type LocalizationProps = {
    locale?: string;
    fallbackLocale?: string;
    translations?: Record<string, unknown>;
    fallbackTranslations?: Record<string, unknown>;
};

const resolveAppName = (props: unknown): string | null => {
    if (!props || typeof props !== 'object') {
        return null;
    }

    const name = (props as Partial<SharedData>).name;

    return typeof name === 'string' && name.length > 0 ? name : null;
};

let currentAppName = defaultAppName;

const applyLocalization = (props: unknown) => {
    if (!props || typeof props !== 'object') {
        return;
    }

    const data = props as Partial<SharedData & LocalizationProps>;
    const locale =
        typeof data.locale === 'string' && data.locale.length > 0
            ? data.locale
            : null;

    if (!locale) {
        return;
    }

    const fallbackLocale =
        typeof data.fallbackLocale === 'string' &&
        data.fallbackLocale.length > 0
            ? data.fallbackLocale
            : locale;

    configureI18n(
        locale,
        fallbackLocale,
        data.translations as Record<string, unknown>,
        data.fallbackTranslations as Record<string, unknown>,
    );
};

router.on('navigate', (event) => {
    const nextName = resolveAppName(event.detail.page.props);

    if (nextName) {
        currentAppName = nextName;
    }

    applyLocalization(event.detail.page.props);
});

// Flash message handler
function handleFlashMessages(flash: any) {
    if (!flash) return;

    if (flash.success) {
        toast.success(flash.success);
    }
    if (flash.error) {
        toast.error(flash.error);
    }
    if (flash.info) {
        toast.info(flash.info);
    }
    if (flash.warning) {
        toast.warning(flash.warning);
    }
    if (flash.status) {
        toast.success(flash.status);
    }
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${currentAppName}` : currentAppName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.tsx`,
            import.meta.glob('./pages/**/*.tsx'),
        ),
    setup({ el, App, props }) {
        const resolved = resolveAppName(props.initialPage.props);

        if (resolved) {
            currentAppName = resolved;
        }

        applyLocalization(props.initialPage.props);

        const root = createRoot(el);

        // Wrap App with Toaster inside
        const AppWithToaster = () => (
            <>
                <App {...props} />
                <Toaster />
            </>
        );

        root.render(
            <StrictMode>
                <AppWithToaster />
            </StrictMode>,
        );

        // Handle initial page load flash messages
        const initialFlash = props.initialPage?.props?.flash;
        if (initialFlash) {
            // Delay to ensure toast is rendered
            setTimeout(() => handleFlashMessages(initialFlash), 100);
        }
    },
    progress: {
        color: '#4B5563',
    },
});

// Handle flash messages on navigation
router.on('success', (event) => {
    if (event.detail?.page?.props?.flash) {
        handleFlashMessages(event.detail.page.props.flash);
    }
});

// This will set light / dark mode on load...
const featureFlags = window.__FEATURE_FLAGS__ || {
    defaultAppearance: 'system' as Appearance,
    appearanceSettings: true,
};
initializeTheme(
    featureFlags.defaultAppearance,
    featureFlags.appearanceSettings,
);
