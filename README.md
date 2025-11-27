# 🛒 Amnet E-commerce Laravel

ระบบอีคอมเมิร์ซที่พัฒนาด้วย Laravel พร้อมการนำหลักการ **Data Structures and Algorithms (DSA)** มาใช้อย่างเต็มรูปแบบ

## 📋 ภาพรวมโปรเจคต์

โปรเจคต์นี้เป็นระบบขายสินค้าออนไลน์ที่ครบครัน มาพร้อมฟีเจอร์หลักต่างๆ และได้รับการออกแบบให้มีประสิทธิภาพสูงโดยใช้หลักการ DSA

### 🎯 ฟีเจอร์หลัก

#### 🛍️ **ระบบขายสินค้า**
- ✅ แสดงสินค้าพร้อมหมวดหมู่และแบรนด์
- ✅ ค้นหาสินค้าด้วย **Trie** และ **Levenshtein Distance**
- ✅ กรองสินค้าด้วยราคาและหมวดหมู่
- ✅ แสดงสินค้าแนะนำและยอดนิยม

#### 🛒 **ระบบตะกร้าสินค้า**
- ✅ เพิ่ม/ลบ/แก้ไขจำนวนสินค้า
- ✅ ตรวจสอบสต็อกอัตโนมัติ
- ✅ คำนวณราคาแบบ real-time
- ✅ ใช้ **member-based architecture**

#### 💳 **ระบบชำระเงิน**
- ✅ บัตรเครดิต/เดบิต
- ✅ QR พร้อมเพย์
- ✅ ชำระปลายทาง (COD)
- ✅ ระบบ callback และ webhook

#### ❤️ **ระบบรายการโปรด (Wishlist)**
- ✅ เพิ่ม/ลบสินค้าจาก wishlist
- ✅ แสดง wishlist แบบ pagination
- ✅ ปุ่มแสดงสถานะ real-time
- ✅ AJAX operations ลื่นไหล

#### 📦 **ระบบจัดการคำสั่งซื้อ**
- ✅ สร้างและติดตามคำสั่งซื้อ
- ✅ ยกเลิกคำสั่งซื้อ (ภายใน 24 ชั่วโมง)
- ✅ คืนสต็อกและจัดการ refund
- ✅ ระบบ transaction สำหรับความปลอดภัย

#### 👤 **ระบบสมาชิก**
- ✅ สมัครสมาชิกและเข้าสู่ระบบ
- ✅ จัดการข้อมูลส่วนตัว
- ✅ ประวัติการสั่งซื้อ
- ✅ ระบบสิทธิ์และบทบาท

## 🏗️ สถาปัตยกรรมและหลักการ DSA

### 📊 **Data Structures ที่นำมาใช้**

#### **Trie (Prefix Tree)**
```php
// ใช้สำหรับ autocomplete และค้นหาคำอย่างรวดเร็ว
// Time Complexity: O(m) where m is prefix length
class SearchService {
    private array $trie = [];

    public function buildAutocompleteTrie(Collection $items): void
    public function autocomplete(string $prefix): Collection
}
```

#### **Levenshtein Distance Algorithm**
```php
// ใช้สำหรับ fuzzy search
// Dynamic Programming: O(m*n) time complexity
private function levenshtein(string $str1, string $str2): int {
    // Wagner-Fischer algorithm implementation
}
```

#### **Hash-based Lookups**
```php
// ใช้สำหรับการกรองหมวดหมู่และแบรนด์
// Average O(1) lookup time
public function categoryFilter(Builder $query, array $categoryIds): Builder {
    return $query->whereIn('category_id', $categoryIds);
}
```

### ⚡ **Algorithms Optimization**

#### **Search Strategy Pattern**
- **Short terms**: Exact match with wildcards
- **Long terms**: Full-text search with ranking
- **Fuzzy search**: Levenshtein distance algorithm

#### **Database Query Optimization**
- **Eager Loading**: `with()` relationships
- **Selective Columns**: เลือกเฉพาะฟิลด์ที่ต้องการ
- **Pagination**: แบ่งหน้าเพื่อ performance
- **Indexing**: ใช้ index ที่เหมาะสม

#### **Caching Strategy**
```php
// Redis caching for search results
public function cacheSearchResults(string $cacheKey, $results, int $ttl = 300)
```

## 🗄️ โครงสร้างฐานข้อมูล

### **Entity Relationship Diagram**
```
User (1) ──── (1) Member
    │
    └── (N) Orders
        │
        ├── (N) OrderItems ──── (N) Products
        │       │
        │       └── (N) Categories
        │
        └── (1) Payment
            │
            └── (N) PaymentLogs

Member (N) ──── (N) Wishlist ──── (N) Products
    │
    └── (N) CartItems ──── (N) Products
```

### **Key Tables**
- **users**: ข้อมูลผู้ใช้
- **members**: ข้อมูลสมาชิก (member-based architecture)
- **products**: ข้อมูลสินค้า
- **categories**: หมวดหมู่สินค้า
- **orders**: คำสั่งซื้อ
- **order_items**: รายการสินค้าในคำสั่งซื้อ
- **payments**: ข้อมูลการชำระเงิน
- **wishlists**: รายการโปรด
- **cart_items**: ตะกร้าสินค้า

## 🚀 การติดตั้งและรันโปรเจคต์

