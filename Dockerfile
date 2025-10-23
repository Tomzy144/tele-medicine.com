# ------------------ base PHP image ------------------
FROM php:8.2-cli

# ------------------ install dependencies ------------------
RUN apt-get update && apt-get install -y curl gnupg && \
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y nodejs && \
    docker-php-ext-install mysqli pdo pdo_mysql

# ------------------ project files -------------------
WORKDIR /var/www/html
COPY . .

# ------------------ install Node dependencies --------
# If your Node.js app has package.json, install dependencies
RUN if [ -f package.json ]; then npm install; fi

# ------------------ expose ports --------------------
EXPOSE 10000 3000

# ------------------ start both servers --------------
# PHP runs on port 10000, Node.js on port 3000
CMD php -S 0.0.0.0:10000 -t . & node server.js
