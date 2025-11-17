-- เพิ่ม column user_id ลงในตาราง orders
ALTER TABLE orders 
ADD COLUMN user_id BIGINT;

-- สร้าง foreign key constraint
ALTER TABLE orders
ADD CONSTRAINT orders_user_id_foreign 
FOREIGN KEY (user_id) 
REFERENCES users(user_id) 
ON DELETE CASCADE;

-- สร้าง index เพื่อเพิ่มประสิทธิภาพ
CREATE INDEX orders_user_id_index ON orders(user_id);