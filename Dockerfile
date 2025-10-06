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
    lsb-release \
    --no-install-recommends

# === INSTALL MSSQL DRIVERS ===
# Download the Microsoft GPG key, convert it, and save it to the trusted keyring directory
RUN curl -fsSL https://packages.microsoft.com/keys/microsoft.asc | gpg --dearmor -o /usr/share/keyrings/microsoft-prod.gpg

# Add the Microsoft repository, signed by the key we just added
RUN echo "deb [arch=amd64,arm64,armhf signed-by=/usr/share/keyrings/microsoft-prod.gpg] https://packages.microsoft.com/debian/12/prod bookworm main" > /etc/apt/sources.list.d/mssql-release.list

# Install Azure CLI for Key Vault access
RUN curl -sL https://aka.ms/InstallAzureCLIDeb | bash

# Update package lists and install the drivers
RUN apt-get update && ACCEPT_EULA=Y apt-get install -y msodbcsql18 mssql-tools18

# Install the PHP extensions via PECL
RUN pecl install sqlsrv pdo_sqlsrv

# Enable the PHP extensions
RUN docker-php-ext-enable sqlsrv pdo_sqlsrv

# Install common PHP extensions for Laravel
RUN docker-php-ext-install bcmath sockets pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create directory for certificates
RUN mkdir -p /usr/local/share/certificates

# Create an entrypoint script to handle certificate import and PHP-FPM startup
RUN echo '#!/bin/bash \n\
# Import certificate if password is provided \n\
if [ -n "$CERT_PASSWORD" ] && [ -f "/usr/local/share/certificates/PHPcert.pfx" ]; then \n\
    echo "Importing certificate for local store..." \n\
    # Extract the certificate (public key) \n\
    openssl pkcs12 -in /usr/local/share/certificates/PHPcert.pfx -clcerts -nokeys -out /usr/local/share/ca-certificates/cert.crt -password pass:$CERT_PASSWORD \n\
    # Extract the private key \n\
    openssl pkcs12 -in /usr/local/share/certificates/PHPcert.pfx -nocerts -nodes -out /usr/local/share/certificates/private.key -password pass:$CERT_PASSWORD \n\
    # Update the CA certificates store \n\
    update-ca-certificates \n\
    echo "Certificate imported successfully" \n\
else \n\
    echo "Local certificate not imported. Either CERT_PASSWORD not set or certificate file not found." \n\
fi \n\
\n\
# Login to Azure if credentials are provided \n\
if [ -n "$AZURE_CLIENT_ID" ] && [ -n "$AZURE_CLIENT_SECRET" ] && [ -n "$AZURE_TENANT_ID" ]; then \n\
    echo "Logging in to Azure..." \n\
    az login --service-principal -u $AZURE_CLIENT_ID -p $AZURE_CLIENT_SECRET --tenant $AZURE_TENANT_ID \n\
    echo "Azure login successful" \n\
else \n\
    echo "Azure login skipped. Azure credentials not provided." \n\
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

# Set permissions for storage and bootstrap cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port 9000
EXPOSE 9000

# Clean up
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Use the entrypoint script as the container's entrypoint
CMD ["/usr/local/bin/docker-entrypoint.sh"]
