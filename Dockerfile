FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . /var/www/

# Define ARGs for database configuration
ARG DB_HOST
ARG DB_USER
ARG DB_PASS
ARG DB_DATABASE
ARG DB_DRIVER

# Convert ARGs to ENVs so they're available at runtime
ENV DB_HOST=${DB_HOST}
ENV DB_USER=${DB_USER}
ENV DB_PASS=${DB_PASS}
ENV DB_DATABASE=${DB_DATABASE}
ENV DB_DRIVER=${DB_DRIVER}

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader

# Configure Apache
RUN a2enmod rewrite
# Set the document root to the public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set proper permissions
RUN chown -R www-data:www-data /var/www
RUN chmod -R 755 /var/www

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]