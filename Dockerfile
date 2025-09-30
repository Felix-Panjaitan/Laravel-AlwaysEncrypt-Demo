FROM php:8.3-fpm-bookworm

# Set working directory
WORKDIR /var/www/html

# Install system dependencies required by Laravel and the MSSQL driver
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zip \
    curl \
    gnupg \
    apt-transport-https \
    unixodbc-dev \
    openssl \
    ca-certificates \
    --no-install-recommends && \
    rm -rf /var/lib/apt/lists/*

# === INSTALL MSSQL DRIVERS ===
# Download the Microsoft GPG key, convert it, and save it to the trusted keyring directory
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg

# Add the Microsoft repository, signed by the key we just added
RUN echo "deb [arch=amd64,arm64,armhf signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list

# Update package lists and install the drivers
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18

# Install the PHP extensions via PECL
RUN pecl install sqlsrv pdo_sqlsrv

# Enable the PHP extensions
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

# Install common PHP extensions for Laravel
# RUN docker-php-ext-install pdo_mysql bcmath sockets pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create directory for certificates
RUN mkdir -p /usr/local/share/certificates

# Create an entrypoint script to handle certificate import and PHP-FPM startup
RUN echo '#!/bin/bash \n\
# Import certificate if password is provided \n\
if [ -n "$CERT_PASSWORD" ] && [ -f "/usr/local/share/certificates/{{certificate_name}}" ]; then \n\
    echo "Importing certificate..." \n\
    # Extract the certificate (public key) \n\
    openssl pkcs12 -in /usr/local/share/certificates/{{certificate_name}} -clcerts -nokeys -out /usr/local/share/ca-certificates/cert.crt -password pass:$CERT_PASSWORD \n\
    # Extract the private key \n\
    openssl pkcs12 -in /usr/local/share/certificates/{{certificate_name}} -nocerts -nodes -out /usr/local/share/certificates/private.key -password pass:$CERT_PASSWORD \n\
    # Update the CA certificates store \n\
    update-ca-certificates \n\
    echo "Certificate imported successfully" \n\
else \n\
    echo "Certificate not imported. Either CERT_PASSWORD not set or certificate file not found." \n\
fi \n\
\n\
# Start PHP-FPM \n\
exec php-fpm' > /usr/local/bin/docker-entrypoint.sh

# Make the script executable
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy existing application directory contents
# Instead of COPY . .
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install --no-scripts --no-autoloader

# Then copy the rest of your application
COPY . .

# Run any post-install scripts and generate autoloader
RUN composer dump-autoload --optimize

# Copy application vendor dependencies from a previous build
# This is a build optimization. It will be created on the first build.
# COPY --chown=www-data:www-data vendor /var/www/html/vendor

# Set permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Use the entrypoint script as the container's entrypoint
CMD ["/usr/local/bin/docker-entrypoint.sh"]
