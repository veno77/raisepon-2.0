FROM php:8.2-apache

# Install required packages and PHP extensions
RUN apt-get update && \
    apt-get -y upgrade && \
    apt-get install -y git snmp libsnmp-dev rrdtool cron librrd-dev && \
    docker-php-ext-install mysqli pdo pdo_mysql snmp && \
    pecl install rrd && \
    docker-php-ext-enable rrd

# Clone the Raisepon project
RUN git clone -b docker_build --single-branch https://github.com/veno77/raisepon-2.0.git /tmp/raisepon

# Copy files and set permissions
RUN mkdir -p /tmp/raisepon/rrd && \
    cp -rf /tmp/raisepon/* /var/www/html/ && \
    mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    chown -R www-data:www-data /var/www/html/rrd 

# Add cron jobs (Update paths to real PHP script locations inside the container)
RUN echo "*/15 * * * * www-data /usr/local/bin/php /var/www/html/update_rrd.php > /dev/null 2>&1" >> /etc/crontab && \
    echo "* */1 * * * www-data /usr/local/bin/php /var/www/html/update_data.php > /dev/null 2>&1" >> /etc/crontab && \
    echo "* * * * * www-data /usr/local/bin/php /var/www/html/update_auto.php > /dev/null 2>&1" >> /etc/crontab && \
    echo "0 2 * * * www-data /usr/local/bin/php /var/www/html/update_backup.php > /dev/null 2>&1" >> /etc/crontab

# Start Apache and cron in foreground
CMD service cron start && apache2-foreground

