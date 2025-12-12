import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/variables.css',
                'resources/css/app.css',
                'resources/css/admin-users.css',
                'resources/css/admin-modern.css',
                'resources/css/dashboard.css',
                'resources/js/app.js',
                'resources/js/bootstrap.js',
                'resources/js/config/constants.js',
                'resources/js/services/ProductService.js',
                'resources/js/services/CartService.js',
                'resources/js/services/WishlistService.js',
                'resources/js/services/OrderService.js',
                'resources/js/components/cart.js',
                'resources/js/components/ProductCard.js',
                'resources/js/utils/api.js',
                'resources/js/utils/dom.js',
                'resources/js/pages/admin-users.js',
                'resources/js/pages/admin-dashboard.js',
            ],
            refresh: true,
        }),
    ],
});

