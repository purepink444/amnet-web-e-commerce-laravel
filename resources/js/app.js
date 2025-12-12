/**
 * Main Application - Vanilla JavaScript
 * Initializes and coordinates all frontend components
 */

class ECommerceApp {
    constructor() {
        this.initialized = false;
        this.components = new Map();
        this.eventListeners = [];
        this.services = {};
    }

    /**
     * Initialize the application
     */
    async init() {
        if (this.initialized) return;

        try {
            console.log('🚀 Initializing E-Commerce Application...');

            // Initialize services
            await this.initServices();

            // Initialize components
            this.initComponents();

            // Setup global event listeners
            this.setupGlobalEvents();

            // Initialize lazy loading
            this.initLazyLoading();

            // Initialize theme
            this.initTheme();

            // Mark as initialized
            this.initialized = true;

            console.log('✅ E-Commerce Application initialized successfully');

            // Dispatch app ready event
            window.dispatchEvent(new CustomEvent('app-ready', {
                detail: { app: this }
            }));

        } catch (error) {
            console.error('❌ Failed to initialize application:', error);
            this.showError('Failed to initialize application. Please refresh the page.');
        }
    }

    /**
     * Initialize services
     */
    async initServices() {
        // Services are already initialized as global objects
        this.services = {
            cart: window.CartService,
            wishlist: window.WishlistService,
            api: window.API
        };

        // Sync services with server if user is authenticated
        if (this.isAuthenticated()) {
            await Promise.all([
                this.services.cart.syncWithServer().catch(console.warn),
                this.services.wishlist.syncWithServer().catch(console.warn)
            ]);
        }
    }

    /**
     * Initialize components
     */
    initComponents() {
        // Initialize product cards
        this.initProductCards();

        // Initialize cart components
        this.initCartComponents();

        // Initialize wishlist components
        this.initWishlistComponents();

        // Initialize forms
        this.initForms();

        // Initialize navigation
        this.initNavigation();
    }

    /**
     * Initialize product cards
     */
    initProductCards() {
        const productContainers = document.querySelectorAll('[data-products]');
        productContainers.forEach(container => {
            this.initProductContainer(container);
        });
    }

    /**
     * Initialize product container
     */
    async initProductContainer(container) {
        const config = this.parseDataAttributes(container);

        try {
            DOMUtils.showLoading(container, 'Loading products...');

            // Fetch products
            let products = [];
            if (config.endpoint) {
                const response = await API.products.get(config.endpoint, config.params || {});
                products = response.data || [];
            } else if (config.products) {
                products = config.products;
            }

            // Clear loading state
            DOMUtils.hideLoading(container);

            // Render products
            this.renderProducts(container, products, config);

        } catch (error) {
            console.error('Failed to load products:', error);
            DOMUtils.hideLoading(container);
            container.innerHTML = '<div class="error-message">Failed to load products. Please try again.</div>';
        }
    }

    /**
     * Render products in container
     */
    async renderProducts(container, products, config = {}) {
        if (!products || products.length === 0) {
            container.innerHTML = '<div class="no-products">No products found.</div>';
            return;
        }

        // Ensure ProductCard component is loaded
        if (!customElements.get('product-card')) {
            try {
                await import('./components/ProductCard.js');
            } catch (error) {
                console.error('Failed to load ProductCard component:', error);
                container.innerHTML = '<div class="error">Failed to load product components.</div>';
                return;
            }
        }

        const grid = document.createElement('div');
        grid.className = `products-grid ${config.layout || 'grid-cols-1 md:grid-cols-3 lg:grid-cols-4'}`;

        products.forEach(product => {
            const card = document.createElement('product-card');
            card.setAttribute('product', JSON.stringify(product));
            card.setAttribute('compact', config.compact || false);
            card.setAttribute('show-wishlist', config.showWishlist !== false);
            grid.appendChild(card);
        });

        container.innerHTML = '';
        container.appendChild(grid);
    }

    /**
     * Initialize cart components
     */
    initCartComponents() {
        // Cart counter in header
        this.initCartCounter();

        // Cart dropdown/sidebar
        this.initCartDropdown();

        // Add to cart buttons
        this.initAddToCartButtons();
    }

