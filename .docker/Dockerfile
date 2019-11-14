# build image
FROM facchin/devbase-base:ubuntu-16.04

# bootstrap
RUN apt-get clean -y \
    && apt-get update -y 

# utils
RUN apt-get install --no-install-recommends -yq \
    net-tools \
    mysql-client \
    curl \
    iputils-ping \
    htop \
    git \
    g++ \
    make

# swoole
# RUN pecl install swoole
RUN git clone https://github.com/swoole/swoole-src.git \
    && cd swoole-src \
    && git checkout v4.4.12 \
    && phpize \
    && ./configure --enable-openssl --enable-sockets --enable-mysqlnd \
    && make && make install \
    && echo "extension=swoole" > /etc/php/7.2/mods-available/swoole.ini \
    && phpenmod swoole

# phalconphp
RUN apt-get install --no-install-recommends -yq \
    php7.2-phalcon \
    && php -r 'echo "Phalcon " . \Phalcon\Version::get() . "\n";'

# RUN true

COPY ./configs/ /

# ARG XDEBUG_REMOTE_ENABLE
# ARG XDEBUG_REMOTE_AUTO_START
# ARG XDEBUG_REMOTE_HOST

# ENV XDEBUG_REMOTE_ENABLE $XDEBUG_REMOTE_ENABLE \
#     XDEBUG_REMOTE_AUTO_START $XDEBUG_REMOTE_AUTO_START \
#     XDEBUG_REMOTE_HOST $XDEBUG_REMOTE_HOST

# after-build
RUN bash /entrypoint.sh

# cleanup
RUN apt-get autoremove -y \
    && apt-get clean -y    
