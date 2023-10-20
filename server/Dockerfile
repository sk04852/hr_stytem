FROM php:7.4-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    zip \
    unzip \
    supervisor \
    git \
    curl \
    libmemcached-dev \
    libfontconfig1 \
    libxrender1 \
    libxext6 \
    zlib1g-dev \
    libpng-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libzip-dev \
    libzstd-dev \
    libmagickcore-dev \
    libmagickwand-dev \
    libxslt-dev \
    liblz4-dev

# compile native PHP packages
RUN docker-php-ext-install \
    calendar \
    exif \
    # FFI \
    gettext \
    # gmp \
    intl \
    shmop \
    soap \
    sockets \
    sysvmsg \
    sysvsem \
    sysvshm \
    xmlrpc \
    xsl \
    opcache \
    json \
    gd \
    pcntl \
    bcmath \
    mysqli \
    pdo_mysql

# configure packages
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

# install additional packages from PECL
RUN pecl install msgpack && docker-php-ext-enable msgpack \
    && pecl install zip && docker-php-ext-enable zip \
    && pecl install igbinary && docker-php-ext-enable igbinary \
    && yes | pecl install redis && docker-php-ext-enable redis \
    && pecl install imagick && docker-php-ext-enable imagick \
    && pecl install xdebug-3.1.0 && docker-php-ext-enable xdebug

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --ansi --version --no-interaction

COPY  ["docker-config/php/", "/usr/local/etc/php/conf.d/"]
COPY  ["docker-config/apache/sites-available/", "/etc/apache2/sites-available/"]
COPY  ["docker-config/supervisord/", "/etc/supervisor/conf.d/"]

RUN a2enmod rewrite

WORKDIR /var/www/html/server
COPY ["composer.json", "composer.lock", "./"]
RUN composer install --no-scripts --no-autoloader

# Changing DocumentRoot (or other Apache configuration)
# ENV APACHE_DOCUMENT_ROOT /var/www/html/server/public

# RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
# RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY . .

# RUN chown -R www-data:www-data /var/www/html/server
# RUN find /var/www/html/server -type f -exec chmod 664 {} \;
# RUN find /var/www/html/server -type d -exec chmod 775 {} \;
# RUN chmod -R ug+rwx storage bootstrap/cache
RUN chmod +x /var/www/html/server/run.sh

EXPOSE 80
# CMD ["composer", "dump-autoload"]
# CMD ["/bin/sh", "/var/www/html/server/run.sh"]
# ENTRYPOINT ["chmod", "+x", "/var/www/html/server/run.sh"]