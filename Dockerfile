FROM php:8.3-apache
RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev
RUN docker-php-ext-install pdo_pgsql
RUN a2enmod headers rewrite
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
COPY docker-entrypoint.sh /usr/local/bin/nexus-entrypoint
RUN chmod +x /usr/local/bin/nexus-entrypoint
EXPOSE 10000
CMD ["nexus-entrypoint"]
