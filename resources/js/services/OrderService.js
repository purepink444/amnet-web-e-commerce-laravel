'use strict';

import { API_BASE_URL, ORDER_STATUS, PAYMENT_METHODS } from '../config/constants.js';

/**
 * Service for handling order-related operations
 * @class OrderService
 */
class OrderService {
    constructor() {
        this.baseURL = API_BASE_URL;
    }

    /**
     * Create new order
     * @param {Object} orderData - Order data
     * @param {Array} orderData.items - Cart items
     * @param {Object} orderData.shipping - Shipping information
     * @param {string} orderData.paymentMethod - Payment method
     * @returns {Promise<Object>} Created order data
     */
    async createOrder(orderData) {
        this._validateOrderData(orderData);

        try {
            const response = await this._makeRequest('/orders', {
                method: 'POST',
                body: JSON.stringify(orderData)
            });

            return response;
        } catch (error) {
            console.error('Failed to create order:', error);
            throw new Error('ไม่สามารถสร้างคำสั่งซื้อได้ กรุณาลองใหม่');
        }
    }

    /**
     * Get user orders
     * @param {Object} filters - Filter options
     * @param {number} filters.page - Page number
     * @param {string} filters.status - Order status filter
     * @returns {Promise<Object>} Orders data with pagination
     */
    async getUserOrders(filters = {}) {
        this._validateOrderFilters(filters);

        try {
            const queryParams = this._buildQueryParams(filters);
            return await this._makeRequest(`/orders${queryParams}`);
        } catch (error) {
            console.error('Failed to fetch orders:', error);
            throw new Error('ไม่สามารถโหลดคำสั่งซื้อได้');
        }
    }

    /**
     * Get single order by ID
     * @param {string|number} orderId - Order ID
     * @returns {Promise<Object>} Order data
     */
    async getOrder(orderId) {
        if (!orderId) {
            throw new Error('Order ID is required');
        }

        try {
            return await this._makeRequest(`/orders/${orderId}`);
        } catch (error) {
            console.error('Failed to fetch order:', error);
            throw new Error('ไม่พบคำสั่งซื้อที่ต้องการ');
        }
    }

    /**
     * Cancel order
     * @param {string|number} orderId - Order ID
     * @param {string} reason - Cancellation reason
     * @returns {Promise<Object>} Updated order data
     */
    async cancelOrder(orderId, reason = '') {
        if (!orderId) {
            throw new Error('Order ID is required');
        }

        if (reason && typeof reason !== 'string') {
            throw new Error('Cancellation reason must be a string');
        }

        try {
            const response = await this._makeRequest(`/orders/${orderId}/cancel`, {
                method: 'POST',
                body: JSON.stringify({ reason })
            });

            return response;
        } catch (error) {
            console.error('Failed to cancel order:', error);
            throw new Error('ไม่สามารถยกเลิกคำสั่งซื้อได้');
        }
    }

    /**
     * Get order tracking information
     * @param {string|number} orderId - Order ID
     * @returns {Promise<Object>} Tracking data
     */
    async getOrderTracking(orderId) {
        if (!orderId) {
            throw new Error('Order ID is required');
        }

        try {
            return await this._makeRequest(`/orders/${orderId}/tracking`);
        } catch (error) {
            console.error('Failed to fetch tracking:', error);
            throw new Error('ไม่สามารถโหลดข้อมูลติดตามได้');
        }
    }

    /**
     * Calculate shipping cost
     * @param {Object} shippingData - Shipping information
     * @param {Array} shippingData.items - Items to ship
     * @param {string} shippingData.destination - Destination address
     * @returns {Promise<Object>} Shipping cost data
     */
    async calculateShipping(shippingData) {
        this._validateShippingData(shippingData);

        try {
            const response = await this._makeRequest('/shipping/calculate', {
                method: 'POST',
                body: JSON.stringify(shippingData)
            });

            return response;
        } catch (error) {
            console.error('Failed to calculate shipping:', error);
            throw new Error('ไม่สามารถคำนวณค่าจัดส่งได้');
        }
    }

    /**
     * Validate order data
     * @private
     * @param {Object} orderData - Order data to validate
     */
    _validateOrderData(orderData) {
        if (!orderData || typeof orderData !== 'object') {
            throw new Error('Order data must be an object');
        }

        if (!Array.isArray(orderData.items) || orderData.items.length === 0) {
            throw new Error('Order must contain at least one item');
        }

        if (!orderData.shipping || typeof orderData.shipping !== 'object') {
            throw new Error('Shipping information is required');
        }

        if (!orderData.paymentMethod || !Object.values(PAYMENT_METHODS).includes(orderData.paymentMethod)) {
            throw new Error('Valid payment method is required');
        }

        // Validate items
        orderData.items.forEach((item, index) => {
            if (!item.product_id || !item.quantity) {
                throw new Error(`Invalid item at index ${index}`);
            }
            if (typeof item.quantity !== 'number' || item.quantity < 1) {
                throw new Error(`Invalid quantity for item at index ${index}`);
            }
        });
    }

    /**
     * Validate order filters
     * @private
     * @param {Object} filters - Filters to validate
     */
    _validateOrderFilters(filters) {
        if (filters.page && (typeof filters.page !== 'number' || filters.page < 1)) {
            throw new Error('Page must be a positive number');
        }

        if (filters.status && !Object.values(ORDER_STATUS).includes(filters.status)) {
            throw new Error('Invalid order status');
        }
    }

    /**
     * Validate shipping data
     * @private
     * @param {Object} shippingData - Shipping data to validate
     */
    _validateShippingData(shippingData) {
        if (!shippingData || typeof shippingData !== 'object') {
            throw new Error('Shipping data must be an object');
        }

        if (!Array.isArray(shippingData.items) || shippingData.items.length === 0) {
            throw new Error('Shipping must contain at least one item');
        }

        if (!shippingData.destination || typeof shippingData.destination !== 'string') {
            throw new Error('Destination address is required');
        }
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
}

// Export singleton instance
const orderService = new OrderService();
export default orderService;
export { OrderService };