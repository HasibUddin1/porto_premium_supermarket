-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 05, 2026 at 10:51 AM
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
-- Database: `porto_premium_supermarket`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Fresh Fruits', 'fresh-fruits', 'categories/fresh-fruits.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(2, 'Fresh Vegetables', 'fresh-vegetables', 'categories/fresh-vegetables.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(3, 'Fresh Meat', 'fresh-meat', 'categories/fresh-meat.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(4, 'Fish & Seafood', 'fish-seafood', 'categories/fish-seafood.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(5, 'Grocery', 'grocery', 'categories/grocery.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(6, 'Snacks & Biscuits', 'snacks-biscuits', 'categories/snacks-biscuits.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(7, 'Canned Foods', 'canned-foods', 'categories/canned-foods.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(8, 'Household Items', 'household-items', 'categories/household-items.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(9, 'Dairy Products', 'dairy-products', 'categories/dairy-products.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26'),
(10, 'Frozen Foods', 'frozen-foods', 'categories/frozen-foods.webp', '2026-07-25 19:20:26', '2026-07-25 19:20:26');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','completed','cancelled') NOT NULL DEFAULT 'pending',
  `delivered_at` timestamp NULL DEFAULT NULL,
  `shipping_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `customer_name` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `phone` varchar(20) NOT NULL DEFAULT '',
  `billing_address` varchar(255) NOT NULL DEFAULT '',
  `payment_method` varchar(50) NOT NULL DEFAULT '',
  `payment_status` enum('unpaid','paid','failed') NOT NULL DEFAULT 'unpaid',
  `stripe_session_id` varchar(255) DEFAULT NULL,
  `stripe_payment_intent` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `delivered_at`, `shipping_address`, `created_at`, `updated_at`, `customer_name`, `email`, `phone`, `billing_address`, `payment_method`, `payment_status`, `stripe_session_id`, `stripe_payment_intent`) VALUES
(1, 1, 10.28, 'pending', NULL, '137/B Middle Paikpara, Porto, Portugal', '2026-08-05 06:30:35', '2026-08-05 06:30:35', 'Hasib Uddin', 'uhasib959@gmail.com', '01603412868', '137/B Middle Paikpara, Porto, Portugal', 'stripe', 'unpaid', NULL, NULL),
(2, 1, 10.28, 'pending', NULL, '137/B Middle Paikpara, Porto, Portugal', '2026-08-05 06:36:59', '2026-08-05 06:36:59', 'Hasib Uddin', 'uhasib959@gmail.com', '01603412868', '137/B Middle Paikpara, Porto, Portugal', 'stripe', 'unpaid', NULL, NULL),
(3, 1, 10.28, 'processing', NULL, '137/B Middle Paikpara, Porto, Portugal', '2026-08-05 06:38:38', '2026-08-05 06:40:01', 'Hasib Uddin', 'uhasib959@gmail.com', '01603412868', '137/B Middle Paikpara, Porto, Portugal', 'stripe', 'paid', 'cs_test_b1Pem8aOpGhCA8actv7pK0VjdfLC1MBX7Jt2YIYbVp5me6HcU32hQNhI5P', 'pi_3U0yUrE3L5rDi1MP1cNe9PWW');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 9, 1, 4.29),
(2, 1, 8, 1, 5.99),
(3, 2, 9, 1, 4.29),
(4, 2, 8, 1, 5.99),
(5, 3, 9, 1, 4.29),
(6, 3, 8, 1, 5.99);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `status` enum('New','Hot') DEFAULT 'New',
  `total_sales` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `image`, `price`, `description`, `tags`, `status`, `total_sales`, `created_at`, `updated_at`) VALUES
(1, 1, 'Fresh Red Apples', 'fresh-red-apples', 'products/fresh-red-apples.webp', 4.99, 'Premium quality fresh red apples sourced daily to ensure crisp texture and natural sweetness.', 'Fresh,Fruit,Healthy,Daily', 'Hot', 523, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(2, 2, 'Organic Carrots', 'organic-carrots', 'products/organic-carrots.webp', 2.99, 'Fresh organic carrots packed with vitamins, perfect for cooking, salads, and healthy meals.', 'Organic,Vegetables,Healthy', 'New', 48, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(3, 3, 'Premium Beef Steak', 'premium-beef-steak', 'products/premium-beef-steak.webp', 18.99, 'Fresh premium beef steak prepared daily by our professional butcher for maximum quality.', 'Beef,Fresh,Protein,Butcher', 'Hot', 356, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(4, 4, 'Fresh Atlantic Salmon', 'fresh-atlantic-salmon', 'products/fresh-atlantic-salmon.webp', 21.49, 'Fresh Atlantic salmon rich in omega-3, carefully handled to maintain freshness.', 'Fish,Seafood,Fresh,Omega-3', 'Hot', 291, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(5, 5, 'Premium Basmati Rice', 'premium-basmati-rice', 'products/premium-basmati-rice.webp', 14.99, 'Long grain premium basmati rice with excellent aroma and texture for everyday meals.', 'Rice,Grocery,Premium', 'Hot', 415, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(6, 5, 'Extra Virgin Olive Oil', 'extra-virgin-olive-oil', 'products/extra-virgin-olive-oil.webp', 11.99, 'Cold-pressed extra virgin olive oil suitable for cooking and healthy lifestyles.', 'Oil,Healthy,Grocery', 'New', 73, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(7, 6, 'Chocolate Cream Biscuits', 'chocolate-cream-biscuits', 'products/chocolate-cream-biscuits.webp', 3.49, 'Crunchy chocolate cream biscuits perfect for tea time and family snacks.', 'Snack,Biscuits,Chocolate', 'Hot', 267, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(8, 7, 'Premium Tuna Chunks', 'premium-tuna-chunks', 'products/premium-tuna-chunks.webp', 5.99, 'Premium canned tuna chunks preserved for freshness and rich protein content.', 'Canned,Tuna,Protein', 'New', 92, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(9, 9, 'Fresh Whole Milk', 'fresh-whole-milk', 'products/fresh-whole-milk.webp', 4.29, 'Fresh whole milk from carefully selected dairy farms with rich natural taste.', 'Milk,Dairy,Fresh', 'Hot', 486, '2026-07-25 19:21:01', '2026-07-25 19:21:01'),
(10, 10, 'Frozen Chicken Nuggets', 'frozen-chicken-nuggets', 'products/frozen-chicken-nuggets.webp', 8.49, 'Ready-to-cook frozen chicken nuggets made from premium quality chicken meat.', 'Frozen,Chicken,Ready to Cook', 'New', 61, '2026-07-25 19:21:01', '2026-07-25 19:21:01');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`id`, `product_id`, `customer_name`, `rating`, `review`, `created_at`) VALUES
(1, 1, 'Emily Johnson', 5, 'Fresh and delicious apples. Excellent quality.', '2026-07-25 19:21:17'),
(2, 1, 'Michael Brown', 4, 'Very crispy and sweet.', '2026-07-25 19:21:17'),
(3, 3, 'David Wilson', 5, 'The beef quality is excellent and very fresh.', '2026-07-25 19:21:17'),
(4, 4, 'Sarah Miller', 5, 'Fresh seafood with great taste.', '2026-07-25 19:21:17'),
(5, 5, 'James Anderson', 5, 'Premium rice with amazing aroma.', '2026-07-25 19:21:17'),
(6, 7, 'Olivia Smith', 4, 'Great biscuits for evening snacks.', '2026-07-25 19:21:17'),
(7, 9, 'Daniel Clark', 5, 'Very fresh milk with rich flavor.', '2026-07-25 19:21:17'),
(8, 10, 'Sophia Taylor', 4, 'Good quality frozen food.', '2026-07-25 19:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `phone` varchar(20) NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `location`, `image`, `created_at`, `updated_at`) VALUES
(1, 'Hasib Uddin', 'uhasib959@gmail.com', '123456', 'user', '01603412868', '137/B Middle Paikpara', NULL, '2026-07-31 19:05:29', '2026-07-31 19:05:29'),
(2, 'Md Sohel', 'sabedoria.porto@gmail.com', '5xE^#8prhL!N8l8L', 'admin', '+351920526147', 'Porto Portugal', NULL, '2026-08-05 08:47:59', '2026-08-05 08:48:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_product` (`user_id`,`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
