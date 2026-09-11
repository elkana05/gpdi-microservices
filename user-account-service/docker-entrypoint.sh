#!/bin/bash
set -e

# Menunggu database siap (opsional, tapi Laravel migrate akan mencoba sampai berhasil atau timeout)
echo "Checking database connection..."

# Menjalankan migrasi database secara otomatis
# --force diperlukan agar bisa jalan di environment production tanpa konfirmasi
php artisan migrate --force

# Menjalankan perintah utama (dari CMD Dockerfile atau command docker-compose)
exec "$@"
