import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin-users.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/components/cart.js',
                'resources/js/components/product.js',
                'resources/js/utils/address-selector.js',
                'resources/js/pages/payment.js',
                'resources/js/pages/checkout.js',
                'resources/js/pages/admin-dashboard.js',
                'resources/js/pages/admin-users.js',
                'resources/js/modules/shared/notifications.js',
            ],
            refresh: true,
        }),
    ],
});