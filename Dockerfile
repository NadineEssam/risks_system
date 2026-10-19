FROM php:8.2-fpm

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    wget \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libonig-dev \
    libxml2-dev \
    libldap2-dev \
    libaio1t64 \
    && ln -s /usr/lib/x86_64-linux-gnu/libaio.so.1t64 /usr/lib/x86_64-linux-gnu/libaio.so.1 \
    && rm -rf /var/lib/apt/lists/*

# Install Oracle Instant Client
RUN mkdir -p /opt/oracle \
    && wget -O /tmp/instantclient-basic.zip \
       https://download.oracle.com/otn_software/linux/instantclient/2326000/instantclient-basic-linux.x64-23.26.0.0.0.zip \
    && wget -O /tmp/instantclient-sdk.zip \
       https://download.oracle.com/otn_software/linux/instantclient/2326000/instantclient-sdk-linux.x64-23.26.0.0.0.zip \
    && unzip -q -o /tmp/instantclient-basic.zip -d /opt/oracle \
    && unzip -q -o /tmp/instantclient-sdk.zip -d /opt/oracle \
    && ln -s /opt/oracle/instantclient_23_26 /opt/oracle/instantclient \
    && echo /opt/oracle/instantclient > /etc/ld.so.conf.d/oracle-instantclient.conf \
    && ldconfig \
    && rm -f /tmp/instantclient-basic.zip /tmp/instantclient-sdk.zip

# Install OCI8
RUN echo "instantclient,/opt/oracle/instantclient" | pecl install oci8 \
    && docker-php-ext-enable oci8

# Install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        gd \
        ldap

WORKDIR /var/www