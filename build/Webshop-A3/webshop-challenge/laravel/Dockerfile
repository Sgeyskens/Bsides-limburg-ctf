# image-name: a3/webshop-a3
# ------------------------------
# Step 1: Base image
# ------------------------------
FROM php:8.2-apache

# Set working directory
WORKDIR /var/www/html

# ------------------------------
# Step 2: Install system dependencies and configure Apache
# ------------------------------
RUN apt-get update && apt-get install -y \
    libsqlite3-dev \
    sqlite3 \
    git \
    unzip \
    libzip-dev \
    zip \
    curl \
    nodejs \
    npm \
    && docker-php-ext-install pdo pdo_sqlite zip \
    && a2enmod rewrite \
    && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# ------------------------------
# Step 3: Install Composer
# ------------------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------------------
# Step 4: Copy project files
# ------------------------------
COPY . .

# ------------------------------
# Step 5: Install PHP dependencies
# ------------------------------
RUN composer install --no-dev --optimize-autoloader

# ------------------------------
# Step 6: Setup environment file and generate app key
# ------------------------------
RUN cp .env.example .env && php artisan key:generate

# ------------------------------
# Step 7: Install Node dependencies and build assets
# ------------------------------
RUN npm install && npm run build

# ------------------------------
# Step 8: Setup database directory and file
# ------------------------------
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite

# ------------------------------
# Step 9: Run migrations and seed
# ------------------------------
RUN php artisan migrate:fresh --seed

# ------------------------------
# Step 10: Set permissions (AFTER all files are created)
# SQLite requires write access to both the database file AND directory
# (for journal/WAL files)
# ------------------------------
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod 775 /var/www/html/database \
    && chmod 666 /var/www/html/database/database.sqlite

# ------------------------------
# Step 11: Expose port 80
# ------------------------------
EXPOSE 80

# ------------------------------
# Step 12: Start Apache
# ------------------------------
CMD ["apache2-foreground"]
