'use strict';

/**
 * Application constants and configuration
 * @module config/constants
 */

// API Configuration
export const API_BASE_URL = '/api';
export const API_TIMEOUT = 30000; // 30 seconds

// Pagination
export const ITEMS_PER_PAGE = 12;
export const MAX_PAGES = 100;

// Cart limits
export const MAX_CART_ITEMS = 99;
export const MAX_QUANTITY_PER_ITEM = 99;

// Product settings
export const MAX_PRODUCT_IMAGES = 10;
export const MAX_PRODUCT_NAME_LENGTH = 255;
export const MAX_PRODUCT_DESCRIPTION_LENGTH = 5000;

// Payment methods
export const PAYMENT_METHODS = {
    CASH: 'cash',
    CARD: 'card',
    BANK_TRANSFER: 'bank_transfer',
    QR_CODE: 'qr_code'
};

// Order status
export const ORDER_STATUS = {
    PENDING: 'pending',
    CONFIRMED: 'confirmed',
    PROCESSING: 'processing',
    SHIPPED: 'shipped',
    DELIVERED: 'delivered',
    CANCELLED: 'cancelled',
    REFUNDED: 'refunded'
};

// User roles
export const USER_ROLES = {
    ADMIN: 'admin',
    CUSTOMER: 'customer',
    STAFF: 'staff'
};

// Validation rules
export const VALIDATION_RULES = {
    EMAIL_REGEX: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
    PHONE_REGEX: /^[\+]?[0-9\-\(\)\s]+$/,
    PASSWORD_MIN_LENGTH: 8,
    USERNAME_MIN_LENGTH: 3,
    USERNAME_MAX_LENGTH: 50
};

// UI Constants
export const TOAST_DURATION = 3000;
export const MODAL_Z_INDEX = 1050;
export const LOADING_Z_INDEX = 1060;

// Cache settings
export const CACHE_TTL = {
    PRODUCTS: 300, // 5 minutes
    CATEGORIES: 600, // 10 minutes
    USER_DATA: 1800 // 30 minutes
};

// Error messages
export const ERROR_MESSAGES = {
    NETWORK_ERROR: 'เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่',
    VALIDATION_ERROR: 'ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบ',
    UNAUTHORIZED: 'ไม่มีสิทธิ์เข้าถึง กรุณาเข้าสู่ระบบ',
    NOT_FOUND: 'ไม่พบข้อมูลที่ต้องการ',
    SERVER_ERROR: 'เกิดข้อผิดพลาดจากเซิร์ฟเวอร์ กรุณาลองใหม่ภายหลัง',
    QUOTA_EXCEEDED: 'เกินจำนวนที่อนุญาต',
    INVALID_FORMAT: 'รูปแบบข้อมูลไม่ถูกต้อง'
};

// Success messages
export const SUCCESS_MESSAGES = {
    ITEM_ADDED: 'เพิ่มสินค้าเรียบร้อยแล้ว',
    ITEM_REMOVED: 'ลบสินค้าเรียบร้อยแล้ว',
    ORDER_PLACED: 'สั่งซื้อเรียบร้อยแล้ว',
    PROFILE_UPDATED: 'อัปเดตข้อมูลเรียบร้อยแล้ว',
    PASSWORD_CHANGED: 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว'
};

// Local storage keys
export const STORAGE_KEYS = {
    CART: 'ecommerce_cart',
    WISHLIST: 'ecommerce_wishlist',
    THEME: 'theme',
    AUTH_TOKEN: 'auth_token',
    USER_DATA: 'user_data'
};

// Feature flags
export const FEATURES = {
    ENABLE_WISHLIST: true,
    ENABLE_REVIEWS: true,
    ENABLE_COMPARE: false,
    ENABLE_QUICK_VIEW: true,
    ENABLE_INFINITE_SCROLL: false
};