'use strict';

import { API_BASE_URL, ITEMS_PER_PAGE, CACHE_TTL } from '../config/constants.js';

/**
 * Service for handling product-related API operations
 * @class ProductService
 */
class ProductService {
    constructor() {
        this.cache = new Map();
        this.baseURL = API_BASE_URL;
    }

    /**
     * Get products with optional filters
     * @param {Object} filters - Filter options
     * @param {number} filters.page - Page number (1-based)
     * @param {number} filters.limit - Items per page
     * @param {string} filters.category - Category slug
     * @param {string} filters.search - Search query
     * @param {string} filters.sort - Sort field (price, name, created_at)
     * @param {string} filters.order - Sort order (asc, desc)
     * @returns {Promise<Object>} Products data with pagination
     */
    async getProducts(filters = {}) {
        // Validate inputs
        this._validateFilters(filters);

        const cacheKey = this._generateCacheKey('products', filters);

        // Check cache first
        if (this._isCacheValid(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        try {
            const queryParams = this._buildQueryParams({
                page: 1,
                limit: ITEMS_PER_PAGE,
                ...filters
            });

            const response = await this._makeRequest(`/products${queryParams}`);

            // Cache the result
            this._setCache(cacheKey, response, CACHE_TTL.PRODUCTS);

            return response;
        } catch (error) {
            console.error('Failed to fetch products:', error);
            throw new Error('ไม่สามารถโหลดสินค้าได้ กรุณาลองใหม่');
        }
    }

    /**
     * Get single product by ID or slug
     * @param {string|number} identifier - Product ID or slug
     * @returns {Promise<Object>} Product data
     */
    async getProduct(identifier) {
        if (!identifier) {
            throw new Error('Product identifier is required');
        }

        const cacheKey = `product_${identifier}`;

        if (this._isCacheValid(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        try {
            const response = await this._makeRequest(`/products/${identifier}`);

            this._setCache(cacheKey, response, CACHE_TTL.PRODUCTS);

            return response;
        } catch (error) {
            console.error('Failed to fetch product:', error);
            throw new Error('ไม่พบสินค้าที่ต้องการ');
        }
    }

    /**
     * Search products
     * @param {string} query - Search query
     * @param {Object} options - Search options
     * @returns {Promise<Object>} Search results
     */
    async searchProducts(query, options = {}) {
        if (!query || typeof query !== 'string') {
            throw new Error('Search query must be a non-empty string');
        }

        if (query.length < 2) {
            throw new Error('Search query must be at least 2 characters');
        }

        try {
            const queryParams = this._buildQueryParams({
                q: query.trim(),
                limit: options.limit || 10
            });

            return await this._makeRequest(`/products/search${queryParams}`);
        } catch (error) {
            console.error('Search failed:', error);
            throw new Error('การค้นหาล้มเหลว กรุณาลองใหม่');
        }
    }

    /**
     * Get product categories
     * @returns {Promise<Array>} Categories list
     */
    async getCategories() {
        const cacheKey = 'categories';

        if (this._isCacheValid(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        try {
            const response = await this._makeRequest('/categories');

            this._setCache(cacheKey, response, CACHE_TTL.CATEGORIES);

            return response;
        } catch (error) {
            console.error('Failed to fetch categories:', error);
            throw new Error('ไม่สามารถโหลดหมวดหมู่สินค้าได้');
        }
    }

    /**
     * Get featured/popular products
     * @param {string} type - Type of featured products (popular, featured, new)
     * @param {number} limit - Number of products to fetch
     * @returns {Promise<Array>} Featured products
     */
    async getFeaturedProducts(type = 'popular', limit = 8) {
        if (!['popular', 'featured', 'new'].includes(type)) {
            throw new Error('Invalid featured type');
        }

        if (typeof limit !== 'number' || limit < 1 || limit > 50) {
            throw new Error('Limit must be between 1 and 50');
        }

        const cacheKey = `featured_${type}_${limit}`;

        if (this._isCacheValid(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        try {
            const queryParams = this._buildQueryParams({ type, limit });
            const response = await this._makeRequest(`/products/featured${queryParams}`);

            this._setCache(cacheKey, response, CACHE_TTL.PRODUCTS);

            return response;
        } catch (error) {
            console.error('Failed to fetch featured products:', error);
            throw new Error('ไม่สามารถโหลดสินค้าแนะนำได้');
        }
    }

    /**
     * Validate filter parameters
     * @private
     * @param {Object} filters - Filters to validate
     */
    _validateFilters(filters) {
        if (filters.page && (typeof filters.page !== 'number' || filters.page < 1)) {
            throw new Error('Page must be a positive number');
        }

        if (filters.limit && (typeof filters.limit !== 'number' || filters.limit < 1 || filters.limit > 100)) {
            throw new Error('Limit must be between 1 and 100');
        }

        if (filters.sort && !['price', 'name', 'created_at', 'rating'].includes(filters.sort)) {
            throw new Error('Invalid sort field');
        }

        if (filters.order && !['asc', 'desc'].includes(filters.order)) {
            throw new Error('Order must be asc or desc');
        }
    }

    /**
     * Generate cache key
     * @private
     * @param {string} prefix - Cache key prefix
     * @param {Object} params - Parameters object
     * @returns {string} Cache key
     */
    _generateCacheKey(prefix, params) {
        const sortedParams = Object.keys(params)
            .sort()
            .reduce((result, key) => {
                result[key] = params[key];
                return result;
            }, {});

        return `${prefix}_${JSON.stringify(sortedParams)}`;
    }

    /**
     * Check if cache is valid
     * @private
     * @param {string} key - Cache key
     * @returns {boolean} True if cache is valid
     */
    _isCacheValid(key) {
        if (!this.cache.has(key)) return false;

        const { data, timestamp, ttl } = this.cache.get(key);
        return (Date.now() - timestamp) < (ttl * 1000);
    }

    /**
     * Set cache data
     * @private
     * @param {string} key - Cache key
     * @param {any} data - Data to cache
     * @param {number} ttl - Time to live in seconds
     */
    _setCache(key, data, ttl) {
        this.cache.set(key, {
            data,
            timestamp: Date.now(),
            ttl
        });
    }

    /**
     * Build query parameters string
     * @private
     * @param {Object} params - Query parameters
     * @returns {string} Query string
     */
    _buildQueryParams(params) {
        const query = new URLSearchParams();

        Object.entries(params).forEach(([key, value]) => {
            if (value !== null && value !== undefined && value !== '') {
                query.append(key, value);
            }
        });

        const queryString = query.toString();
        return queryString ? `?${queryString}` : '';
    }

    /**
     * Make HTTP request
     * @private
     * @param {string} endpoint - API endpoint
     * @param {Object} options - Request options
     * @returns {Promise<Object>} Response data
     */
    async _makeRequest(endpoint, options = {}) {
        const url = `${this.baseURL}${endpoint}`;

        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...this._getAuthHeaders()
            }
        };

        const finalOptions = { ...defaultOptions, ...options };

        try {
            const response = await fetch(url, finalOptions);

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }

            return await response.json();
        } catch (error) {
            console.error('API request failed:', error);
            throw error;
        }
    }

    /**
     * Get authentication headers
     * @private
     * @returns {Object} Auth headers
     */
    _getAuthHeaders() {
        const token = localStorage.getItem('auth_token');
        return token ? { 'Authorization': `Bearer ${token}` } : {};
    }

    /**
     * Clear cache
     */
    clearCache() {
        this.cache.clear();
    }

    /**
     * Get cache statistics
     * @returns {Object} Cache stats
     */
    getCacheStats() {
        return {
            size: this.cache.size,
            keys: Array.from(this.cache.keys())
        };
    }
}

// Export singleton instance
const productService = new ProductService();
export default productService;
export { ProductService };