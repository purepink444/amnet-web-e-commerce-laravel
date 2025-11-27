-- Amnet E-commerce Database Export
-- Generated on: 2025-11-22 09:05:10
-- Database: amnet-ecommerce

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- Table: roles
--

DROP TABLE IF EXISTS "roles" CASCADE;

CREATE TABLE "roles" (
    "role_id" integer NOT NULL DEFAULT nextval('roles_role_id_seq'::regclass),
    "role_name" character varying NOT NULL
);

INSERT INTO "roles" ("role_id", "role_name") VALUES
('1', 'admin'),
('2', 'member');

--
-- Table: products
--

DROP TABLE IF EXISTS "products" CASCADE;

CREATE TABLE "products" (
    "product_id" integer NOT NULL DEFAULT nextval('products_product_id_seq'::regclass),
    "category_id" integer,
    "brand_id" integer,
    "product_name" character varying NOT NULL,
    "description" text,
    "price" numeric NOT NULL,
    "stock_quantity" integer,
    "image_url" character varying,
    "specifications" jsonb,
    "status" character varying DEFAULT 'active'::character varying,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "view_count" integer NOT NULL
);

INSERT INTO "products" ("product_id", "category_id", "brand_id", "product_name", "description", "price", "stock_quantity", "image_url", "specifications", "status", "created_at", "updated_at", "view_count") VALUES
('1', '7', '2', 'RG-S808C', 'Ruijie RG-S7800C Core Switch Series is specially designed for next-gen integrated network. Implementing advanced RGOS11.X operating system and VSU/VSD virtualization technologies, the switch future supports future Ethernet requirements. The leading technologies break customer physical network barriers to form an integrated network. The VSU (Virtual Switch Unit) feature greatly simplifies customer network architecture to enhance the operational efficiency. The VSD (Virtual Switch Device), another virtualization technology, significantly lowers the total cost of investment by improving device utilization. The RG-S7800C Switch Series is ideal for MAN, campus network and settings alike.
Feature highlights of RG-S7800C Series Core Switch:
?Cost-effective Entry-level Chassis Switch
?Superior Performance with up to 88.62Tbps Switching Capacity
?Support Network Virtualization (VSU & VSD) and Advanced Layer 3 Routing (OSPF, BGP)
?Carrier-class Reliability: Control Engine/ Power/ Fan Redundancy, Hot-swappable Components
?Support Large-scale MAC (up to 64K) and ARP Table (up to 20K)
?Support PoE/ PoE+ with Independent PoE Power Supply Module', '4000.00', '200', 'https://www.samcomsecurity.com/attachment/products/1607932321_1.jpg', NULL, 'active', '2025-11-12 09:10:48', '2025-11-22 13:28:20.835635', '1');

--
-- Table: members
--

DROP TABLE IF EXISTS "members" CASCADE;

CREATE TABLE "members" (
    "member_id" integer NOT NULL DEFAULT nextval('members_member_id_seq'::regclass),
    "user_id" integer NOT NULL,
    "first_name" character varying NOT NULL,
    "last_name" character varying NOT NULL,
    "date_of_birth" date,
    "address" text,
    "district" character varying,
    "province" character varying,
    "postal_code" character varying,
    "profile_image" character varying,
    "membership_level" character varying DEFAULT 'bronze'::character varying,
    "points" integer
);

--
-- Table: brands
--

DROP TABLE IF EXISTS "brands" CASCADE;

CREATE TABLE "brands" (
    "brand_id" integer NOT NULL DEFAULT nextval('brands_brand_id_seq'::regclass),
    "brand_name" character varying NOT NULL,
    "logo_url" character varying,
    "description" text,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO "brands" ("brand_id", "brand_name", "logo_url", "description", "created_at") VALUES
('1', 'UNV', NULL, 'Uniview - กล้องวงจรปิดคุณภาพสูง', '2025-11-12 10:27:42.447299'),
('2', 'Ruijie', NULL, 'Ruijie Networks - อุปกรณ์เครือข่าย', '2025-11-12 10:27:42.447299'),
('3', 'H3C', NULL, 'H3C - Enterprise Network Solutions', '2025-11-12 10:27:42.447299'),
('4', 'Tiandy', NULL, 'Tiandy - AI Security Camera', '2025-11-12 10:27:42.447299'),
('5', 'SAMCOM', NULL, 'SAMCOM - AI Camera Solutions', '2025-11-12 10:27:42.447299');

--
-- Table: categories
--

DROP TABLE IF EXISTS "categories" CASCADE;

CREATE TABLE "categories" (
    "category_id" integer NOT NULL DEFAULT nextval('categories_category_id_seq'::regclass),
    "category_name" character varying NOT NULL,
    "parent_category_id" integer,
    "description" text,
    "icon" character varying,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO "categories" ("category_id", "category_name", "parent_category_id", "description", "icon", "created_at") VALUES
('1', 'กล้องวงจรปิด', NULL, 'ระบบกล้องวงจรปิดทุกประเภท', NULL, '2025-11-12 10:27:42.460493'),
('2', 'อุปกรณ์เครือข่าย', NULL, 'สวิตช์, เราเตอร์, ไวไฟ', NULL, '2025-11-12 10:27:42.460493'),
('3', 'IoT & AI', NULL, 'อุปกรณ์ AI และ IoT', NULL, '2025-11-12 10:27:42.460493'),
('4', 'IP Camera', '1', NULL, NULL, '2025-11-12 10:27:42.468215'),
('5', 'PTZ Camera', '1', NULL, NULL, '2025-11-12 10:27:42.468215'),
('6', 'NVR/DVR', '1', NULL, NULL, '2025-11-12 10:27:42.468215'),
('7', 'Switches', '2', NULL, NULL, '2025-11-12 10:27:42.468215'),
('8', 'Router', '2', NULL, NULL, '2025-11-12 10:27:42.468215'),
('9', 'Wireless', '2', NULL, NULL, '2025-11-12 10:27:42.468215');

--
-- Table: cart
--

DROP TABLE IF EXISTS "cart" CASCADE;

CREATE TABLE "cart" (
    "cart_id" integer NOT NULL DEFAULT nextval('cart_cart_id_seq'::regclass),
    "member_id" integer NOT NULL,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

--
-- Table: cart_items
--

DROP TABLE IF EXISTS "cart_items" CASCADE;

CREATE TABLE "cart_items" (
    "cart_item_id" integer NOT NULL DEFAULT nextval('cart_items_cart_item_id_seq'::regclass),
    "product_id" integer NOT NULL,
    "quantity" integer NOT NULL DEFAULT 1,
    "price_at_add" numeric NOT NULL,
    "added_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "user_id" bigint NOT NULL,
    "created_at" timestamp without time zone,
    "updated_at" timestamp without time zone,
    "id" bigint NOT NULL DEFAULT nextval('cart_items_id_seq'::regclass)
);

--
-- Table: order_items
--

DROP TABLE IF EXISTS "order_items" CASCADE;

CREATE TABLE "order_items" (
    "order_item_id" integer NOT NULL DEFAULT nextval('order_items_order_item_id_seq'::regclass),
    "order_id" integer NOT NULL,
    "product_id" integer NOT NULL,
    "quantity" integer NOT NULL,
    "price_at_purchase" numeric NOT NULL,
    "subtotal" numeric NOT NULL
);

--
-- Table: payments
--

DROP TABLE IF EXISTS "payments" CASCADE;

CREATE TABLE "payments" (
    "payment_id" integer NOT NULL DEFAULT nextval('payments_payment_id_seq'::regclass),
    "order_id" integer NOT NULL,
    "payment_method" character varying NOT NULL,
    "payment_status" character varying DEFAULT 'pending'::character varying,
    "payment_date" timestamp without time zone,
    "amount" numeric NOT NULL,
    "transaction_id" character varying,
    "payment_proof_url" character varying,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

--
-- Table: shipping
--

DROP TABLE IF EXISTS "shipping" CASCADE;

CREATE TABLE "shipping" (
    "shipping_id" integer NOT NULL DEFAULT nextval('shipping_shipping_id_seq'::regclass),
    "order_id" integer NOT NULL,
    "shipping_company" character varying,
    "tracking_number" character varying,
    "shipping_status" character varying DEFAULT 'preparing'::character varying,
    "shipped_date" timestamp without time zone,
    "delivered_date" timestamp without time zone,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

--
-- Table: orders
--

DROP TABLE IF EXISTS "orders" CASCADE;

CREATE TABLE "orders" (
    "order_id" integer NOT NULL DEFAULT nextval('orders_order_id_seq'::regclass),
    "member_id" integer NOT NULL,
    "order_date" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "total_amount" numeric NOT NULL,
    "order_status" character varying DEFAULT 'pending'::character varying,
    "shipping_address" text NOT NULL,
    "shipping_method" character varying,
    "tracking_number" character varying,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "user_id" bigint
);

--
-- Table: reviews
--

DROP TABLE IF EXISTS "reviews" CASCADE;

CREATE TABLE "reviews" (
    "review_id" integer NOT NULL DEFAULT nextval('reviews_review_id_seq'::regclass),
    "product_id" integer NOT NULL,
    "member_id" integer NOT NULL,
    "rating" integer,
    "comment" text,
    "review_images" jsonb,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

--
-- Table: wishlists
--

DROP TABLE IF EXISTS "wishlists" CASCADE;

CREATE TABLE "wishlists" (
    "wishlist_id" integer NOT NULL DEFAULT nextval('wishlists_wishlist_id_seq'::regclass),
    "member_id" integer NOT NULL,
    "product_id" integer NOT NULL,
    "added_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP
);

--
-- Table: product_images
--

DROP TABLE IF EXISTS "product_images" CASCADE;

CREATE TABLE "product_images" (
    "product_image_id" integer NOT NULL DEFAULT nextval('product_images_product_image_id_seq'::regclass),
    "product_id" integer NOT NULL,
    "image_url" character varying NOT NULL,
    "created_at" timestamp without time zone DEFAULT now(),
    "updated_at" timestamp without time zone DEFAULT now()
);

--
-- Table: migrations
--

DROP TABLE IF EXISTS "migrations" CASCADE;

CREATE TABLE "migrations" (
    "id" integer NOT NULL DEFAULT nextval('migrations_id_seq'::regclass),
    "migration" character varying NOT NULL,
    "batch" integer NOT NULL
);

INSERT INTO "migrations" ("id", "migration", "batch") VALUES
('1', '2025_11_18_024148_create_carts_table', '1'),
('3', '2025_11_22_041748_add_view_count_to_products_table', '2'),
('4', '2025_11_22_065015_drop_carts_table', '3'),
('5', '2025_11_22_065557_alter_cart_items_table_add_user_id', '4');

--
-- Table: users
--

DROP TABLE IF EXISTS "users" CASCADE;

CREATE TABLE "users" (
    "user_id" integer NOT NULL DEFAULT nextval('users_user_id_seq'::regclass),
    "username" character varying NOT NULL,
    "password" character varying NOT NULL,
    "email" character varying NOT NULL,
    "phone" character varying,
    "role_id" integer NOT NULL DEFAULT 2,
    "created_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "updated_at" timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    "firstname" character varying,
    "lastname" character varying,
    "address" text,
    "subdistrict" character varying,
    "district" character varying,
    "province" character varying,
    "zipcode" character varying
);

INSERT INTO "users" ("user_id", "username", "password", "email", "phone", "role_id", "created_at", "updated_at", "firstname", "lastname", "address", "subdistrict", "district", "province", "zipcode") VALUES
('1', 'admin', '$2a$10$XQBHjQ9ZqYqX0rK5Yx4YzeGJYJh8L0Y9K0Z1Z2Z3Z4Z5Z6Z7Z8Z9Za', 'admin@amnet.co.th', '0812345678', '1', '2025-11-12 10:27:42.473155', '2025-11-12 10:27:42.473155', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
('8', 'Hacker505', '$2y$12$ok4Y4.UM.l76AQqbL4z5me8Egt4e32tgkF8PdFZdSnpYBEz9A8QA6', 'pathpure7779@gmail.com', NULL, '2', '2025-11-14 01:22:40', '2025-11-14 01:22:40', 'ถิรพุทธ', 'ศรีมูล', '79/17', '300114', '3001', '30', '30000'),
('9', 'creammy_1012', '$2y$12$o2PZjr/dLoHgMvar0wsXcux208TOFJnkGWiDxw0W3GsUExLBA2g.O', 'lalita.ki@rmuti.ac.th', '0888888888', '2', '2025-11-17 07:14:46', '2025-11-17 07:14:46', 'ลลิตา', 'กิ่งพาน', '99/99', 'ห้วยบง', 'ด่านขุนทด', 'นครราชสีมา', '30210'),
('7', 'Hacker404', '$2y$12$K5835VMKscASfk1dsOAXeOjXLkz8nrE7mla.7kRXM8CIPqFJ9jNiO', 'witchacid7@gmail.com', NULL, '1', '2025-11-12 05:44:15', '2025-11-17 15:13:52.332556', 'ถิรพุทธ', 'ศรีมูล', '79/17', 'พุดซา', 'เมือง', 'นครราชสีมา', '30000');

