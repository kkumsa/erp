# Laravel PHP-FPM Dockerfile
FROM php:8.3-fpm-alpine

# 작업 디렉토리 설정
WORKDIR /var/www/html

# 시스템 패키지 설치
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    linux-headers \
    $PHPIZE_DEPS

# PHP 확장 설치
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        xml

# Redis 확장 설치
RUN pecl install redis && docker-php-ext-enable redis

# Composer 설치
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP 설정
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# PHP 커스텀 설정
RUN echo "upload_max_filesize = 100M" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "post_max_size = 100M" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "memory_limit = 512M" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "max_execution_time = 600" >> "$PHP_INI_DIR/conf.d/custom.ini" \
    && echo "max_input_time = 600" >> "$PHP_INI_DIR/conf.d/custom.ini"

# OPcache 설정
RUN echo "opcache.enable=1" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.memory_consumption=256" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.max_accelerated_files=20000" >> "$PHP_INI_DIR/conf.d/opcache.ini" \
    && echo "opcache.validate_timestamps=0" >> "$PHP_INI_DIR/conf.d/opcache.ini"

# 사용자 생성
RUN addgroup -g 1000 -S www && adduser -u 1000 -S www -G www

# 소스 복사
COPY --chown=www:www . .

# Composer 의존성 설치
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 권한 설정
RUN chown -R www:www /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# 사용자 변경
USER www

# 포트 노출
EXPOSE 9000

CMD ["php-fpm"]
