CREATE DATABASE IF NOT EXISTS goswami_industry;
USE goswami_industry;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    product_id INT,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

-- Insert sample admin (password is 'admin123')
INSERT INTO users (name, email, password, role) VALUES ('Admin', 'admin@goswami.com', '$2y$10$K2d8miVNaYiRiXPjfZEhre3SbmzPc7oHd/4HrVLEca.2QmOCF0K6.', 'admin');

-- Insert sample categories
INSERT INTO categories (name) VALUES ('Electronics'), ('Clothing'), ('Home & Kitchen');

-- Insert sample products
INSERT INTO products (category_id, name, description, price, image) VALUES 
(1, 'Smartphone X', 'Latest smartphone with amazing features, edge-to-edge display, and professional camera system.', 699.99, 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&q=80'),
(1, 'Laptop Pro', 'High-performance laptop for professionals. Features the latest processor and all-day battery life.', 1299.99, 'https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=500&q=80'),
(2, 'Premium T-Shirt', 'Comfortable 100% cotton t-shirt with a modern fit. Perfect for everyday wear.', 19.99, 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=500&q=80'),
(3, 'Smart Coffee Maker', 'Brew the perfect cup of coffee every morning with our smart coffee maker.', 149.99, 'https://images.unsplash.com/photo-1517668808822-9ebb02f2a0e6?w=500&q=80');
