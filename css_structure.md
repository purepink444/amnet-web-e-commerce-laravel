# CSS Structure & File Mapping Guide

## โครงสร้างไฟล์ CSS

```
resources/css/
├── app.css                    (Master file - imports all modules)
├── base/
│   ├── variables.css          (CSS variables - colors, spacing, typography)
│   ├── reset.css              (CSS reset & base styles)
│   └── typography.css         (Typography & font imports)
├── components/
│   ├── button.css             (Button component styles)
│   ├── card.css               (Card & product card styles)
│   ├── form.css               (Form & input styles)
│   ├── badge.css              (Badge component styles)
│   ├── rating.css             (Rating component styles)
│   ├── modal.css              (Alert/modal styles)
│   └── breadcrumb.css         (Breadcrumb component styles)
├── layouts/
│   ├── header.css             (Header/navbar layout styles)
│   ├── footer.css             (Footer layout styles)
│   ├── sidebar.css            (Sidebar & account pages styles)
│   └── grid.css               (Grid & responsive layout styles)
├── pages/
│   ├── home.css               (Home page specific styles)
│   ├── product-list.css       (Product listing page styles)
│   ├── product-detail.css     (Product detail page styles)
│   ├── cart.css               (Shopping cart page styles)
│   ├── checkout.css           (Checkout page styles)
│   └── account.css            (Account/user pages styles)
└── utilities/
    ├── animations.css         (Animation keyframes & classes)
    └── helpers.css            (Utility helper classes)
```

## คู่มือการแก้ไข CSS ตามหน้าเว็บ

### 🏠 หน้าแรก (Home Page)
**ไฟล์ที่ต้องแก้ไข:** `resources/css/pages/home.css`
- Hero section (ส่วนแนะนำ)
- Brand logos (โลโก้แบรนด์)
- Call-to-action buttons
- **ตัวอย่าง:** เปลี่ยนสีพื้นหลัง Hero → แก้ไขในไฟล์นี้

### 🛍️ หน้ารายการสินค้า (Product List/Category)
**ไฟล์ที่ต้องแก้ไข:** `resources/css/pages/product-list.css`
- การแสดงผลรายการสินค้า
- ฟิลเตอร์และการค้นหา
- การจัดเรียงและ pagination
- **ตัวอย่าง:** ปรับสีปุ่ม "Add to Cart" → แก้ไขในไฟล์นี้

**ไฟล์ที่เกี่ยวข้อง:**
- `resources/css/components/card.css` (การ์ดสินค้า)
- `resources/css/components/button.css` (ปุ่มต่างๆ)
- `resources/css/layouts/grid.css` (การจัดวางกริด)

### 📄 หน้ารายละเอียดสินค้า (Product Detail)
**ไฟล์ที่ต้องแก้ไข:** `resources/css/pages/product-detail.css`
- การแสดงรูปภาพสินค้า
- รายละเอียดและ description
- ปุ่มซื้อและ wishlist
- **ตัวอย่าง:** ปรับขนาดรูปภาพ → แก้ไขในไฟล์นี้

**ไฟล์ที่เกี่ยวข้อง:**
- `resources/css/components/card.css` (รูปแบบการ์ด)
- `resources/css/components/button.css` (ปุ่มซื้อ)
- `resources/css/components/rating.css` (แสดงดาวรีวิว)

### 🛒 หน้ารถเข็น (Shopping Cart)
**ไฟล์ที่ต้องแก้ไข:** `resources/css/pages/cart.css`
- ตารางแสดงสินค้าในรถเข็น
- ปุ่ม Update/Remove
- Summary และ Checkout button
- **ตัวอย่าง:** เปลี่ยนสีปุ่ม "Remove" → แก้ไขในไฟล์นี้

**ไฟล์ที่เกี่ยวข้อง:**
- `resources/css/components/button.css` (ปุ่มต่างๆ)
- `resources/css/layouts/grid.css` (ตารางและการจัดวาง)

### 💳 หน้าชำระเงิน (Checkout)
**ไฟล์ที่ต้องแก้ไข:** `resources/css/pages/checkout.css`
- ฟอร์มข้อมูลการจัดส่ง
- ขั้นตอนการชำระเงิน
- Progress indicator
- **ตัวอย่าง:** ปรับระยะห่างฟอร์ม → แก้ไขในไฟล์นี้

**ไฟล์ที่เกี่ยวข้อง:**
- `resources/css/components/form.css` (ฟอร์มและ input)
- `resources/css/components/button.css` (ปุ่มดำเนินการ)

### 👤 หน้าบัญชีผู้ใช้ (Account/Profile)
**ไฟล์ที่ต้องแก้ไข:** `resources/css/pages/account.css`
- หน้า Profile และข้อมูลส่วนตัว
- Order history
- Settings และ preferences
- **ตัวอย่าง:** เปลี่ยนสีเมนูด้านข้าง → แก้ไขในไฟล์นี้

