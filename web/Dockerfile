FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Set environment variables for noninteractive mode and time zone
ENV DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Ho_Chi_Minh

#Install dependencies for ModSecurity
RUN apt-get update && \
    apt-get install -y g++ flex bison curl apache2-dev \
        doxygen libyajl-dev ssdeep liblua5.2-dev \
        libgeoip-dev libtool dh-autoreconf \
        libcurl4-gnutls-dev libxml2 libpcre3-dev \
        libxml2-dev git wget tar \
        nano apt-transport-https gnupg2 \
        autoconf automake pkg-config && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

# Download LibModsecurity 
RUN git clone https://github.com/owasp-modsecurity/ModSecurity.git

# Initialize and update git submodules to get libInjection
RUN cd ModSecurity && \
    git submodule init && \
    git submodule update

    # Compile and Install LibModsecurity
RUN cd ModSecurity && \
./build.sh && ./configure && \
make && make install

# Install ModSecurity-Apache Connector
RUN cd ~ && git clone https://github.com/SpiderLabs/ModSecurity-apache
RUN cd ~/ModSecurity-apache && \
    ./autogen.sh && \
    ./configure --with-libmodsecurity=/usr/local/modsecurity/ && \
    make && \
    make install

# Load the Apache ModSecurity Connector Module
RUN echo "LoadModule security3_module /usr/lib/apache2/modules/mod_security3.so" >> /etc/apache2/apache2.conf

#Configure ModSecurity
RUN mkdir /etc/apache2/modsecurity.d && \
    cp ModSecurity/modsecurity.conf-recommended /etc/apache2/modsecurity.d/modsecurity.conf && \
    cp ModSecurity/unicode.mapping /etc/apache2/modsecurity.d/ && \ 
    sed -i 's/SecRuleEngine DetectionOnly/SecRuleEngine On/' /etc/apache2/modsecurity.d/modsecurity.conf
ADD modsec_rules.conf /etc/apache2/modsecurity.d/

# Install OWASP ModSecurity Core Rule Set (CRS) on Ubuntu
RUN git clone https://github.com/SpiderLabs/owasp-modsecurity-crs.git /etc/apache2/modsecurity.d/owasp-crs && \
    cp /etc/apache2/modsecurity.d/owasp-crs/crs-setup.conf.example /etc/apache2/modsecurity.d/owasp-crs/crs-setup.conf
ADD RuleUserSet.conf /etc/apache2/modsecurity.d/owasp-crs/rules/
RUN touch /etc/apache2/modsecurity.d/owasp-crs/rules/RuleUserSet.conf

# Activate ModSecurity
RUN mv /etc/apache2/sites-available/000-default.conf /etc/apache2/sites-available/000-default.conf.old
ADD 000-default.conf /etc/apache2/sites-available/

# Add source code to Apache2
WORKDIR /var/www/html
COPY ./src/ . 

COPY php.ini /usr/local/etc/php/conf.d/
RUN chown -R www-data:www-data /var/www/html/
RUN chmod -R 777 /var/www/html/uploads/
