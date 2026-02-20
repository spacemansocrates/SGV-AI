-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 08:43 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sgv`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `customer_type` varchar(30) NOT NULL DEFAULT 'individual',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_note_items`
--

CREATE TABLE `delivery_note_items` (
  `delivery_note_item_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_note_id` bigint(20) UNSIGNED NOT NULL,
  `lpo_item_id` bigint(20) UNSIGNED NOT NULL,
  `qty_received_now` decimal(12,2) NOT NULL,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `finance_transactions`
--

CREATE TABLE `finance_transactions` (
  `transaction_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_no` varchar(30) NOT NULL,
  `requisition_id` bigint(20) UNSIGNED DEFAULT NULL,
  `lpo_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ticket_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(12,2) NOT NULL,
  `currency` varchar(10) NOT NULL DEFAULT 'MWK',
  `payment_method` varchar(30) NOT NULL,
  `payment_reference` varchar(80) DEFAULT NULL,
  `payee_name` varchar(180) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'released_receipt_pending',
  `receipt_attachment_path` varchar(255) DEFAULT NULL,
  `receipt_number` varchar(80) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `finance_transactions`
--
DELIMITER $$
CREATE TRIGGER `bi_finance_txn_receipt_guard` BEFORE INSERT ON `finance_transactions` FOR EACH ROW BEGIN
  IF NEW.status = 'released_receipt_attached' AND (NEW.receipt_attachment_path IS NULL OR NEW.receipt_attachment_path = '') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Cannot mark receipt attached without receipt_attachment_path';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `bu_finance_txn_receipt_guard` BEFORE UPDATE ON `finance_transactions` FOR EACH ROW BEGIN
  IF NEW.status = 'released_receipt_attached' AND (NEW.receipt_attachment_path IS NULL OR NEW.receipt_attachment_path = '') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'Cannot mark receipt attached without receipt_attachment_path';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_items`
--

CREATE TABLE `inventory_items` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(180) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(80) DEFAULT NULL,
  `sku` varchar(80) DEFAULT NULL,
  `barcode` varchar(80) DEFAULT NULL,
  `unit` varchar(30) DEFAULT NULL,
  `unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `current_stock` decimal(12,2) NOT NULL DEFAULT 0.00,
  `reorder_level` decimal(12,2) NOT NULL DEFAULT 0.00,
  `location` varchar(120) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_cards`
--

CREATE TABLE `job_cards` (
  `job_card_id` bigint(20) UNSIGNED NOT NULL,
  `job_card_no` varchar(30) NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to` bigint(20) UNSIGNED DEFAULT NULL,
  `job_status` varchar(40) NOT NULL DEFAULT 'Job Card Created',
  `technical_notes` text DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lpos`
--

CREATE TABLE `lpos` (
  `lpo_id` bigint(20) UNSIGNED NOT NULL,
  `lpo_no` varchar(30) NOT NULL,
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `issued_by` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'issued',
  `issued_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expected_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lpo_items`
--

CREATE TABLE `lpo_items` (
  `lpo_item_id` bigint(20) UNSIGNED NOT NULL,
  `lpo_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name_override` varchar(180) DEFAULT NULL,
  `specification` varchar(255) DEFAULT NULL,
  `qty_ordered` decimal(12,2) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `qty_received` decimal(12,2) NOT NULL DEFAULT 0.00,
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `lpo_items`
--
DELIMITER $$
CREATE TRIGGER `bi_lpo_items_identity` BEFORE INSERT ON `lpo_items` FOR EACH ROW BEGIN
  IF (NEW.item_id IS NULL AND (NEW.item_name_override IS NULL OR NEW.item_name_override = ''))
     OR (NEW.item_id IS NOT NULL AND NEW.item_name_override IS NOT NULL AND NEW.item_name_override <> '') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'lpo_items must have either item_id OR item_name_override';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `bu_lpo_items_identity` BEFORE UPDATE ON `lpo_items` FOR EACH ROW BEGIN
  IF (NEW.item_id IS NULL AND (NEW.item_name_override IS NULL OR NEW.item_name_override = ''))
     OR (NEW.item_id IS NOT NULL AND NEW.item_name_override IS NOT NULL AND NEW.item_name_override <> '') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'lpo_items must have either item_id OR item_name_override';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_requisitions`
--

CREATE TABLE `purchase_requisitions` (
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `requisition_no` varchar(30) NOT NULL,
  `linked_ticket_id` bigint(20) UNSIGNED DEFAULT NULL,
  `linked_job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `justification` text NOT NULL,
  `priority` varchar(20) NOT NULL DEFAULT 'normal',
  `status` varchar(30) NOT NULL DEFAULT 'pending',
  `requested_by` bigint(20) UNSIGNED NOT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approval_comment` text DEFAULT NULL,
  `rejection_reason_code` varchar(80) DEFAULT NULL,
  `rejection_comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requisition_items`
--

CREATE TABLE `requisition_items` (
  `requisition_item_id` bigint(20) UNSIGNED NOT NULL,
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_name_override` varchar(180) DEFAULT NULL,
  `specification` varchar(255) DEFAULT NULL,
  `qty` decimal(12,2) NOT NULL,
  `estimated_unit_cost` decimal(12,2) NOT NULL DEFAULT 0.00,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `requisition_items`
--
DELIMITER $$
CREATE TRIGGER `bi_requisition_items_identity` BEFORE INSERT ON `requisition_items` FOR EACH ROW BEGIN
  IF (NEW.item_id IS NULL AND (NEW.item_name_override IS NULL OR NEW.item_name_override = ''))
     OR (NEW.item_id IS NOT NULL AND NEW.item_name_override IS NOT NULL AND NEW.item_name_override <> '') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'requisition_items must have either item_id OR item_name_override';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `bu_requisition_items_identity` BEFORE UPDATE ON `requisition_items` FOR EACH ROW BEGIN
  IF (NEW.item_id IS NULL AND (NEW.item_name_override IS NULL OR NEW.item_name_override = ''))
     OR (NEW.item_id IS NOT NULL AND NEW.item_name_override IS NOT NULL AND NEW.item_name_override <> '') THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'requisition_items must have either item_id OR item_name_override';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `rfqs`
--

CREATE TABLE `rfqs` (
  `rfq_id` bigint(20) UNSIGNED NOT NULL,
  `rfq_no` varchar(30) NOT NULL,
  `requisition_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfq_quotes`
--

CREATE TABLE `rfq_quotes` (
  `quote_id` bigint(20) UNSIGNED NOT NULL,
  `rfq_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'MWK',
  `lead_time_days` int(10) UNSIGNED DEFAULT NULL,
  `validity_date` date DEFAULT NULL,
  `terms` text DEFAULT NULL,
  `attachment_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfq_suppliers`
--

CREATE TABLE `rfq_suppliers` (
  `rfq_supplier_id` bigint(20) UNSIGNED NOT NULL,
  `rfq_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `sent_at` datetime DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'listed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `description`, `created_at`) VALUES
(1, 'customer_rep', 'Customer Representative', '2026-02-19 14:04:00'),
(2, 'workshop_supervisor', 'Workshop Supervisor / Chief Technician', '2026-02-19 14:04:00'),
(3, 'stores_manager', 'Stores Manager + Procurement', '2026-02-19 14:04:00'),
(4, 'ops_manager', 'Vice Director / Operations Manager', '2026-02-19 14:04:00'),
(5, 'accountant', 'Accounts Manager', '2026-02-19 14:04:00'),
(6, 'admin', 'Admin/Director', '2026-02-19 14:04:00');

-- --------------------------------------------------------

--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `movement_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `movement_type` varchar(20) NOT NULL,
  `quantity` decimal(12,2) NOT NULL,
  `reference_type` varchar(30) NOT NULL,
  `reference_id` bigint(20) UNSIGNED DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `moved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stores_issue_items`
--

CREATE TABLE `stores_issue_items` (
  `issue_item_id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED DEFAULT NULL,
  `job_card_id` bigint(20) UNSIGNED DEFAULT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `qty` decimal(12,2) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'planned',
  `issued_by` bigint(20) UNSIGNED DEFAULT NULL,
  `issued_at` datetime DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `stores_issue_items`
--
DELIMITER $$
CREATE TRIGGER `bi_stores_issue_items_ref` BEFORE INSERT ON `stores_issue_items` FOR EACH ROW BEGIN
  IF (NEW.ticket_id IS NULL AND NEW.job_card_id IS NULL)
     OR (NEW.ticket_id IS NOT NULL AND NEW.job_card_id IS NOT NULL) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'stores_issue_items must reference exactly one of ticket_id or job_card_id';
  END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `bu_stores_issue_items_ref` BEFORE UPDATE ON `stores_issue_items` FOR EACH ROW BEGIN
  IF (NEW.ticket_id IS NULL AND NEW.job_card_id IS NULL)
     OR (NEW.ticket_id IS NOT NULL AND NEW.job_card_id IS NOT NULL) THEN
    SIGNAL SQLSTATE '45000'
      SET MESSAGE_TEXT = 'stores_issue_items must reference exactly one of ticket_id or job_card_id';
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(180) NOT NULL,
  `phone` varchar(60) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `payment_terms` varchar(120) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supplier_delivery_notes`
--

CREATE TABLE `supplier_delivery_notes` (
  `delivery_note_id` bigint(20) UNSIGNED NOT NULL,
  `delivery_note_no` varchar(60) NOT NULL,
  `lpo_id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `received_by` bigint(20) UNSIGNED NOT NULL,
  `received_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'received',
  `attachment_path` varchar(255) DEFAULT NULL,
  `supplier_invoice_no` varchar(60) DEFAULT NULL,
  `supplier_invoice_path` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `ticket_no` varchar(30) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `bike_model` varchar(120) DEFAULT NULL,
  `bike_vin` varchar(120) DEFAULT NULL,
  `bike_mileage` int(10) UNSIGNED DEFAULT NULL,
  `complaint_description` text NOT NULL,
  `warranty_flag` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(40) NOT NULL DEFAULT 'Submitted',
  `current_assignee_role` varchar(50) DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `ops_manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `stores_manager_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ticket_timeline`
--

CREATE TABLE `ticket_timeline` (
  `action_id` bigint(20) UNSIGNED NOT NULL,
  `ticket_id` bigint(20) UNSIGNED NOT NULL,
  `actor_user_id` bigint(20) UNSIGNED NOT NULL,
  `action_type` varchar(40) NOT NULL,
  `from_status` varchar(40) DEFAULT NULL,
  `to_status` varchar(40) DEFAULT NULL,
  `reason_code` varchar(80) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` int(10) UNSIGNED NOT NULL,
  `username` varchar(60) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(120) DEFAULT NULL,
  `full_name` varchar(120) NOT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `role_id`, `username`, `password_hash`, `email`, `full_name`, `phone`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 6, 'admin', '$2y$10$5H2ptXipwv.bbLiWNHZur.dNhjpZ2g5qzp1.KqfVZXaBmM5tkRjkG', 'admin@sgv.com', 'Admin User', NULL, 1, '2026-02-20 09:40:21', '2026-02-19 17:52:08', '2026-02-20 07:40:21'),
(2, 1, 'customer_rep', '$2y$10$5H2ptXipwv.bbLiWNHZur.dNhjpZ2g5qzp1.KqfVZXaBmM5tkRjkG', 'customer_rep@sgv.com', 'Customer Rep', NULL, 1, '2026-02-20 09:17:13', '2026-02-19 17:52:08', '2026-02-20 07:17:13'),
(3, 2, 'workshop_supervisor', '$2y$10$5H2ptXipwv.bbLiWNHZur.dNhjpZ2g5qzp1.KqfVZXaBmM5tkRjkG', 'workshop@sgv.local', 'Workshop Supervisor', NULL, 1, NULL, '2026-02-19 17:52:08', '2026-02-19 17:57:19'),
(4, 3, 'stores_manager', '$2y$10$5H2ptXipwv.bbLiWNHZur.dNhjpZ2g5qzp1.KqfVZXaBmM5tkRjkG', 'stores@sgv.com', 'Stores Manager', NULL, 1, NULL, '2026-02-19 17:52:08', '2026-02-20 07:07:32'),
(5, 4, 'ops_manager', '$2y$10$5H2ptXipwv.bbLiWNHZur.dNhjpZ2g5qzp1.KqfVZXaBmM5tkRjkG', 'ops@sgv.com', 'Operations Manager', NULL, 1, NULL, '2026-02-19 17:52:08', '2026-02-20 07:07:38'),
(6, 5, 'accountant', '$2y$10$5H2ptXipwv.bbLiWNHZur.dNhjpZ2g5qzp1.KqfVZXaBmM5tkRjkG', 'accountant@sgv.com', 'Accountant', NULL, 1, NULL, '2026-02-19 17:52:08', '2026-02-20 07:07:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `idx_customers_name` (`name`),
  ADD KEY `fk_customers_created_by` (`created_by`);

--
-- Indexes for table `delivery_note_items`
--
ALTER TABLE `delivery_note_items`
  ADD PRIMARY KEY (`delivery_note_item_id`),
  ADD KEY `fk_dn_items_dn` (`delivery_note_id`),
  ADD KEY `fk_dn_items_lpo_item` (`lpo_item_id`);

--
-- Indexes for table `finance_transactions`
--
ALTER TABLE `finance_transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD UNIQUE KEY `transaction_no` (`transaction_no`),
  ADD KEY `idx_txn_status` (`status`),
  ADD KEY `idx_txn_req` (`requisition_id`),
  ADD KEY `idx_txn_lpo` (`lpo_id`),
  ADD KEY `idx_txn_ticket` (`ticket_id`),
  ADD KEY `idx_txn_job` (`job_card_id`),
  ADD KEY `fk_txn_user` (`created_by`);

--
-- Indexes for table `inventory_items`
--
ALTER TABLE `inventory_items`
  ADD PRIMARY KEY (`item_id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD UNIQUE KEY `barcode` (`barcode`),
  ADD KEY `idx_inventory_name` (`item_name`),
  ADD KEY `idx_inventory_category` (`category`);

--
-- Indexes for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD PRIMARY KEY (`job_card_id`),
  ADD UNIQUE KEY `job_card_no` (`job_card_no`),
  ADD UNIQUE KEY `ticket_id` (`ticket_id`),
  ADD KEY `idx_job_status` (`job_status`),
  ADD KEY `idx_job_cards_assigned_to` (`assigned_to`,`job_status`);

--
-- Indexes for table `lpos`
--
ALTER TABLE `lpos`
  ADD PRIMARY KEY (`lpo_id`),
  ADD UNIQUE KEY `lpo_no` (`lpo_no`),
  ADD KEY `idx_lpo_supplier` (`supplier_id`),
  ADD KEY `fk_lpo_req` (`requisition_id`),
  ADD KEY `fk_lpo_user` (`issued_by`);

--
-- Indexes for table `lpo_items`
--
ALTER TABLE `lpo_items`
  ADD PRIMARY KEY (`lpo_item_id`),
  ADD KEY `idx_lpo_items_lpo` (`lpo_id`),
  ADD KEY `fk_lpo_items_item` (`item_id`);

--
-- Indexes for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  ADD PRIMARY KEY (`requisition_id`),
  ADD UNIQUE KEY `requisition_no` (`requisition_no`),
  ADD KEY `idx_pr_status` (`status`),
  ADD KEY `idx_pr_ticket` (`linked_ticket_id`),
  ADD KEY `idx_pr_job` (`linked_job_card_id`),
  ADD KEY `fk_pr_approved` (`approved_by`),
  ADD KEY `idx_pr_requested_by` (`requested_by`,`status`);

--
-- Indexes for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD PRIMARY KEY (`requisition_item_id`),
  ADD KEY `idx_req_items_req` (`requisition_id`),
  ADD KEY `fk_req_items_item` (`item_id`);

--
-- Indexes for table `rfqs`
--
ALTER TABLE `rfqs`
  ADD PRIMARY KEY (`rfq_id`),
  ADD UNIQUE KEY `rfq_no` (`rfq_no`),
  ADD KEY `idx_rfq_req` (`requisition_id`),
  ADD KEY `fk_rfq_user` (`created_by`);

--
-- Indexes for table `rfq_quotes`
--
ALTER TABLE `rfq_quotes`
  ADD PRIMARY KEY (`quote_id`),
  ADD UNIQUE KEY `uq_rfq_quote` (`rfq_id`,`supplier_id`),
  ADD KEY `fk_quote_supplier` (`supplier_id`);

--
-- Indexes for table `rfq_suppliers`
--
ALTER TABLE `rfq_suppliers`
  ADD PRIMARY KEY (`rfq_supplier_id`),
  ADD UNIQUE KEY `uq_rfq_supplier` (`rfq_id`,`supplier_id`),
  ADD KEY `fk_rfq_sup_supplier` (`supplier_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`movement_id`),
  ADD KEY `idx_stock_mov_item` (`item_id`,`moved_at`),
  ADD KEY `idx_stock_mov_ref` (`reference_type`,`reference_id`),
  ADD KEY `fk_stock_mov_user` (`performed_by`);

--
-- Indexes for table `stores_issue_items`
--
ALTER TABLE `stores_issue_items`
  ADD PRIMARY KEY (`issue_item_id`),
  ADD KEY `idx_issue_ticket` (`ticket_id`),
  ADD KEY `idx_issue_job` (`job_card_id`),
  ADD KEY `fk_issue_item` (`item_id`),
  ADD KEY `fk_issue_user` (`issued_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`),
  ADD UNIQUE KEY `uq_supplier_name` (`name`);

--
-- Indexes for table `supplier_delivery_notes`
--
ALTER TABLE `supplier_delivery_notes`
  ADD PRIMARY KEY (`delivery_note_id`),
  ADD UNIQUE KEY `uq_dn` (`lpo_id`,`delivery_note_no`),
  ADD KEY `fk_dn_supplier` (`supplier_id`),
  ADD KEY `fk_dn_user` (`received_by`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`ticket_id`),
  ADD UNIQUE KEY `ticket_no` (`ticket_no`),
  ADD KEY `idx_tickets_customer` (`customer_id`),
  ADD KEY `idx_tickets_status` (`status`),
  ADD KEY `idx_tickets_created_at` (`created_at`),
  ADD KEY `fk_tickets_created_by` (`created_by`),
  ADD KEY `fk_tickets_ops` (`ops_manager_id`),
  ADD KEY `fk_tickets_stores` (`stores_manager_id`),
  ADD KEY `idx_tickets_assignee_role` (`current_assignee_role`,`status`);

--
-- Indexes for table `ticket_timeline`
--
ALTER TABLE `ticket_timeline`
  ADD PRIMARY KEY (`action_id`),
  ADD KEY `idx_timeline_ticket` (`ticket_id`,`created_at`),
  ADD KEY `fk_timeline_actor` (`actor_user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_role` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_note_items`
--
ALTER TABLE `delivery_note_items`
  MODIFY `delivery_note_item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `finance_transactions`
--
ALTER TABLE `finance_transactions`
  MODIFY `transaction_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_items`
--
ALTER TABLE `inventory_items`
  MODIFY `item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_cards`
--
ALTER TABLE `job_cards`
  MODIFY `job_card_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lpos`
--
ALTER TABLE `lpos`
  MODIFY `lpo_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lpo_items`
--
ALTER TABLE `lpo_items`
  MODIFY `lpo_item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  MODIFY `requisition_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `requisition_items`
--
ALTER TABLE `requisition_items`
  MODIFY `requisition_item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rfqs`
--
ALTER TABLE `rfqs`
  MODIFY `rfq_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rfq_quotes`
--
ALTER TABLE `rfq_quotes`
  MODIFY `quote_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rfq_suppliers`
--
ALTER TABLE `rfq_suppliers`
  MODIFY `rfq_supplier_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `movement_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stores_issue_items`
--
ALTER TABLE `stores_issue_items`
  MODIFY `issue_item_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `supplier_delivery_notes`
--
ALTER TABLE `supplier_delivery_notes`
  MODIFY `delivery_note_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tickets`
--
ALTER TABLE `tickets`
  MODIFY `ticket_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ticket_timeline`
--
ALTER TABLE `ticket_timeline`
  MODIFY `action_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `fk_customers_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `delivery_note_items`
--
ALTER TABLE `delivery_note_items`
  ADD CONSTRAINT `fk_dn_items_dn` FOREIGN KEY (`delivery_note_id`) REFERENCES `supplier_delivery_notes` (`delivery_note_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dn_items_lpo_item` FOREIGN KEY (`lpo_item_id`) REFERENCES `lpo_items` (`lpo_item_id`) ON UPDATE CASCADE;

--
-- Constraints for table `finance_transactions`
--
ALTER TABLE `finance_transactions`
  ADD CONSTRAINT `fk_txn_job` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`job_card_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_txn_lpo` FOREIGN KEY (`lpo_id`) REFERENCES `lpos` (`lpo_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_txn_req` FOREIGN KEY (`requisition_id`) REFERENCES `purchase_requisitions` (`requisition_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_txn_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_txn_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `job_cards`
--
ALTER TABLE `job_cards`
  ADD CONSTRAINT `fk_job_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_job_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON UPDATE CASCADE;

--
-- Constraints for table `lpos`
--
ALTER TABLE `lpos`
  ADD CONSTRAINT `fk_lpo_req` FOREIGN KEY (`requisition_id`) REFERENCES `purchase_requisitions` (`requisition_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lpo_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lpo_user` FOREIGN KEY (`issued_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `lpo_items`
--
ALTER TABLE `lpo_items`
  ADD CONSTRAINT `fk_lpo_items_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_lpo_items_lpo` FOREIGN KEY (`lpo_id`) REFERENCES `lpos` (`lpo_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_requisitions`
--
ALTER TABLE `purchase_requisitions`
  ADD CONSTRAINT `fk_pr_approved` FOREIGN KEY (`approved_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pr_job` FOREIGN KEY (`linked_job_card_id`) REFERENCES `job_cards` (`job_card_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pr_requested` FOREIGN KEY (`requested_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pr_ticket` FOREIGN KEY (`linked_ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `requisition_items`
--
ALTER TABLE `requisition_items`
  ADD CONSTRAINT `fk_req_items_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_req_items_req` FOREIGN KEY (`requisition_id`) REFERENCES `purchase_requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rfqs`
--
ALTER TABLE `rfqs`
  ADD CONSTRAINT `fk_rfq_req` FOREIGN KEY (`requisition_id`) REFERENCES `purchase_requisitions` (`requisition_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rfq_user` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rfq_quotes`
--
ALTER TABLE `rfq_quotes`
  ADD CONSTRAINT `fk_quote_rfq` FOREIGN KEY (`rfq_id`) REFERENCES `rfqs` (`rfq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_quote_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON UPDATE CASCADE;

--
-- Constraints for table `rfq_suppliers`
--
ALTER TABLE `rfq_suppliers`
  ADD CONSTRAINT `fk_rfq_sup_rfq` FOREIGN KEY (`rfq_id`) REFERENCES `rfqs` (`rfq_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_rfq_sup_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON UPDATE CASCADE;

--
-- Constraints for table `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `fk_stock_mov_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_stock_mov_user` FOREIGN KEY (`performed_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `stores_issue_items`
--
ALTER TABLE `stores_issue_items`
  ADD CONSTRAINT `fk_issue_item` FOREIGN KEY (`item_id`) REFERENCES `inventory_items` (`item_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_issue_job` FOREIGN KEY (`job_card_id`) REFERENCES `job_cards` (`job_card_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_issue_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_issue_user` FOREIGN KEY (`issued_by`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `supplier_delivery_notes`
--
ALTER TABLE `supplier_delivery_notes`
  ADD CONSTRAINT `fk_dn_lpo` FOREIGN KEY (`lpo_id`) REFERENCES `lpos` (`lpo_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dn_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_dn_user` FOREIGN KEY (`received_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE;

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `fk_tickets_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tickets_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tickets_ops` FOREIGN KEY (`ops_manager_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_tickets_stores` FOREIGN KEY (`stores_manager_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `ticket_timeline`
--
ALTER TABLE `ticket_timeline`
  ADD CONSTRAINT `fk_timeline_actor` FOREIGN KEY (`actor_user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_timeline_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `tickets` (`ticket_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
