/**
 * CartService - Vanilla JavaScript
 * Handles shopping cart operations with localStorage persistence
 */
class CartService {
    constructor() {
        this.storageKey = 'shopping_cart';
        this.apiBaseUrl = '/api/v1';
        this.cart = this.loadFromStorage();
        this.listeners = [];
    }

    /**
     * Subscribe to cart changes
     */
    subscribe(listener) {
        this.listeners.push(listener);
        return () => {
            this.listeners = this.listeners.filter(l => l !== listener);
        };
    }

    /**
     * Notify all listeners
     */
    notify() {
        this.listeners.forEach(listener => listener(this.getState()));
    }

    /**
     * Get current cart state
     */
    getState() {
        return {
            items: [...this.cart.items],
            total: this.cart.total,
            itemCount: this.getItemCount()
        };
    }

    /**
     * Get total item count
     */
    getItemCount() {
        return this.cart.items.reduce((sum, item) => sum + item.quantity, 0);
    }

    /**
     * Add item to cart
     */
    async addItem(productId, quantity = 1) {
        try {
            // First, try to add via API
            const response = await this.makeRequest('/cart/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            if (response.success) {
                // Update local cart state
                await this.syncWithServer();
                this.notify();
                return { success: true };
            }

            return { success: false, error: response.message || 'Failed to add item' };
        } catch (error) {
            console.error('CartService.addItem error:', error);

            // Fallback to local storage if API fails
            this.addItemLocally(productId, quantity);
            return { success: true, fallback: true };
        }
    }

    /**
     * Add item locally (fallback)
     */
    addItemLocally(productId, quantity) {
        const existingItem = this.cart.items.find(item => item.product_id === productId);

        if (existingItem) {
            existingItem.quantity += quantity;
        } else {
            this.cart.items.push({
                product_id: productId,
                quantity: quantity,
                added_at: new Date().toISOString()
            });
        }

        this.updateTotal();
        this.saveToStorage();
        this.notify();
    }

    /**
     * Update item quantity
     */
    async updateItem(productId, quantity) {
        try {
            const response = await this.makeRequest('/cart/update', {
                method: 'PATCH',
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            });

            if (response.success) {
                await this.syncWithServer();
                this.notify();
                return { success: true };
            }

            return { success: false, error: response.message };
        } catch (error) {
            console.error('CartService.updateItem error:', error);
            this.updateItemLocally(productId, quantity);
            return { success: true, fallback: true };
        }
    }

    /**
     * Update item locally
     */
    updateItemLocally(productId, quantity) {
        const item = this.cart.items.find(item => item.product_id === productId);
        if (item) {
            item.quantity = Math.max(0, quantity);
            if (item.quantity === 0) {
                this.removeItemLocally(productId);
            } else {
                this.updateTotal();
                this.saveToStorage();
                this.notify();
            }
        }
    }

    /**
     * Remove item from cart
     */
    async removeItem(productId) {
        try {
            const response = await this.makeRequest('/cart/remove', {
                method: 'DELETE',
                body: JSON.stringify({
                    product_id: productId
                })
            });

            if (response.success) {
                await this.syncWithServer();
                this.notify();
                return { success: true };
            }

            return { success: false, error: response.message };
        } catch (error) {
            console.error('CartService.removeItem error:', error);
            this.removeItemLocally(productId);
            return { success: true, fallback: true };
        }
    }

    /**
     * Remove item locally
     */
    removeItemLocally(productId) {
        this.cart.items = this.cart.items.filter(item => item.product_id !== productId);
        this.updateTotal();
        this.saveToStorage();
        this.notify();
    }

    /**
     * Clear cart
     */
    async clearCart() {
        try {
            const response = await this.makeRequest('/cart/clear', {
                method: 'DELETE'
            });

            if (response.success) {
                this.cart = { items: [], total: 0 };
                this.saveToStorage();
                this.notify();
                return { success: true };
            }

            return { success: false, error: response.message };
        } catch (error) {
            console.error('CartService.clearCart error:', error);
            this.cart = { items: [], total: 0 };
            this.saveToStorage();
            this.notify();
            return { success: true, fallback: true };
        }
    }

    /**
     * Sync cart with server
     */
    async syncWithServer() {
        try {
            const response = await this.makeRequest('/cart');
            if (response.success && response.data) {
                this.cart = {
                    items: response.data.items || [],
                    total: response.data.total || 0
                };
                this.saveToStorage();
            }
        } catch (error) {
            console.error('Failed to sync cart with server:', error);
        }
    }

    /**
     * Update total price
     */
    updateTotal() {
        // Note: In a real implementation, you'd fetch current prices from server
        // For now, we'll use cached prices or assume prices are included in items
        this.cart.total = this.cart.items.reduce((sum, item) => {
            return sum + ((item.price || 0) * item.quantity);
        }, 0);
    }

    /**
     * Make API request
     */
    async makeRequest(endpoint, options = {}) {
        const url = `${this.apiBaseUrl}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            },
            ...options
        };

        // Add CSRF token if available
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            config.headers['X-CSRF-TOKEN'] = csrfToken.content;
        }

        // Add auth token if available
        const authToken = localStorage.getItem('auth_token');
        if (authToken) {
            config.headers.Authorization = `Bearer ${authToken}`;
        }

        const response = await fetch(url, config);
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || `HTTP ${response.status}`);
        }

        return data;
    }

    /**
     * Load cart from localStorage
     */
    loadFromStorage() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                const parsed = JSON.parse(saved);
                return {
                    items: parsed.items || [],
                    total: parsed.total || 0
                };
            }
        } catch (error) {
            console.error('Failed to load cart from storage:', error);
        }

        return { items: [], total: 0 };
    }

    /**
     * Save cart to localStorage
     */
    saveToStorage() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.cart));
        } catch (error) {
            console.error('Failed to save cart to storage:', error);
        }
    }

    /**
     * Get cart summary for header/cart icon
     */
    getSummary() {
        return {
            itemCount: this.getItemCount(),
            total: this.cart.total
        };
    }
}

// Create singleton instance
const cartService = new CartService();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CartService;
}

// Make available globally
window.CartService = cartService;