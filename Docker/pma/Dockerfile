# phpMyAdmin Dockerfile: Create container from official phpmyadmin/phpmyadmin:latest image, update, and replace php.ini file.
# docker build -t phpmyadmin_local:phpmyadmin_custom_1.0 .

FROM phpmyadmin/phpmyadmin:latest

# APT Update/Upgrade, then install packages we need
#RUN apt update && apt upgrade -y && apt autoremove
#RUN apt install -y \
#   vim
#RUN apt clean

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Replace php.ini
COPY php.ini /usr/local/etc/php

# Remove old php.ini files
RUN rm /usr/local/etc/php/php.ini-development && \
    rm /usr/local/etc/php/php.ini-production
