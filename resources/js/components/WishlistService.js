/**
 * WishlistService - Vanilla JavaScript
 * Handles user wishlist operations
 */
class WishlistService {
    constructor() {
        this.storageKey = 'user_wishlist';
        this.apiBaseUrl = '/api/v1';
        this.wishlist = this.loadFromStorage();
        this.listeners = [];
    }

    /**
     * Subscribe to wishlist changes
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
        this.listeners.forEach(listener => listener([...this.wishlist]));
    }

    /**
     * Get current wishlist
     */
    getWishlist() {
        return [...this.wishlist];
    }

    /**
     * Check if product is in wishlist
     */
    isInWishlist(productId) {
        return this.wishlist.includes(productId);
    }

    /**
     * Toggle product in wishlist
     */
    async toggleItem(productId) {
        const isInWishlist = this.isInWishlist(productId);

        try {
            const endpoint = isInWishlist ? '/wishlist/remove' : '/wishlist/add';
            const response = await this.makeRequest(endpoint, {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId
                })
            });

            if (response.success) {
                if (isInWishlist) {
                    this.removeItemLocally(productId);
                } else {
                    this.addItemLocally(productId);
                }
                this.notify();
                return {
                    success: true,
                    action: isInWishlist ? 'removed' : 'added'
                };
            }

            return { success: false, error: response.message };
        } catch (error) {
            console.error('WishlistService.toggleItem error:', error);

            // Fallback to local storage
            if (isInWishlist) {
                this.removeItemLocally(productId);
            } else {
                this.addItemLocally(productId);
            }
            this.notify();

            return {
                success: true,
                fallback: true,
                action: isInWishlist ? 'removed' : 'added'
            };
        }
    }

    /**
     * Add item to wishlist
     */
    async addItem(productId) {
        if (this.isInWishlist(productId)) {
            return { success: true, message: 'Already in wishlist' };
        }

        try {
            const response = await this.makeRequest('/wishlist/add', {
                method: 'POST',
                body: JSON.stringify({
                    product_id: productId
                })
            });

            if (response.success) {
                this.addItemLocally(productId);
                this.notify();
                return { success: true };
            }

            return { success: false, error: response.message };
        } catch (error) {
            console.error('WishlistService.addItem error:', error);
            this.addItemLocally(productId);
            this.notify();
            return { success: true, fallback: true };
        }
    }

    /**
     * Remove item from wishlist
     */
    async removeItem(productId) {
        if (!this.isInWishlist(productId)) {
            return { success: true, message: 'Not in wishlist' };
        }

        try {
            const response = await this.makeRequest('/wishlist/remove', {
                method: 'DELETE',
                body: JSON.stringify({
                    product_id: productId
                })
            });

            if (response.success) {
                this.removeItemLocally(productId);
                this.notify();
                return { success: true };
            }

            return { success: false, error: response.message };
        } catch (error) {
            console.error('WishlistService.removeItem error:', error);
            this.removeItemLocally(productId);
            this.notify();
            return { success: true, fallback: true };
        }
    }

    /**
     * Clear entire wishlist
     */
    async clearWishlist() {
        try {
            const response = await this.makeRequest('/wishlist/clear', {
                method: 'DELETE'
            });

            if (response.success) {
                this.wishlist = [];
                this.saveToStorage();
                this.notify();
                return { success: true };
            }

            return { success: false, error: response.message };
        } catch (error) {
            console.error('WishlistService.clearWishlist error:', error);
            this.wishlist = [];
            this.saveToStorage();
            this.notify();
            return { success: true, fallback: true };
        }
    }

    /**
     * Sync wishlist with server
     */
    async syncWithServer() {
        try {
            const response = await this.makeRequest('/wishlist');
            if (response.success && response.data) {
                this.wishlist = response.data.map(item => item.product_id) || [];
                this.saveToStorage();
            }
        } catch (error) {
            console.error('Failed to sync wishlist with server:', error);
        }
    }

    /**
     * Add item locally (fallback)
     */
    addItemLocally(productId) {
        if (!this.wishlist.includes(productId)) {
            this.wishlist.push(productId);
            this.saveToStorage();
        }
    }

    /**
     * Remove item locally (fallback)
     */
    removeItemLocally(productId) {
        this.wishlist = this.wishlist.filter(id => id !== productId);
        this.saveToStorage();
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
     * Load wishlist from localStorage
     */
    loadFromStorage() {
        try {
            const saved = localStorage.getItem(this.storageKey);
            if (saved) {
                return JSON.parse(saved);
            }
        } catch (error) {
            console.error('Failed to load wishlist from storage:', error);
        }

        return [];
    }

    /**
     * Save wishlist to localStorage
     */
    saveToStorage() {
        try {
            localStorage.setItem(this.storageKey, JSON.stringify(this.wishlist));
        } catch (error) {
            console.error('Failed to save wishlist to storage:', error);
        }
    }

    /**
     * Get wishlist count
     */
    getCount() {
        return this.wishlist.length;
    }
}

// Create singleton instance
const wishlistService = new WishlistService();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = WishlistService;
}

// Make available globally
window.WishlistService = wishlistService;