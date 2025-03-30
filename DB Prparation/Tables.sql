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
