import { createInertiaApp } from '@inertiajs/react';
import createServer from '@inertiajs/react/server';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import ReactDOMServer from 'react-dom/server';

import { Toaster } from '@/components/ui/sonner';
import { TooltipProvider } from '@/components/ui/tooltip';
import AuthLayout from '@/layouts/auth-layout';

const appName = import.meta.env.VITE_APP_NAME || 'PBM Agency';

createServer((page) =>
    createInertiaApp({
        page,
        render: ReactDOMServer.renderToString,
        title: (title) => (title ? `${title} - ${appName}` : appName),
        resolve: (name) =>
            resolvePageComponent(
                `./pages/${name}.tsx`,
                import.meta.glob('./pages/**/*.tsx'),
            ) as any,
        layout: (name) => {
            switch (true) {
                case name.startsWith('auth/'):
                    return AuthLayout;
                default:
                    return null;
            }
        },
        setup: ({ App, props }) => {
            return (
                <TooltipProvider delayDuration={0}>
                    <App {...props} />
                    <Toaster />
                </TooltipProvider>
            );
        },
    }),
);
