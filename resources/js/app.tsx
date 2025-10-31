import '../css/app.css';

import { createInertiaApp, router } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { StrictMode } from 'react';
import { createRoot } from 'react-dom/client';
import { toast } from 'sonner';
import { Toaster } from './components/ui/sonner';
import { initializeTheme } from './hooks/use-appearance';
import { initializeTheme, type Appearance } from './hooks/use-appearance';

declare global {
    interface Window {
        __FEATURE_FLAGS__?: {
            defaultAppearance: Appearance;
            appearanceSettings: boolean;
        };
    }
}

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

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
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.tsx`,
            import.meta.glob('./pages/**/*.tsx'),
        ),
    setup({ el, App, props }) {
        const root = createRoot(el);

        root.render(
            <StrictMode>
                <App {...props} />
                <Toaster />
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
