CREATE TABLE categories (
    name VARCHAR(50) PRIMARY KEY,
    __typename VARCHAR(50)
);

CREATE TABLE products (
    id VARCHAR(100) PRIMARY KEY,
    name VARCHAR(255),
    inStock BOOLEAN,
    description TEXT,
    category VARCHAR(50),
    brand VARCHAR(100),
    __typename VARCHAR(50),
    FOREIGN KEY (category) REFERENCES categories(name)
);

CREATE TABLE product_gallery (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(100),
    image_url TEXT,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE attributes (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(100),
    name VARCHAR(100),
    type VARCHAR(50),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE attribute_items (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    attribute_id BIGINT,
    display_value VARCHAR(100),
    value VARCHAR(100),
    item_id VARCHAR(100),
    __typename VARCHAR(50),
    FOREIGN KEY (attribute_id) REFERENCES attributes(id)
);

CREATE TABLE prices (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    product_id VARCHAR(100),
    amount DECIMAL(10,2),
    currency_label VARCHAR(10),
    currency_symbol VARCHAR(5),
    __typename VARCHAR(50),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE `orders` (
  `id` varchar(36) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `address` text,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci

CREATE TABLE `order_items` (
  `id` varchar(36) NOT NULL,
  `order_id` varchar(36) NOT NULL,
  `product_id` varchar(36) NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE `order_item_attributes` (
  `id` varchar(36) NOT NULL,
  `order_item_id` varchar(36) NOT NULL,
  `attribute_id` BIGINT NOT NULL,
  `attribute_items_id` BIGINT NOT NULL,
  `attribute_name` varchar(255) NOT NULL,
  `display_value` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_item_id` (`order_item_id`),
  KEY `attribute_id` (`attribute_id`),
  KEY `attribute_items_id` (`attribute_items_id`),
  CONSTRAINT `order_item_attributes_ibfk_1` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_item_attributes_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`),
  CONSTRAINT `order_item_attributes_ibfk_3` FOREIGN KEY (`attribute_items_id`) REFERENCES `attribute_items` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;