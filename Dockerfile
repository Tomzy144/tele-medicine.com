# Use PHP 8.2 CLI as base image
FROM php:8.2-cli

# Install Node.js + Supervisor
RUN apt-get update && apt-get install -y curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs supervisor

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Create Supervisor directory
RUN mkdir -p /etc/supervisor/conf.d

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose your port (Render expects this)
EXPOSE 10000

# Start both PHP and Node using Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
