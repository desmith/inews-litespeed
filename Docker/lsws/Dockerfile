##FROM quay.io/centos/centos:stream9
FROM ghcr.io/ndigitals/openlitespeed:1.7.18-lsphp82

#RUN dnf update -y
##RUN dnf config-manager --set-enabled crb
#RUN dnf install -y epel-release
#RUN dnf --enablerepo=crb install -y libmemcached-awesome
#RUN dnf --enablerepo=crb install -y libmemcached-awesome-tools

#RUN dnf install -y \
#    bc \
#    certbot \
#    fish \
#    git \
#    htop \
#    httpd-tools \
#    neovim \
#    nfs-utils \
#    mlocate \
#    python3 \
#    python3-pip \
#    stress \
#    wget \
#    zip

#RUN updatedb

#RUN wget -O - https://repo.litespeed.sh | bash
#RUN dnf install -y openlitespeed

#RUN dnf install -y lsphp82 lsphp82-common \
#    lsphp82-bcmath \
#    lsphp82-gd \
#    lsphp82-intl \
#    lsphp82-process \
#    lsphp82-mbstring \
#    lsphp82-pecl-memcached \
#    lsphp82-mysqlnd \
#    lsphp82-opcache \
#    lsphp82-pdo \
#    lsphp82-imap \
#    lsphp82-sodium \
#    lsphp82-soap \
#    lsphp82-xml \
#    lsphp82-zip

##RUN mv /usr/local/lsws/lsphp74/bin/lsphp /usr/local/lsws/lsphp74/bin/lsphp74
##RUN mv /usr/local/lsws/lsphp82/bin/lsphp /usr/local/lsws/lsphp74/bin/lsphp82

EXPOSE 80
EXPOSE 443
EXPOSE 7080

#CMD ["-D", "FOREGROUND"]
#CMD ["/usr/local/lsws/bin/openlitespeed", "-D", "FOREGROUND"]
#CMD ["/bin/bash"]
#ENTRYPOINT ["/usr/local/lsws/bin/openlitespeed"]
