FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set environment variables for noninteractive mode and time zone
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Ho_Chi_Minh

RUN apt-get update && apt-get install -y wget nano docker.io
# Add source code to Apache2
WORKDIR /var/www/html
COPY ./src/ . 

COPY php.ini /usr/local/etc/php/conf.d/

RUN chmod -R 777 /var/www/html/uploads/
RUN usermod -aG docker www-data