    /**
     * Initialize cart counter
     */
    initCartCounter() {
        const counters = document.querySelectorAll('[data-cart-count]');
        if (counters.length === 0) return;

        const updateCounter = () => {
            const summary = this.services.cart.getSummary();
            counters.forEach(counter => {
                counter.textContent = summary.itemCount;
                counter.style.display = summary.itemCount > 0 ? 'inline' : 'none';
            });
        };

        // Initial update
        updateCounter();

        // Subscribe to cart changes
        this.services.cart.subscribe(updateCounter);
    }

    /**
     * Initialize cart dropdown
     */
    initCartDropdown() {
        const cartTriggers = document.querySelectorAll('[data-cart-toggle]');
        const cartDropdown = document.querySelector('[data-cart-dropdown]');

        if (!cartTriggers.length || !cartDropdown) return;

        cartTriggers.forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                this.toggleCartDropdown(cartDropdown);
            });
        });

        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!cartDropdown.contains(e.target) &&
                !Array.from(cartTriggers).some(trigger => trigger.contains(e.target))) {
                cartDropdown.classList.remove('open');
            }
        });
    }

    /**
     * Toggle cart dropdown
     */
    toggleCartDropdown(dropdown) {
        const isOpen = dropdown.classList.contains('open');

        if (isOpen) {
            dropdown.classList.remove('open');
        } else {
            this.renderCartDropdown(dropdown);
            dropdown.classList.add('open');
        }
    }

    /**
     * Render cart dropdown content
     */
    renderCartDropdown(dropdown) {
        const cartState = this.services.cart.getState();

        if (cartState.items.length === 0) {
            dropdown.innerHTML = `
                <div class="cart-empty">
                    <p>Your cart is empty</p>
                    <a href="/products" class="btn btn-primary">Continue Shopping</a>
                </div>
            `;
            return;
        }

        const itemsHtml = cartState.items.slice(0, 3).map(item => `
            <div class="cart-item">
                <img src="${item.product.image}" alt="${item.product.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <h4 class="cart-item-title">${item.product.name}</h4>
                    <p class="cart-item-price">฿${item.product.price} × ${item.quantity}</p>
                </div>
                <button class="cart-item-remove" data-product-id="${item.product_id}">×</button>
            </div>
        `).join('');

        dropdown.innerHTML = `
            <div class="cart-items">
                ${itemsHtml}
            </div>
            <div class="cart-footer">
                <div class="cart-total">
                    <span>Total: ฿${cartState.total.toLocaleString()}</span>
                </div>
                <div class="cart-actions">
                    <a href="/cart" class="btn btn-outline">View Cart</a>
                    <a href="/checkout" class="btn btn-primary">Checkout</a>
                </div>
            </div>
        `;

        // Add remove item listeners
        dropdown.querySelectorAll('.cart-item-remove').forEach(btn => {
            btn.addEventListener('click', async (e) => {
                e.preventDefault();
                const productId = btn.dataset.productId;
                await this.services.cart.removeItem(productId);
                this.renderCartDropdown(dropdown);
            });
        });
    }

    /**
     * Initialize add to cart buttons
     */
    initAddToCartButtons() {
        // This is handled by ProductCard components
        // Global event listener for cart updates
        window.addEventListener('cart-updated', (e) => {
            console.log('Cart updated:', e.detail);
            // Update cart counter and dropdown if needed
        });
    }

    /**
     * Initialize wishlist components
     */
    initWishlistComponents() {
        // Wishlist counter
        this.initWishlistCounter();

        // Wishlist buttons are handled by ProductCard components
        window.addEventListener('wishlist-updated', (e) => {
            console.log('Wishlist updated:', e.detail);
        });
    }

    /**
     * Initialize wishlist counter
     */
    initWishlistCounter() {
        const counters = document.querySelectorAll('[data-wishlist-count]');
        if (counters.length === 0) return;

        const updateCounter = () => {
            const count = this.services.wishlist.getCount();
            counters.forEach(counter => {
                counter.textContent = count;
                counter.style.display = count > 0 ? 'inline' : 'none';
            });
        };

        // Initial update
        updateCounter();

        // Subscribe to wishlist changes
        this.services.wishlist.subscribe(updateCounter);
    }

    /**
     * Initialize forms
     */
    initForms() {
        // Search form
        this.initSearchForm();

        // Contact forms
        this.initContactForms();

        // Newsletter signup
        this.initNewsletterForm();
    }

    /**
     * Initialize search form
     */
    initSearchForm() {
        const searchForm = document.querySelector('[data-search-form]');
        if (!searchForm) return;

        const searchInput = searchForm.querySelector('input[type="search"]');
        const searchResults = document.querySelector('[data-search-results]');

        if (!searchInput || !searchResults) return;

        let searchTimeout;

        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();

            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            searchTimeout = setTimeout(() => {
                this.performSearch(query, searchResults);
            }, 300);
        });

        // Close search results when clicking outside
        document.addEventListener('click', (e) => {
            if (!searchForm.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    }

    /**
     * Perform search
     */
    async performSearch(query, resultsContainer) {
        try {
            const response = await API.products.searchProducts(query);
            const products = response.data || [];

            if (products.length === 0) {
                resultsContainer.innerHTML = '<div class="search-no-results">No products found</div>';
            } else {
                const resultsHtml = products.slice(0, 5).map(product => `
                    <a href="/product/${product.id}" class="search-result-item">
                        <img src="${product.image}" alt="${product.name}" class="search-result-image">
                        <div class="search-result-details">
                            <div class="search-result-title">${product.name}</div>
                            <div class="search-result-price">฿${product.price.toLocaleString()}</div>
                        </div>
                    </a>
                `).join('');

                resultsContainer.innerHTML = resultsHtml;
            }

            resultsContainer.style.display = 'block';
        } catch (error) {
            console.error('Search failed:', error);
            resultsContainer.innerHTML = '<div class="search-error">Search failed. Please try again.</div>';
            resultsContainer.style.display = 'block';
        }
    }

    /**
     * Initialize contact forms
     */
    initContactForms() {
        const contactForms = document.querySelectorAll('form[data-contact-form]');
        contactForms.forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                await this.handleContactForm(form);
            });
        });
    }

    /**
     * Handle contact form submission
     */
    async handleContactForm(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Sending...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Send to API (assuming there's a contact endpoint)
            const response = await API.client.post('/contact', data);

            if (response.success) {
                this.showNotification('Message sent successfully!', 'success');
                form.reset();
            } else {
                throw new Error(response.message);
            }
        } catch (error) {
            console.error('Contact form error:', error);
            this.showNotification('Failed to send message. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    /**
     * Initialize newsletter form
     */
    initNewsletterForm() {
        const newsletterForm = document.querySelector('form[data-newsletter-form]');
        if (!newsletterForm) return;

        newsletterForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleNewsletterForm(newsletterForm);
        });
    }

    /**
     * Handle newsletter form submission
     */
    async handleNewsletterForm(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;

        try {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Subscribing...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            const response = await API.client.post('/newsletter/subscribe', data);

            if (response.success) {
                this.showNotification('Successfully subscribed to newsletter!', 'success');
                form.reset();
            } else {
                throw new Error(response.message);
            }
        } catch (error) {
            console.error('Newsletter subscription error:', error);
            this.showNotification('Failed to subscribe. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    /**
     * Initialize navigation
     */
    initNavigation() {
        // Mobile menu toggle
        this.initMobileMenu();

        // Smooth scrolling for anchor links
        this.initSmoothScrolling();

        // Active navigation highlighting
        this.initActiveNavigation();
    }

    /**
     * Initialize mobile menu
     */
    initMobileMenu() {
        const menuToggle = document.querySelector('[data-mobile-menu-toggle]');
        const mobileMenu = document.querySelector('[data-mobile-menu]');

        if (!menuToggle || !mobileMenu) return;

        menuToggle.addEventListener('click', (e) => {
            e.preventDefault();
            mobileMenu.classList.toggle('open');
            menuToggle.classList.toggle('active');
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!mobileMenu.contains(e.target) && !menuToggle.contains(e.target)) {
                mobileMenu.classList.remove('open');
                menuToggle.classList.remove('active');
            }
        });
    }

    /**
     * Initialize smooth scrolling
     */
    initSmoothScrolling() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');
        anchorLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                const targetId = link.getAttribute('href');
                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    e.preventDefault();
                    DOMUtils.scrollToElement(targetElement, 80); // Offset for fixed header
                }
            });
        });
    }

    /**
     * Initialize active navigation
     */
    initActiveNavigation() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('nav a[href]');

        navLinks.forEach(link => {
            const linkPath = link.getAttribute('href');
            if (linkPath === currentPath || (linkPath !== '/' && currentPath.startsWith(linkPath))) {
                link.classList.add('active');
            }
        });
    }

    /**
     * Setup global event listeners
     */
    setupGlobalEvents() {
        // Handle online/offline status
        window.addEventListener('online', () => {
            this.showNotification('Connection restored', 'success');
            // Sync data with server
            this.syncDataWithServer();
        });

        window.addEventListener('offline', () => {
            this.showNotification('You are offline. Some features may not work.', 'warning');
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', (e) => {
            // Handle SPA-like navigation if needed
        });

        // Handle unhandled promise rejections
        window.addEventListener('unhandledrejection', (e) => {
            console.error('Unhandled promise rejection:', e.reason);
            this.showError('An unexpected error occurred. Please refresh the page.');
        });
    }

    /**
     * Initialize lazy loading
     */
    initLazyLoading() {
        DOMUtils.lazyLoadImages();

        // Intersection Observer for other lazy loading
        if ('IntersectionObserver' in window) {
            const observerOptions = {
                root: null,
                rootMargin: '50px',
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;

                        // Add visible class for animations
                        element.classList.add('visible');

                        // Stop observing
                        observer.unobserve(element);
                    }
                });
            }, observerOptions);

            // Observe elements with animation classes
            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        }
    }

    /**
     * Initialize theme
     */
    initTheme() {
        const savedTheme = localStorage.getItem('theme') || 'light';
        this.setTheme(savedTheme);

        // Theme toggle button
        const themeToggle = document.querySelector('[data-theme-toggle]');
        if (themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                this.setTheme(newTheme);
            });
        }
    }

    /**
     * Set theme
     */
    setTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        // Update theme toggle button
        const themeToggle = document.querySelector('[data-theme-toggle]');
        if (themeToggle) {
            themeToggle.setAttribute('aria-label', `Switch to ${theme === 'light' ? 'dark' : 'light'} theme`);
        }
    }

    /**
     * Sync data with server when coming back online
     */
    async syncDataWithServer() {
        if (!this.isAuthenticated()) return;

        try {
            await Promise.all([
                this.services.cart.syncWithServer(),
                this.services.wishlist.syncWithServer()
            ]);
        } catch (error) {
            console.warn('Failed to sync data with server:', error);
        }
    }

    /**
     * Check if user is authenticated
     */
    isAuthenticated() {
        return !!localStorage.getItem('auth_token');
    }

    /**
     * Parse data attributes from element
     */
    parseDataAttributes(element) {
        const config = {};
        Array.from(element.attributes).forEach(attr => {
            if (attr.name.startsWith('data-')) {
                const key = attr.name.slice(5);
                try {
                    config[key] = JSON.parse(attr.value);
                } catch {
                    config[key] = attr.value;
                }
            }
        });
        return config;
    }

    /**
     * Show notification
     */
    showNotification(message, type = 'info') {
        if (window.Swal) {
            window.Swal.fire({
                text: message,
                icon: type,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        } else {
            // Fallback notification
            const notification = DOMUtils.createElement('div', {
                className: `notification notification-${type}`
            }, message);

            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 12px 16px;
                border-radius: 8px;
                color: white;
                background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : type === 'warning' ? '#f59e0b' : '#6b7280'};
                z-index: 1000;
                font-size: 14px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                animation: slideInRight 0.3s ease;
            `;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showNotification(message, 'error');
    }

    /**
     * Destroy application
     */
    destroy() {
        // Clean up event listeners
        this.eventListeners.forEach(cleanup => cleanup());

        // Clean up services
        Object.values(this.services).forEach(service => {
            if (service.destroy) {
                service.destroy();
            }
        });

        this.initialized = false;
    }
}

// Create and export application instance
const app = new ECommerceApp();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ECommerceApp;
}

// Make available globally
window.ECommerceApp = ECommerceApp;
window.app = app;

// Auto-initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => app.init());
} else {
    app.init();
}
