# ------------------ base PHP image ------------------
    FROM php:8.2-cli              
    # full image, not -slim

    # ------------------ project files -------------------
    WORKDIR /var/www/html
    COPY . .
    
    # ------------------ expose HTTP port ----------------
    EXPOSE 10000
    
    # ------------------ start built‑in web server -------
    CMD ["php", "-S", "0.0.0.0:10000", "-t", "."]
    


    