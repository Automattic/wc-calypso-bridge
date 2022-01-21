FROM alpine:3.12

# Install deps
RUN apk -q --update --no-cache add tini shadow less git subversion nodejs npm \
	composer ncurses mysql-client mariadb-connector-c curl python3 py3-pip g++ make \
    php7 php7-pecl-apcu php7-bcmath php7-bz2 php7-ctype php7-curl php7-dom \
    php7-ftp php7-gd php7-iconv php7-json php7-mbstring php7-mysqli \
    php7-pecl-oauth php7-opcache php7-openssl php7-pcntl php7-pdo php7-pdo_mysql \
    php7-phar php7-session php7-simplexml php7-tokenizer php7-pecl-xdebug php7-xml \
    php7-xmlwriter php7-zip php7-zlib

# Woo requires pnpm
RUN npm install -g pnpm

# Install wp-cli
RUN wget -q -O /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar && \
    chmod +x /usr/local/bin/wp

# Install WordPress - must use /tmp for test env
run mkdir -p /tmp && \
	cd /tmp && \
	wget -q https://wordpress.org/nightly-builds/wordpress-latest.zip && \
    unzip -q wordpress-latest.zip

# Don't use mysql extension (copied from install-wp-tests.sh)
RUN wget -q -O /tmp/wordpress/wp-content/db.php https://raw.github.com/markoheijnen/wp-mysqli/master/db.php

# Install WP Tests lib and data
RUN mkdir -p /tmp/wordpress-tests-lib
RUN svn export https://develop.svn.wordpress.org/trunk/tests/phpunit/includes/ /tmp/wordpress-tests-lib/includes
RUN svn export https://develop.svn.wordpress.org/trunk/tests/phpunit/data/ /tmp/wordpress-tests-lib/data
RUN svn export https://develop.svn.wordpress.org/trunk/wp-tests-config-sample.php /tmp/wordpress-tests-lib/wp-tests-config.php
RUN sed -i "s:dirname( __FILE__ ) . '/src/':'/tmp/wordpress/':" /tmp/wordpress-tests-lib/wp-tests-config.php
RUN sed -i "s/youremptytestdbnamehere/wordpress_tests/" /tmp/wordpress-tests-lib/wp-tests-config.php
RUN sed -i "s/yourusernamehere/wordpress_tests/" /tmp/wordpress-tests-lib/wp-tests-config.php
RUN sed -i "s/yourpasswordhere/wordpress_tests/" /tmp/wordpress-tests-lib/wp-tests-config.php
RUN sed -i "s|localhost|mysql:3306|" /tmp/wordpress-tests-lib/wp-tests-config.php

# Install WooCommerce
RUN git clone --depth 1 https://github.com/woocommerce/woocommerce.git /tmp/woocommerce
ENV PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true
ENV SKIP_UPDATE_TEXTDOMAINS=true
RUN cd /tmp/woocommerce && pnpm install
RUN cd /tmp/woocommerce/plugins/woocommerce && pnpm install
RUN cd /tmp/woocommerce/plugins/woocommerce && composer install --no-dev
RUN cp -a /tmp/woocommerce/plugins/woocommerce /tmp/wordpress/wp-content/plugins/woocommerce

WORKDIR /tmp/wordpress/wp-content/plugins/wc-calypso-bridge
ENTRYPOINT ["tini", "--"]
CMD [ "sh" ]