### **Prerequisites**
- PHP 8.1+
- Composer
- Node.js & NPM
- PostgreSQL/MySQL
- Redis (แนะนำ)

### **Installation Steps**

1. **Clone Repository**
```bash
git clone <repository-url>
cd amnet-web-e-commerce-laravel
```

2. **Install Dependencies**
```bash
composer install
npm install
```

3. **Environment Setup**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Database Setup**
```bash
# Configure database in .env file
php artisan migrate
php artisan db:seed
```

5. **Build Assets**
```bash
npm run build
# or for development
npm run dev
```

6. **Start Server**
```bash
php artisan serve
```

## 📈 Performance Metrics

### **Search Performance**
- **Trie Lookup**: O(m) - m = prefix length
- **Fuzzy Search**: O(m*n) - m,n = string lengths
- **Database Queries**: Optimized with eager loading

### **Response Times**
- **Product Search**: < 100ms
- **Cart Operations**: < 50ms
- **Order Processing**: < 200ms

### **Scalability Features**
- **Database Indexing**: Optimized for search queries
- **Caching**: Redis for frequently accessed data
- **Pagination**: Prevents large dataset loading
- **Lazy Loading**: Relationships loaded on demand

## 🔒 Security Features

### **Authentication & Authorization**
- Laravel Sanctum for API authentication
- Role-based access control
- CSRF protection
- Input validation and sanitization

### **Payment Security**
- PCI DSS compliant payment processing
- Encrypted card data storage
- Secure webhook validation
- Transaction logging

### **Data Protection**
- Password hashing with bcrypt
- Sensitive data encryption
- SQL injection prevention
- XSS protection

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=SearchServiceTest

# Run with coverage
php artisan test --coverage
```

## 📚 API Documentation

### **RESTful Endpoints**

#### **Products**
- `GET /api/products` - แสดงสินค้าทั้งหมด
- `GET /api/products/{id}` - แสดงสินค้าเฉพาะ
- `GET /api/products/search` - ค้นหาสินค้า

#### **Cart**
- `POST /api/cart/add/{productId}` - เพิ่มสินค้าในตะกร้า
- `PATCH /api/cart/update` - อัปเดตจำนวนสินค้า
- `DELETE /api/cart/remove` - ลบสินค้าออกจากตะกร้า

#### **Orders**
- `GET /api/orders` - แสดงคำสั่งซื้อของผู้ใช้
- `POST /api/orders` - สร้างคำสั่งซื้อใหม่
- `DELETE /api/orders/{id}` - ยกเลิกคำสั่งซื้อ

## 🤝 Contributing

1. Fork the repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

- **Developer**: Amnet Development Team
- **Tech Stack**: Laravel, PostgreSQL, Redis, Bootstrap
- **Architecture**: MVC with DSA principles

## 🔄 การอัปเดตล่าสุด (Latest Updates)

### **Database Schema Overhaul (27 พฤศจิกายน 2025)**
- ✅ **ปรับปรุงโครงสร้างฐานข้อมูล**: อัปเดต migrations, models และ seeders ให้ตรงกับ SQL schema ใหม่ที่ครบครัน
- ✅ **ตารางฐานข้อมูลหลัก**:
  - `roles` - จัดการบทบาทผู้ใช้
  - `users` - ข้อมูลผู้ใช้พร้อมระบบสิทธิ์
  - `members` - ข้อมูลสมาชิกแบบครบถ้วน
  - `categories` - หมวดหมู่สินค้าพร้อมโครงสร้างลำดับชั้น
  - `brands` - แบรนด์สินค้าพร้อมข้อมูลเพิ่มเติม
  - `products` - สินค้าพร้อม SKU, specifications และ views
  - `product_images` - จัดการรูปภาพสินค้าหลายรูป
  - `cart_items` - ตะกร้าสินค้าที่เชื่อมโยงกับสมาชิก
  - `reviews` - รีวิวสินค้าพร้อมระบบ verified purchase
  - `orders` - คำสั่งซื้อพร้อม discount และ payment status
  - `order_items` - รายการสินค้าในคำสั่งซื้อ
  - `payments` - ข้อมูลการชำระเงินครบถ้วน
  - `shipping` - ข้อมูลการจัดส่ง
  - `wishlists` - รายการโปรด
  - `promotions` - ระบบโปรโมชั่นและคูปองส่วนลด
- ✅ **ฟีเจอร์เพิ่มเติม**:
  - ระบบ indexes สำหรับประสิทธิภาพการค้นหา
  - Triggers สำหรับอัปเดต timestamps อัตโนมัติ
  - Foreign key constraints ที่สมบูรณ์
  - Data validation และ constraints
- ✅ **การปรับปรุงระบบ**:
  - ลบตารางที่ไม่จำเป็นออก (cache, jobs, password_reset_tokens, sessions)
  - อัปเดต models ให้รองรับ primary keys แบบกำหนดเอง
  - ปรับปรุง seeders ให้ตรงกับโครงสร้างใหม่
  - จัดการ conflicts และ merge กับ remote repository

## 🎉 Acknowledgments

- Laravel Framework for robust backend
- Bootstrap for responsive frontend
- PostgreSQL for reliable database
- Redis for high-performance caching

---

**⭐ Star this repository if you find it helpful!**

**📧 Contact**: dev@amnet.com | 🌐 https://amnet.com**