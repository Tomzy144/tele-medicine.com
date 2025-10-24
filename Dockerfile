# Use PHP 8.2 CLI as base image
FROM php:8.2-cli

# Install Node.js + Supervisor
RUN apt-get update && apt-get install -y curl \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Create Supervisor directory
RUN mkdir -p /etc/supervisor/conf.d

# Copy Supervisor config
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose Render port (Render assigns $PORT automatically)
EXPOSE 10000

# Ensure Supervisor runs and keeps both PHP + Node alive
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