**ไฟล์ที่เกี่ยวข้อง:**
- `resources/css/layouts/sidebar.css` (แถบด้านข้าง)
- `resources/css/components/form.css` (ฟอร์มแก้ไขข้อมูล)

## 🎨 คอมโพเนนต์ที่ใช้ร่วมกัน

### ปุ่ม (Buttons)
**ไฟล์:** `resources/css/components/button.css`
- ปุ่มหลัก, ปุ่มรอง, ปุ่มขนาดต่างๆ
- สถานะ hover, active, disabled

### การ์ดสินค้า (Product Cards)
**ไฟล์:** `resources/css/components/card.css`
- การ์ดแสดงสินค้า
- รูปภาพ, ชื่อ, ราคา, rating
- Hover effects และ animations

### ฟอร์ม (Forms)
**ไฟล์:** `resources/css/components/form.css`
- Input fields, labels, validation
- Select dropdowns, checkboxes
- Error states และ success states

### แบดจ์และป้าย (Badges)
**ไฟล์:** `resources/css/components/badge.css`
- ป้าย "Sale", "New", "Out of Stock"
- สถานะและสีต่างๆ

### เรตติ้งและดาว (Ratings)
**ไฟล์:** `resources/css/components/rating.css`
- แสดงดาวรีวิว
- Interactive rating inputs

### โมดอลและแจ้งเตือน (Modals/Alerts)
**ไฟล์:** `resources/css/components/modal.css`
- Popup modals
- Alert messages (success, error, warning)
- Confirm dialogs

## 🏗️ เลย์เอาต์หลัก

### Header/Navbar
**ไฟล์:** `resources/css/layouts/header.css`
- Navigation menu
- Logo และ branding
- Search bar
- User menu และ dropdowns

### Footer
**ไฟล์:** `resources/css/layouts/footer.css`
- ลิงก์และข้อมูลติดต่อ
- Social media links
- Copyright และ legal links

### Sidebar
**ไฟล์:** `resources/css/layouts/sidebar.css`
- Navigation สำหรับ admin
- Account menu สำหรับผู้ใช้
- Filter panels

### Grid System
**ไฟล์:** `resources/css/layouts/grid.css`
- Responsive grid layouts
- Product grid arrangements
- Column spacing และ breakpoints

## ⚙️ ยูทิลิตี้และแอนิเมชัน

### Animations
**ไฟล์:** `resources/css/utilities/animations.css`
- Keyframe animations
- Hover effects
- Loading animations
- Page transitions

### Helper Classes
**ไฟล์:** `resources/css/utilities/helpers.css`
- Spacing utilities (margin, padding)
- Color utilities
- Display utilities (show/hide)
- Text alignment และ typography helpers

## 🎯 หลักการแก้ไข CSS

### 1. แก้ไขเฉพาะหน้า (Page-specific changes)
- ใช้ไฟล์ในโฟลเดอร์ `pages/`
- เช่น: ต้องการเปลี่ยนสีพื้นหลังเฉพาะหน้าแรก → แก้ไข `home.css`

### 2. แก้ไขคอมโพเนนต์ (Component changes)
- ใช้ไฟล์ในโฟลเดอร์ `components/`
- เช่น: ต้องการเปลี่ยนสีปุ่มทั้งเว็บ → แก้ไข `button.css`

### 3. แก้ไขเลย์เอาต์ (Layout changes)
- ใช้ไฟล์ในโฟลเดอร์ `layouts/`
- เช่น: ต้องการเปลี่ยน header → แก้ไข `header.css`

### 4. แก้ไขตัวแปรหลัก (Global variables)
- แก้ไข `resources/css/base/variables.css`
- เช่น: ต้องการเปลี่ยนสีหลักทั้งเว็บ → แก้ไขไฟล์นี้

### 5. เพิ่มยูทิลิตี้ (Utility classes)
- แก้ไข `resources/css/utilities/helpers.css`
- เช่น: ต้องการเพิ่มคลาส `.text-center` → แก้ไขไฟล์นี้

## 📝 ไฟล์สำคัญ

- **`resources/css/app.css`** - ไฟล์หลักที่ import ทุกโมดูล (ไม่ต้องแก้ไข)
- **`resources/css/base/variables.css`** - ตัวแปร CSS หลัก (สี, ฟอนต์, ระยะห่าง)
- **`vite.config.js`** - การตั้งค่า build (ไม่ต้องแก้ไขถ้าไม่เพิ่มไฟล์ใหม่)

## 🚀 การเพิ่มฟีเจอร์ใหม่

1. สร้างไฟล์ CSS ใหม่ในโฟลเดอร์ที่เหมาะสม
2. Import ไฟล์ใหม่ใน `resources/css/app.css`
3. ทดสอบ build ด้วย `npm run build`
4. อัปเดตเอกสารนี้ถ้าจำเป็น