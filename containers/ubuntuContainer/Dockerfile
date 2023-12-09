# Use the Ubuntu 20.04 image as the base image
FROM ubuntu:20.04

# Disable popups asking for geography area
ARG DEBIAN_FRONTEND=noninteractive

# Set working directory
WORKDIR /home

# Install necessary packages
RUN apt-get update && apt-get install -y \
    bash \
    apache2 \
    php \
    libapache2-mod-php \
    ufw \
    git \
    openssl \
    vim

# Enable required Apache modules
RUN a2enmod php7.4

# Allow Apache through the firewall
RUN ufw allow 'Apache'

# Install PHP extensions
RUN apt-get install -y \
    php-mysqli \
    php-gd \
    php-mbstring \
    php-zip \
    php-curl \
    php-exif \
    php-dom

# Clone the GitHub repository
RUN cd /var/www/html && git clone -b summer-2023 --single-branch https://github.com/BU-Spark/se-Symbiota-portal.git

# Copy symbiota config files to the apache2 directory
COPY 002-symbiota.conf /etc/apache2/sites-available

# Enable symbiota site
RUN a2dissite 000-default.conf
RUN a2ensite 002-symbiota.conf

# Expose port 80 for web traffic
EXPOSE 80

# Set environment variables
ENV APACHE_RUN_DIR /var/run/apache2
ENV APACHE_LOG_DIR /var/log/apache2
ENV APACHE_LOCK_DIR /var/lock/apache2
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_PID_FILE /var/run/apache2/apache2.pid

# Make sure the required directories exist, if they didn't already exist
RUN mkdir -p $APACHE_RUN_DIR $APACHE_LOCK_DIR $APACHE_LOG_DIR

# Start bash
# CMD ["/bin/bash"]
# Start apache
ENTRYPOINT ["/usr/sbin/apache2", "-DFOREGROUND"]