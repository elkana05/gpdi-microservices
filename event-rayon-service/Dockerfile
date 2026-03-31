# Menggunakan base image PHP 8.2 versi CLI (sangat ringan, cocok untuk microservice)
FROM php:8.2-cli

# Install dependencies sistem (Linux) yang dibutuhkan Laravel & Postgres
RUN apt-get update -y && apt-get install -y \
    libpq-dev \
    unzip \
    zip \
    git \
    curl

# Install ekstensi PHP khusus untuk terhubung ke PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Install Composer langsung dari official image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory di dalam container
WORKDIR /var/www

# Copy seluruh file project Laravel Anda ke dalam container
COPY . .

# Berikan permission read/write ke folder storage dan cache (krusial untuk Laravel)
RUN chmod -R 777 storage bootstrap/cache

# Eksekusi composer install untuk memastikan semua package terinstal di dalam container
RUN composer install --optimize-autoloader --no-dev

# Eksekusi expose port sesuai blueprint microservice Anda (Port 8003)
EXPOSE 8003

# Command default yang dijalankan saat container dihidupkan
# Host 0.0.0.0 sangat penting agar bisa diakses dari luar container (API Gateway / Postman)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8003"]