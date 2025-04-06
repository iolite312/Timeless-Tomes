CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `picture` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `language` varchar(255) NOT NULL,
  `genre` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`genre`)),
  `isbn` bigint(20) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `seller_id` int(11) NOT NULL
) ;

INSERT INTO `books` (`id`, `title`, `description`, `picture`, `author`, `language`, `genre`, `isbn`, `price`, `stock`, `seller_id`) VALUES
(1, 'Realm breaker', 'No', 'da9963c5-911f-4f6c-9495-f6e8418a39d9.jpeg', 'Victoria aveyard', 'English', '[\"Action\",\"Adventure\",\"Fantasy\"]', 9780063111370, 29.99, 10, 2);

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `postalcode` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `stripe_intent` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ;

INSERT INTO `orders` (`id`, `first_name`, `last_name`, `street`, `city`, `postalcode`, `user_id`, `stripe_intent`, `payment_status`, `created_at`) VALUES
(1, 'John', 'Doe', 'street 11', 'Amsterdam', '1111AA', 2, 'pi_3RArp0PQOO8LCsj40XpfRwiF', 'completed', '2025-04-06 11:57:06');

CREATE TABLE `orders_books` (
  `order_id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ;

INSERT INTO `orders_books` (`order_id`, `book_id`, `quantity`) VALUES
(1, 1, 1);

CREATE TABLE `sellers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT 0
) ;

INSERT INTO `sellers` (`id`, `name`, `user_id`, `approved`) VALUES
(1, 'Tomes', 1, 1),
(2, 'Timeless', 2, 1);

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `postalcode` int(11) DEFAULT NULL,
  `stripe_customer` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
);

INSERT INTO `users` (`id`, `email`, `password`, `first_name`, `last_name`, `role`, `profile_picture`, `street`, `city`, `postalcode`, `stripe_customer`, `created_at`) VALUES
(1, 'seller@gmail.com', '$2y$12$0RUNTJtmw4i3EKj.j/EkA..5P5.LwtU8VUk7X.2WBXqakTtyZQHXa', 'Doe', 'John', 'seller', 'profile_placeholder.png', NULL, NULL, NULL, 'cus_S51SGeoLHVE4qs', '2025-04-06 11:30:21'),
(2, 'admin@gmail.com', '$2y$12$T4TzTwSBddbT0oEm3XX8Q.YmiIWCtbu95WkH4.gAsFlMj2YQ4qK7q', 'John', 'Doe', 'admin', 'profile_placeholder.png', NULL, NULL, NULL, 'cus_S51ilmDLoVQ3iB', '2025-04-06 11:46:59');


ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`);

ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `orders_books`
  ADD PRIMARY KEY (`order_id`,`book_id`),
  ADD KEY `book_id` (`book_id`);

ALTER TABLE `sellers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);


ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `sellers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;


ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `sellers` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

ALTER TABLE `orders`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

ALTER TABLE `orders_books`
  ADD CONSTRAINT `orders_books_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`),
  ADD CONSTRAINT `orders_books_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

ALTER TABLE `sellers`
  ADD CONSTRAINT `sellers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;
