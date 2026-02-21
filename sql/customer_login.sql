-- Customer login: add customer role and link users to customers
-- Run this after sgv.sql (or on existing DB).
-- After running, create a customer user like this (replace CUSTOMER_ID and use a real password hash):
--   INSERT INTO users (role_id, customer_id, username, password_hash, full_name, email)
--   SELECT r.role_id, CUSTOMER_ID, 'customer1', '$2y$10$...', c.name, c.email
--   FROM roles r, customers c WHERE r.role_name = 'customer' AND c.customer_id = CUSTOMER_ID;

-- 1. Insert customer role
INSERT INTO `roles` (`role_name`, `description`) VALUES
('customer', 'Customer (portal login for ticket creation)')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);

-- 2. Add customer_id to users (one user per customer)
ALTER TABLE `users`
  ADD COLUMN `customer_id` bigint(20) UNSIGNED DEFAULT NULL AFTER `role_id`,
  ADD UNIQUE KEY `idx_users_customer` (`customer_id`);

-- 3. FK: users.customer_id -> customers.customer_id
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE;
