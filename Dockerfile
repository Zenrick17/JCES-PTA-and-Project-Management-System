# Build frontend assets
FROM node:20-alpine AS frontend-build

WORKDIR /app

COPY package*.json ./
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources ./resources

RUN npm ci
RUN npm run build

# Use official PHP with Apache
FROM php:8.2-apache

# Install required php extensions for Laravel
RUN apt-get update && apt-get install -y \
	git \
	unzip \
	libpq-dev \
	libzip-dev \
	zip \
	&& docker-php-ext-install pdo_mysql pdo_pgsql zip \
	&& rm -rf /var/lib/apt/lists/*

#Enable Apache mod_rewrite (needed for Laravel routing)
RUN a2enmod rewrite

# Set Apache DocumentRoot to /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf && sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/apache2.conf

# Suppress Apache ServerName warning
RUN printf 'ServerName localhost\n' > /etc/apache2/conf-available/servername.conf && a2enconf servername

# Copy App Code
COPY . /var/www/html/

# Copy built frontend assets into the runtime image
COPY --from=frontend-build /app/public/build /var/www/html/public/build

# Remove development artifacts and ensure Laravel runtime directories exist
RUN rm -f /var/www/html/public/hot \
	&& rm -f /var/www/html/bootstrap/cache/*.php \
	&& mkdir -p /var/www/html/storage/framework/cache/data \
	&& mkdir -p /var/www/html/storage/framework/sessions \
	&& mkdir -p /var/www/html/storage/framework/views \
	&& mkdir -p /var/www/html/storage/logs

# Create uploads folder and set permissions
RUN mkdir -p /var/www/html/public/uploads \
	&& chown -R www-data:www-data /var/www/html/public/uploads \
	&& chmod -R 755 /var/www/html/public/uploads

# Set working directory
WORKDIR /var/www/html

# Install Composer //changes
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Ensure public storage assets are reachable in the container
RUN rm -rf /var/www/html/public/storage \
	&& ln -s /var/www/html/storage/app/public /var/www/html/public/storage \
	&& mkdir -p /var/www/html/storage/app/public/projects \
	&& mkdir -p /var/www/html/storage/app/public/receipt_img

# Set Permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/storage \
	&& chmod -R ug+rwX /var/www/html/storage /var/www/html/bootstrap/cache

# Expose Render's required port
EXPOSE 10000

# Start Apache 
CMD ["apache2-foreground"]