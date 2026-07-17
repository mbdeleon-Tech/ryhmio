CREATE DATABASE IF NOT EXISTS `rhymio_music`;
USE `rhymio_music`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `complete_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `complete_address` text NOT NULL,
  `contact_number` varchar(40) NOT NULL,
  `role` enum('admin','buyer') NOT NULL DEFAULT 'buyer',
  `is_confirmed` tinyint(4) NOT NULL DEFAULT 0,
  `confirm_token` varchar(255) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(80) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(120) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image_url` varchar(255) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(120) NOT NULL,
  `delivery_address` text NOT NULL,
  `contact_number` varchar(40) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(60) NOT NULL,
  `order_status` varchar(40) NOT NULL DEFAULT 'Pending',
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `actor_name` varchar(100) NOT NULL,
  `action` varchar(120) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Guitars'),
(2, 'Keyboards'),
(3, 'Drums'),
(4, 'Strings'),
(5, 'Studio Gear');

INSERT INTO `products` (`category_id`, `name`, `description`, `price`, `stock`, `image_url`, `status`) VALUES
(1, 'Cedarline Acoustic Guitar', 'Warm dreadnought acoustic guitar for practice, worship sets, and small performances.', 7800.00, 12, 'assets/products/cedarline-acoustic-guitar.jpg', 'active'),
(1, 'VoltEdge Electric Guitar', 'Solid-body electric guitar with bright pickups and smooth neck action.', 14500.00, 7, 'assets/products/voltedge-electric-guitar.jpg', 'active'),
(2, 'StageLite 61-Key Keyboard', 'Portable keyboard with touch response, demo songs, and learning modes.', 11950.00, 9, 'assets/products/stagelite-keyboard.jpg', 'active'),
(3, 'PulseKick Drum Set', 'Five-piece beginner drum kit with cymbals, a throne, and a kick pedal.', 26500.00, 4, 'assets/products/pulsekick-drum-set.jpg', 'active'),
(4, 'Arco Student Violin', 'Full-size violin with a bow, case, rosin, and starter shoulder rest.', 6800.00, 10, 'assets/products/arco-student-violin.jpg', 'active'),
(5, 'ClearTone USB Microphone', 'USB condenser microphone for home recording, streaming, and online lessons.', 3200.00, 15, 'assets/products/cleartone-usb-microphone.jpg', 'active');

-- Password for both sample accounts is: password
INSERT INTO `users` (`complete_name`, `email`, `password`, `complete_address`, `contact_number`, `role`, `is_confirmed`) VALUES
('Rhymio Admin', 'admin@rhymio.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Office, Manila', '09170000000', 'admin', 1),
('Rhymio Buyer', 'buyer@rhymio.test', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Buyer Street, Quezon City', '09280000000', 'buyer', 1);

INSERT INTO `audit_logs` (`user_id`, `actor_name`, `action`, `details`) VALUES
(1, 'Rhymio Admin', 'Database seed', 'Initial admin account, buyer account, categories, and products were created.');

-- Extra Rhymio catalog items

INSERT INTO categories (name)
SELECT 'Effects Pedals'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Effects Pedals');

INSERT INTO categories (name)
SELECT 'Accessories'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Accessories');

INSERT INTO categories (name)
SELECT 'Ukuleles'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Ukuleles');

INSERT INTO categories (name)
SELECT 'Brass & Woodwinds'
WHERE NOT EXISTS (SELECT 1 FROM categories WHERE name = 'Brass & Woodwinds');

INSERT INTO products (category_id, name, description, price, stock, image_url, status)
SELECT id, 'BlueWave Chorus Pedal', 'Compact chorus pedal for shimmering clean tones and wide modulation effects.', 2450.00, 8, 'assets/products/bluewave-chorus-pedal.jpg', 'active'
FROM categories WHERE name = 'Effects Pedals';

INSERT INTO products (category_id, name, description, price, stock, image_url, status)
SELECT id, 'DriveBox Overdrive Pedal', 'Warm overdrive pedal for blues, rock, worship, and lead guitar tones.', 2850.00, 6, 'assets/products/drivebox-overdrive-pedal.jpg', 'active'
FROM categories WHERE name = 'Effects Pedals';

INSERT INTO products (category_id, name, description, price, stock, image_url, status)
SELECT id, 'Rhymio Clip-On Tuner', 'Chromatic clip-on tuner for guitar, bass, violin, and ukulele players.', 650.00, 24, 'assets/products/rhymio-clip-on-tuner.jpg', 'active'
FROM categories WHERE name = 'Accessories';

INSERT INTO products (category_id, name, description, price, stock, image_url, status)
SELECT id, 'Padded Guitar Gig Bag', 'Lightweight padded gig bag with shoulder straps and an accessory pocket.', 1350.00, 14, 'assets/products/padded-guitar-gig-bag.jpg', 'active'
FROM categories WHERE name = 'Accessories';

INSERT INTO products (category_id, name, description, price, stock, image_url, status)
SELECT id, 'IslandTone Concert Ukulele', 'Concert ukulele with a bright tone, smooth fretboard, and starter strings.', 3900.00, 11, 'assets/products/islandtone-concert-ukulele.jpg', 'active'
FROM categories WHERE name = 'Ukuleles';

INSERT INTO products (category_id, name, description, price, stock, image_url, status)
SELECT id, 'Mahogany Soprano Ukulele', 'Compact soprano ukulele for beginners, travel practice, and casual playing.', 2450.00, 13, 'assets/products/mahogany-soprano-ukulele.jpg', 'active'
FROM categories WHERE name = 'Ukuleles';

INSERT INTO products (category_id, name, description, price, stock, image_url, status)
SELECT id, 'Student Alto Saxophone', 'Entry-level alto saxophone with a case, neck strap, mouthpiece, and reeds.', 28500.00, 3, 'assets/products/student-alto-saxophone.jpg', 'active'
FROM categories WHERE name = 'Brass & Woodwinds';

INSERT INTO products (category_id, name, description, price, stock, image_url, status)
SELECT id, 'Brassline Trumpet', 'Beginner trumpet with a lacquer finish, case, mouthpiece, and cleaning cloth.', 12900.00, 5, 'assets/products/brassline-trumpet.jpg', 'active'
FROM categories WHERE name = 'Brass & Woodwinds';
