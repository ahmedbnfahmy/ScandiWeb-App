# GraphQL E-Commerce API

A lightweight, PHP-based GraphQL API for e-commerce applications with product and order management capabilities.

## Overview

This application provides a GraphQL backend service for managing products, attributes, and orders. It uses a modern PHP architecture with GraphQL as the API layer, providing flexible querying capabilities for client applications.

## Technologies

- PHP 7.4+
- GraphQL (webonyx/graphql-php)
- FastRoute for routing
- MySQL/MariaDB
- Docker support

## Features

- Product management with attribute support
- Order creation and management
- GraphQL API for flexible data querying
- CORS support
- Rate limiting
- Error handling and logging

## Installation

### Prerequisites

- PHP 7.4 or higher
- Composer
- MySQL/MariaDB
- Docker (optional)

### Local Setup

1. Clone the repository:
   ```
   git clone [repository-url]
   cd [repository-name]
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Configure environment variables:
   - Copy `.env.example` to `.env` (if not present, create it)
   - Configure database connection settings

4. Set up the database:
   - Import the database schema from `Schema/` directory if available
   - Ensure the database user has appropriate permissions

5. Start the server:
   ```
   php -S localhost:8000 -t public
   ```

### Docker Setup

1. Build and run the Docker container:
   ```
   docker build -t ecommerce-api .
   docker run -p 8000:80 ecommerce-api
   ```

## API Usage

The API is accessible via a single GraphQL endpoint:
