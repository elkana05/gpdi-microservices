#!/bin/bash
# =============================================================================
# GPDI - Setup SSL (HTTPS) dengan Let's Encrypt Certbot
# Jalankan SETELAH DNS sudah propagasi dan setup-vps.sh selesai
# =============================================================================
set -e

DOMAIN="gpdisibulele.com"
EMAIL="admin@gpdisibulele.com"   # Ganti dengan email Anda!

echo "============================================="
echo " Setup SSL untuk $DOMAIN"
echo "============================================="

# Install Certbot
apt-get install -y certbot python3-certbot-nginx

# Dapatkan sertifikat SSL
certbot --nginx -d $DOMAIN -d www.$DOMAIN \
    --non-interactive \
    --agree-tos \
    --email $EMAIL \
    --redirect

# Perbarui Nginx config untuk HTTPS + redirect HTTP->HTTPS
cat > /etc/nginx/sites-available/gpdisibulele.com << 'NGINXCONF'
# Redirect HTTP ke HTTPS
server {
    listen 80;
    server_name gpdisibulele.com www.gpdisibulele.com;
    return 301 https://gpdisibulele.com$request_uri;
}

# Redirect www ke non-www (HTTPS)
server {
    listen 443 ssl;
    server_name www.gpdisibulele.com;
    ssl_certificate /etc/letsencrypt/live/gpdisibulele.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/gpdisibulele.com/privkey.pem;
    return 301 https://gpdisibulele.com$request_uri;
}

# Main server block (HTTPS)
server {
    listen 443 ssl;
    server_name gpdisibulele.com;

    ssl_certificate /etc/letsencrypt/live/gpdisibulele.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/gpdisibulele.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;

    client_max_body_size 50M;

    location / {
        proxy_pass http://127.0.0.1:8000;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto https;
        proxy_read_timeout 60s;
        proxy_connect_timeout 5s;
    }
}
NGINXCONF

nginx -t && systemctl reload nginx

echo ""
echo "✅ SSL berhasil dipasang!"
echo "🌐 Website sekarang bisa diakses di: https://$DOMAIN"
echo ""
echo "Auto-renewal SSL sudah aktif otomatis via certbot timer."
