# ==========================================
# Stage 1: PHP/Composer Build dependencies
# ==========================================
FROM composer:2 AS php-builder
WORKDIR /app
COPY composer.json composer.lock ./
# تثبيت الحزم بدون إضافات التطوير
RUN composer install --ignore-platform-reqs --no-interaction --no-plugins --no-scripts --prefer-dist --no-dev
COPY . .
RUN composer dump-autoload --optimize --no-dev

# ==========================================
# Stage 2: Node.js/Frontend Build
# ==========================================
FROM node:20-alpine AS node-builder
WORKDIR /app
COPY package.json package-lock.json vite.config.js postcss.config.js tailwind.config.js ./
RUN npm ci
COPY . .
# بناء ملفات الواجهة الأمامية
RUN npm run build

# ==========================================
# Stage 3: Final Production Image (Alpine)
# ==========================================
# استخدام نسخة Alpine الخفيفة جداً بدلاً من الأساسية
FROM dunglas/frankenphp:php8.4-alpine

# تثبيت الحزم الأساسية الخفيفة فقط
RUN apk add --no-cache \
    poppler-utils \
    mariadb-client

# تثبيت إضافات PHP
RUN install-php-extensions pdo_mysql mysqli

WORKDIR /app

# نسخ كود المشروع النظيف
COPY . .

# نسخ الحزم الجاهزة من المراحل السابقة (دون تحميل Node أو Composer هنا!)
COPY --from=php-builder /app/vendor/ ./vendor/
COPY --from=node-builder /app/public/build/ ./public/build/

# نسخ إعدادات PHP و Caddy
COPY php.ini /usr/local/etc/php/conf.d/custom.ini
COPY Caddyfile /etc/caddy/Caddyfile

# إنشاء رابط التخزين وضبط الصلاحيات
RUN php artisan storage:link && \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache /app/public

EXPOSE 80 443

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]