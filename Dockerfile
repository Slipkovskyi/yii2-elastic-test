# Use the official PHP image
FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    && docker-php-ext-install pdo pdo_mysql \
    && rm -r /var/lib/apt/lists/*

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /var/www/html

# Copy the current directory to the container (if needed)
COPY ./ /var/www/html
