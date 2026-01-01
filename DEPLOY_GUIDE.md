# Panduan Deployment Kios Afgan POS System

Dokumen ini berisi panduan langkah demi langkah untuk melakukan deployment aplikasi Kios Afgan ke server production (VPS) dengan sistem operasi Ubuntu 22.04 atau 24.04 LTS.

## Daftar Isi

-   [Pendahuluan](#pendahuluan)
-   [Persiapan Server](#persiapan-server)
-   [Setup Environment](#setup-environment)
-   [Langkah Deployment](#langkah-deployment)
-   [Konfigurasi Database](#konfigurasi-database)
-   [Konfigurasi Web Server (Nginx)](#konfigurasi-web-server-nginx)
-   [Konfigurasi SSL (HTTPS)](#konfigurasi-ssl-https)
-   [Process Management & Permissions](#process-management--permissions)

---

## Pendahuluan

Aplikasi **Kios Afgan** dibangun menggunakan **Laravel** (PHP) dan **Vite** (Frontend Build Tool). Deployment dilakukan menggunakan Nginx sebagai web server dan PHP-FPM untuk memproses script PHP.

**Prasyarat:**

-   VPS dengan Ubuntu 22.04/24.04.
-   Akses root atau user dengan hak `sudo`.
-   Domain yang sudah diarahkan ke IP server (untuk SSL).

---

## Persiapan Server

Update paket sistem dan konfigurasi firewall dasar.

```bash
# Update repository dan paket sistem
sudo apt update && sudo apt upgrade -y

# Install utilitas dasar (opsional tapi disarankan)
sudo apt install git curl unzip zip -y
```

### Konfigurasi Firewall (UFW)

Pastikan port SSH (22), HTTP (80), dan HTTPS (443) terbuka.

```bash
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

> [!WARNING]
> Pastikan Anda telah mengizinkan OpenSSH sebelum mengaktifkan UFW, atau Anda akan kehilangan akses ke server.

---

## Setup Environment

Kita akan menginstal Nginx, PHP 8.2 (atau sesuaikan dengan kebutuhan Laravel), Composer, dan Node.js.

### 1. Install Nginx

```bash
sudo apt install nginx -y
```

### 2. Install PHP & Extension

Tambahkan repositori PPA untuk mendapatkan versi PHP terbaru.

```bash
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 dan ekstensi yang diperlukan Laravel
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-bcmath php8.2-curl php8.2-zip php8.2-intl php8.2-gd -y
```

### 3. Install Composer

```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 4. Install Node.js (untuk Vite build)

```bash
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs
```

### 5. Install MySQL / MariaDB

```bash
sudo apt install mysql-server -y
```

---

## Langkah Deployment

Kita akan menyalin kode aplikasi ke direktori `/var/www`.

### 1. Clone Repository

Ganti URL repo dengan URL repository Git Anda yang sebenarnya.

```bash
cd /var/www
sudo git clone https://github.com/username/kios-afgan.git kios-afgan
cd kios-afgan
```

### 2. Install Dependencies

```bash
# PHP Dependencies
composer install --optimize-autoloader --no-dev

# Node Dependencies
npm install
npm run build
```

### 3. Konfigurasi Environment (.env)

```bash
cp .env.example .env
nano .env
```

Sesuaikan konfigurasi berikut di dalam file `.env`:

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kios_afgan
DB_USERNAME=kios_user
DB_PASSWORD=password_aman_anda
```

Generate application key:

```bash
php artisan key:generate
```

---

## Konfigurasi Database

Setup user dan database MySQL.

```bash
sudo mysql
```

Jalankan query SQL berikut (ganti password dengan yang kuat):

```sql
CREATE DATABASE kios_afgan;
CREATE USER 'kios_user'@'localhost' IDENTIFIED BY 'password_aman_anda';
GRANT ALL PRIVILEGES ON kios_afgan.* TO 'kios_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Migrasi Database

Jalankan migrasi untuk membuat tabel dan seeder (opsional).

```bash
php artisan migrate --force
# Jika ingin mengisi data awal:
# php artisan db:seed --force
```

---

## Konfigurasi Web Server (Nginx)

Buat konfigurasi server block baru.

```bash
sudo nano /etc/nginx/sites-available/kios-afgan
```

Isi dengan konfigurasi berikut:

```nginx
server {
    listen 80;
    server_name domain-anda.com www.domain-anda.com;
    root /var/www/kios-afgan/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Aktifkan konfigurasi dan restart Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/kios-afgan /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

## Process Management & Permissions

### Permissions

Pastikan Nginx (user `www-data`) memiliki akses ke folder storage.

```bash
sudo chown -R www-data:www-data /var/www/kios-afgan
sudo chmod -R 775 /var/www/kios-afgan/storage
sudo chmod -R 775 /var/www/kios-afgan/bootstrap/cache
```

### Optimization

Cache konfigurasi dan route untuk performa lebih baik.

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Konfigurasi SSL (HTTPS)

Gunakan Certbot untuk mengamankan koneksi dengan SSL Let's Encrypt gratis.

```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx -y

# Request sertifikat
sudo certbot --nginx -d domain-anda.com -d www.domain-anda.com
```

Certbot akan otomatis memperbarui konfigurasi Nginx Anda untuk me-redirect HTTP ke HTTPS.

> [!TIP]
> Certbot sudah menyiapkan cron job untuk auto-renewal. Anda bisa test dengan `sudo certbot renew --dry-run`.

---

**Deployment Selesai!**
Aplikasi Kios Afgan sekarang seharusnya sudah dapat diakses melalui `https://domain-anda.com`.
