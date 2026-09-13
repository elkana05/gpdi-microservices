#!/bin/bash
# =============================================================================
# GPDI Microservices - VPS Setup Script
# Domain : gpdisibulele.com
# IP VPS : 147.93.81.208
# OS     : Ubuntu 22.04 LTS
# =============================================================================
set -e

DOMAIN="gpdisibulele.com"
REPO_URL="https://github.com/elkana05/gpdi-microservices.git"
BRANCH="docker-uji-coba"
APP_DIR="/var/www/gpdi-microservices"

echo "============================================="
echo " GPDI VPS Setup - $DOMAIN"
echo "============================================="

# -----------------------------------------------------------------------------
# STEP 1: Update system
# -----------------------------------------------------------------------------
echo ""
echo "[1/7] Update system packages..."
apt-get update -y && apt-get upgrade -y
apt-get install -y curl git ufw

# -----------------------------------------------------------------------------
# STEP 2: Install Docker
# -----------------------------------------------------------------------------
echo ""
echo "[2/7] Install Docker..."
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
systemctl enable docker
systemctl start docker

# Install Docker Compose plugin
apt-get install -y docker-compose-plugin
docker compose version

echo "✅ Docker installed: $(docker --version)"

# -----------------------------------------------------------------------------
# STEP 3: Setup Firewall
# -----------------------------------------------------------------------------
echo ""
echo "[3/7] Setup firewall (UFW)..."
ufw allow OpenSSH
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable
echo "✅ Firewall: SSH, HTTP, HTTPS dibuka"

# -----------------------------------------------------------------------------
# STEP 4: Clone Repository
# -----------------------------------------------------------------------------
echo ""
echo "[4/7] Clone repository..."
mkdir -p /var/www
cd /var/www

if [ -d "$APP_DIR" ]; then
    echo "Directory sudah ada, pull latest..."
    cd "$APP_DIR"
    git pull origin $BRANCH
else
    git clone -b $BRANCH $REPO_URL $APP_DIR
    cd "$APP_DIR"
fi

echo "✅ Repository cloned ke $APP_DIR"

# -----------------------------------------------------------------------------
# STEP 5: Setup .env files (copy dari .env.production)
# -----------------------------------------------------------------------------
echo ""
echo "[5/7] Setup .env files..."
SERVICES=("api-gateway" "user-account-service" "content-publication-service" "event-rayon-service" "administration-utility-service")

for svc in "${SERVICES[@]}"; do
    if [ ! -f "$APP_DIR/$svc/.env" ]; then
        if [ -f "$APP_DIR/$svc/.env.production" ]; then
            cp "$APP_DIR/$svc/.env.production" "$APP_DIR/$svc/.env"
            echo "✅ $svc/.env dibuat dari .env.production"
        else
            echo "⚠️  $svc/.env.production tidak ditemukan! Buat manual."
        fi
    else
        echo "⚠️  $svc/.env sudah ada, skip (tidak ditimpa)"
    fi
done

# -----------------------------------------------------------------------------
# STEP 6: Install Nginx
# -----------------------------------------------------------------------------
echo ""
echo "[6/7] Install Nginx..."
apt-get install -y nginx

# Buat konfigurasi Nginx untuk GPDI
cat > /etc/nginx/sites-available/gpdisibulele.com << 'NGINXCONF'
server {
    listen 80;
    server_name gpdisibulele.com www.gpdisibulele.com;

    # Redirect www ke non-www
    if ($host = www.gpdisibulele.com) {
        return 301 https://gpdisibulele.com$request_uri;
    }

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_read_timeout 60s;
        proxy_connect_timeout 5s;
    }
}
NGINXCONF

ln -sf /etc/nginx/sites-available/gpdisibulele.com /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx
echo "✅ Nginx configured"

# -----------------------------------------------------------------------------
# STEP 7: Build & Run Docker Compose
# -----------------------------------------------------------------------------
echo ""
echo "[7/7] Build dan jalankan Docker containers..."
cd "$APP_DIR"
docker compose up -d --build

echo ""
echo "⏳ Menunggu database siap (30 detik)..."
sleep 30

# Jalankan migrasi
echo ""
echo "📦 Menjalankan migrasi database..."
docker compose exec -T api-gateway php artisan migrate --force
docker compose exec -T user-account-service php artisan migrate --force
docker compose exec -T content-publication-service php artisan migrate --force
docker compose exec -T event-rayon-service php artisan migrate --force
docker compose exec -T administration-utility-service php artisan migrate --force

# Storage link untuk service yang butuh
docker compose exec -T content-publication-service php artisan storage:link 2>/dev/null || true
docker compose exec -T event-rayon-service php artisan storage:link 2>/dev/null || true

echo ""
echo "============================================="
echo " ✅ SETUP SELESAI!"
echo "============================================="
echo " URL API  : http://$DOMAIN/api"
echo " IP VPS   : 147.93.81.208"
echo ""
echo " ⚠️  TODO WAJIB setelah ini:"
echo " 1. Generate APP_KEY tiap service:"
echo "    docker compose exec api-gateway php artisan key:generate"
echo "    docker compose exec user-account-service php artisan key:generate"
echo "    docker compose exec content-publication-service php artisan key:generate"
echo "    docker compose exec event-rayon-service php artisan key:generate"
echo "    docker compose exec administration-utility-service php artisan key:generate"
echo ""
echo " 2. Generate JWT_SECRET baru:"
echo "    docker compose exec api-gateway php artisan jwt:secret"
echo "    (Salin hasilnya ke JWT_SECRET di .env semua service, lalu restart)"
echo ""
echo " 3. Setup SSL (HTTPS) - jalankan:"
echo "    bash /var/www/gpdi-microservices/setup-ssl.sh"
echo "============================================="
