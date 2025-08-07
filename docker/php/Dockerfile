FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    net-tools \
    iputils-ping \
    procps \
    dnsutils \
    netcat-traditional

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


# Install Node.js and npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

# Update npm to latest version
RUN npm install -g npm@latest

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create system user to run Composer and Artisan Commands
RUN useradd -G www-data,root -u 1000 -d /home/dev dev
RUN mkdir -p /home/dev/.composer && \
    chown -R dev:dev /home/dev

# Set working directory
WORKDIR /var/www

USER dev
