# 🏗️ Laravel E-Commerce API Documentation

## 📋 Overview

This API provides comprehensive e-commerce functionality with proper RESTful design, authentication, and standardized responses.

**Base URL:** `https://your-domain.com/api/v1`

**Authentication:** Sanctum (Bearer Token)

**Rate Limiting:** 60 requests per minute

---

## 🔐 Authentication

### Login
```http
POST /api/login
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}
```

### Get Authenticated User
```http
GET /api/user
Authorization: Bearer {token}
```

---

## 📦 Products API

### List Products (Public)
```http
GET /api/v1/products?page=1&per_page=15&category_id=1&search=laptop&sort_by=price&sort_direction=asc
```

**Query Parameters:**
- `page` (int): Page number (default: 1)
- `per_page` (int): Items per page (default: 15, max: 100)
- `category_id` (int): Filter by category
- `brand_id` (int): Filter by brand
- `search` (string): Search in product name/description
- `price_min` (float): Minimum price filter
- `price_max` (float): Maximum price filter
- `status` (string): Product status (active/inactive)
- `sort_by` (string): Sort field (name, price, created_at, view_count)
- `sort_direction` (string): Sort direction (asc/desc)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "product_id": "PROD001",
      "product_name": "Laptop Pro",
      "price": 2999.99,
      "stock_quantity": 50,
      "status": "active",
      "category": {
        "id": 1,
        "name": "Electronics"
      },
      "brand": {
        "id": 1,
        "name": "Brand X"
      },
      "primary_image": {
        "id": 1,
        "image_path": "/storage/products/laptop.jpg"
      }
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  },
  "message": "Products retrieved successfully",
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Get Product Details (Public)
```http
GET /api/v1/products/{id}
```

### Create Product (Admin Only)
```http
POST /api/v1/products
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "product_name": "New Product",
  "description": "Product description",
  "price": 99.99,
  "stock_quantity": 100,
  "category_id": 1,
  "brand_id": 1,
  "status": "active"
}
```

### Update Product (Admin Only)
```http
PUT /api/v1/products/{id}
Authorization: Bearer {admin_token}
Content-Type: application/json

{
  "product_name": "Updated Product Name",
  "price": 149.99
}
```

### Delete Product (Admin Only)
```http
DELETE /api/v1/products/{id}
Authorization: Bearer {admin_token}
```

---

## 🛒 Cart API

### Get Cart (Authenticated)
```http
GET /api/v1/cart
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "items": [
      {
        "id": 1,
        "product_id": 1,
        "quantity": 2,
        "price_at_add": 99.99,
        "product": {
          "id": 1,
          "product_name": "Product Name",
          "price": 99.99
        }
      }
    ],
    "total_items": 2,
    "total_price": 199.98
  },
  "message": "Cart retrieved successfully",
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Add to Cart (Authenticated)
```http
POST /api/v1/cart/items
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2
}
```

### Update Cart Item (Authenticated)
```http
PUT /api/v1/cart/items/{item_id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "quantity": 3
}
```

### Remove from Cart (Authenticated)
```http
DELETE /api/v1/cart/items/{item_id}
Authorization: Bearer {token}
```

### Get Cart Count (Authenticated)
```http
GET /api/v1/cart/count
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "count": 5
  },
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

---

## 🔍 Search & Discovery API

### Get Featured Products (Public)
```http
GET /api/v1/products/featured?limit=10
```

### Search Products (Public)
```http
GET /api/v1/products/search?q=laptop
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "product_id": 1,
      "product_name": "Gaming Laptop",
      "price": 2999.99,
      "image_url": "/storage/products/laptop.jpg"
    }
  ],
  "message": "Search suggestions retrieved successfully",
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Get Related Products (Public)
```http
GET /api/v1/products/{product_id}/related?limit=4
```

### Get Product Reviews (Public)
```http
GET /api/v1/products/{product_id}/reviews?limit=10
```

---

## 📊 Response Format Standards

### Success Response
```json
{
  "success": true,
  "data": { /* resource data */ },
  "message": "Operation completed successfully",
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "error_code": "ERROR_CODE",
  "errors": { /* validation errors */ },
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

### Paginated Response
```json
{
  "success": true,
  "data": [ /* array of resources */ ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15
  },
  "message": "Resources retrieved successfully",
  "timestamp": "2024-01-01T12:00:00.000000Z"
}
```

---

## 🚨 Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request validation failed |
| `UNAUTHORIZED` | Authentication required |
| `FORBIDDEN` | Access denied |
| `NOT_FOUND` | Resource not found |
| `CONFLICT` | Resource conflict |
| `RATE_LIMITED` | Too many requests |
| `INTERNAL_ERROR` | Internal server error |
| `INSUFFICIENT_STOCK` | Insufficient stock for the requested quantity |
| `PRODUCT_NOT_ACTIVE` | Product is not available |
| `INVALID_QUANTITY` | Invalid quantity specified |
| `CART_EMPTY` | Shopping cart is empty |

---

## 📋 HTTP Status Codes

- `200` - OK (successful GET requests)
- `201` - Created (successful POST requests)
- `204` - No Content (successful DELETE requests)
- `400` - Bad Request (validation errors)
- `401` - Unauthorized (authentication required)
- `403` - Forbidden (access denied)
- `404` - Not Found (resource not found)
- `409` - Conflict (business rule violations)
- `422` - Unprocessable Entity (validation errors)
- `429` - Too Many Requests (rate limited)
- `500` - Internal Server Error (server errors)

---

## 🔒 Security Features

- **Rate Limiting:** 60 requests per minute per IP
- **CORS Protection:** Configured allowed origins
- **CSRF Protection:** Enabled for web routes
- **Input Validation:** Comprehensive validation rules
- **SQL Injection Protection:** Eloquent ORM with parameter binding
- **XSS Protection:** Blade templating with auto-escaping

---

## 📈 Performance Features

- **Caching:** Redis-based caching for frequently accessed data
- **Database Indexing:** Optimized indexes for search operations
- **Eager Loading:** N+1 query prevention
- **Pagination:** Efficient pagination for large datasets
- **Background Jobs:** Queue system for heavy operations

---

## 🧪 Testing

Run API tests with:
```bash
php artisan test --testsuite=api
```

---

## 📝 Changelog

### Version 1.0.0
- Initial API release
- Basic CRUD operations for products
- Cart management
- Search and filtering
- Authentication with Sanctum

---

*Last updated: January 2024*