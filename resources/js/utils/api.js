/**
 * API Utilities - Vanilla JavaScript
 * Centralized API communication layer
 */

class ApiClient {
    constructor(baseURL = '/api/v1') {
        this.baseURL = baseURL;
        this.defaultHeaders = {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        };
    }

    /**
     * Make HTTP request
     */
    async request(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            headers: { ...this.defaultHeaders },
            ...options
        };

        // Merge headers
        if (options.headers) {
            config.headers = { ...config.headers, ...options.headers };
        }

        // Add CSRF token for non-GET requests
        if (config.method && config.method !== 'GET') {
            const csrfToken = this.getCsrfToken();
            if (csrfToken) {
                config.headers['X-CSRF-TOKEN'] = csrfToken;
            }
        }

        // Add authorization token if available
        const authToken = this.getAuthToken();
        if (authToken) {
            config.headers.Authorization = `Bearer ${authToken}`;
        }

        try {
            const response = await fetch(url, config);
            const data = await this.parseResponse(response);

            if (!response.ok) {
                throw new ApiError(
                    data.message || `HTTP ${response.status}`,
                    response.status,
                    data
                );
            }

            return data;
        } catch (error) {
            if (error instanceof ApiError) {
                throw error;
            }

            // Network or parsing error
            throw new ApiError(
                error.message || 'Network error',
                0,
                { originalError: error }
            );
        }
    }

    /**
     * GET request
     */
    async get(endpoint, params = {}) {
        const queryString = this.buildQueryString(params);
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return this.request(url);
    }

    /**
     * POST request
     */
    async post(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'POST',
            body: JSON.stringify(data)
        });
    }

    /**
     * PUT request
     */
    async put(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PUT',
            body: JSON.stringify(data)
        });
    }

    /**
     * PATCH request
     */
    async patch(endpoint, data = {}) {
        return this.request(endpoint, {
            method: 'PATCH',
            body: JSON.stringify(data)
        });
    }

    /**
     * DELETE request
     */
    async delete(endpoint, data = {}) {
        const config = {
            method: 'DELETE'
        };

        if (Object.keys(data).length > 0) {
            config.body = JSON.stringify(data);
        }

        return this.request(endpoint, config);
    }

    /**
     * Upload file
     */
    async upload(endpoint, formData, onProgress = null) {
        const url = `${this.baseURL}${endpoint}`;
        const config = {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': this.getCsrfToken(),
                'X-Requested-With': 'XMLHttpRequest'
                // Don't set Content-Type for FormData, let browser set it
            }
        };

        // Add authorization token if available
        const authToken = this.getAuthToken();
        if (authToken) {
            config.headers.Authorization = `Bearer ${authToken}`;
        }

        // Add progress tracking if callback provided
        if (onProgress) {
            config.signal = this.createProgressSignal(onProgress);
        }

        try {
            const response = await fetch(url, config);
            const data = await this.parseResponse(response);

            if (!response.ok) {
                throw new ApiError(
                    data.message || `HTTP ${response.status}`,
                    response.status,
                    data
                );
            }

            return data;
        } catch (error) {
            if (error instanceof ApiError) {
                throw error;
            }
            throw new ApiError(
                error.message || 'Upload failed',
                0,
                { originalError: error }
            );
        }
    }

    /**
     * Get CSRF token from meta tag
     */
    getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        return token ? token.content : null;
    }

    /**
     * Get auth token from localStorage
     */
    getAuthToken() {
        return localStorage.getItem('auth_token');
    }

    /**
     * Set auth token
     */
    setAuthToken(token) {
        if (token) {
            localStorage.setItem('auth_token', token);
        } else {
            localStorage.removeItem('auth_token');
        }
    }

    /**
     * Build query string from object
     */
    buildQueryString(params) {
        const query = new URLSearchParams();
        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined) {
                query.append(key, value);
            }
        });
        return query.toString();
    }

    /**
     * Parse response based on content type
     */
    async parseResponse(response) {
        const contentType = response.headers.get('content-type');

        if (contentType && contentType.includes('application/json')) {
            return await response.json();
        } else if (contentType && contentType.includes('text/')) {
            return await response.text();
        } else {
            return await response.blob();
        }
    }

    /**
     * Create progress tracking signal
     */
    createProgressSignal(onProgress) {
        const controller = new AbortController();

        // Note: This is a simplified version. For full progress tracking,
        // you might need to use XMLHttpRequest instead of fetch
        // as fetch doesn't support progress events natively

        return controller.signal;
    }
}

/**
 * Custom API Error class
 */
class ApiError extends Error {
    constructor(message, status, data = {}) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.data = data;
    }
}

/**
 * Product API methods
 */
class ProductAPI extends ApiClient {
    async getProducts(params = {}) {
        return this.get('/products', params);
    }

    async getProduct(id) {
        return this.get(`/products/${id}`);
    }

    async getFeaturedProducts() {
        return this.get('/products/featured');
    }

    async searchProducts(query, filters = {}) {
        return this.get('/products/search', { q: query, ...filters });
    }
}

/**
 * Cart API methods
 */
class CartAPI extends ApiClient {
    async getCart() {
        return this.get('/cart');
    }

    async addToCart(productId, quantity = 1) {
        return this.post('/cart/add', { product_id: productId, quantity });
    }

    async updateCart(productId, quantity) {
        return this.patch('/cart/update', { product_id: productId, quantity });
    }

    async removeFromCart(productId) {
        return this.delete('/cart/remove', { product_id: productId });
    }

    async clearCart() {
        return this.delete('/cart/clear');
    }

    async getCartCount() {
        return this.get('/cart/count');
    }
}

/**
 * Wishlist API methods
 */
class WishlistAPI extends ApiClient {
    async getWishlist() {
        return this.get('/wishlist');
    }

    async addToWishlist(productId) {
        return this.post('/wishlist/add', { product_id: productId });
    }

    async removeFromWishlist(productId) {
        return this.delete('/wishlist/remove', { product_id: productId });
    }

    async clearWishlist() {
        return this.delete('/wishlist/clear');
    }
}

/**
 * User API methods
 */
class UserAPI extends ApiClient {
    async login(credentials) {
        return this.post('/auth/login', credentials);
    }

    async register(userData) {
        return this.post('/auth/register', userData);
    }

    async logout() {
        return this.post('/auth/logout');
    }

    async getProfile() {
        return this.get('/user/profile');
    }

    async updateProfile(data) {
        return this.put('/user/profile', data);
    }
}

/**
 * Order API methods
 */
class OrderAPI extends ApiClient {
    async getOrders(params = {}) {
        return this.get('/orders', params);
    }

    async getOrder(id) {
        return this.get(`/orders/${id}`);
    }

    async createOrder(orderData) {
        return this.post('/orders', orderData);
    }

    async cancelOrder(id) {
        return this.patch(`/orders/${id}/cancel`);
    }
}

// Create API instances
const apiClient = new ApiClient();
const productAPI = new ProductAPI();
const cartAPI = new CartAPI();
const wishlistAPI = new WishlistAPI();
const userAPI = new UserAPI();
const orderAPI = new OrderAPI();

// Export for module use
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        ApiClient,
        ApiError,
        ProductAPI,
        CartAPI,
        WishlistAPI,
        UserAPI,
        OrderAPI,
        apiClient,
        productAPI,
        cartAPI,
        wishlistAPI,
        userAPI,
        orderAPI
    };
}

// Make available globally
window.API = {
    client: apiClient,
    products: productAPI,
    cart: cartAPI,
    wishlist: wishlistAPI,
    user: userAPI,
    orders: orderAPI,
    ApiError
};