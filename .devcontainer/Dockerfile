FROM mcr.microsoft.com/devcontainers/php:1-8.3-bookworm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    libpq-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    unzip \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    intl \
    zip \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    opcache \
    gd

# Install Symfony CLI
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Install Composer (already included in base image, but ensure latest)
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Configure PHP for development
RUN echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/docker-php-memlimit.ini \
    && echo "upload_max_filesize = 100M" >> /usr/local/etc/php/conf.d/docker-php-uploads.ini \
    && echo "post_max_size = 100M" >> /usr/local/etc/php/conf.d/docker-php-uploads.ini

# Set working directory
WORKDIR /workspaces/fixture-project-banking

# Default command
CMD ["sleep", "infinity"]
