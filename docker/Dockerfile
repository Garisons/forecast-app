FROM nginx:stable

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get upgrade -y && apt-get install -y wget lsb-release vim unzip && rm -f /etc/localtime && ln -s /usr/share/zoneinfo/Europe/Riga /etc/localtime \
&& wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg \
&& echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/php.list \
&& apt-get update && apt-get install -y \
php-fpm \
php-mbstring \
php-imagick \
php-zip \
php-xml \
php-curl \
php-intl \
php-pgsql \
php-xdebug \
&& usermod -aG www-data nginx \
&& php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && php composer-setup.php &&  php -r "unlink('composer-setup.php');" && mv composer.phar /usr/local/bin/composer \
&& apt-get install -y build-essential \
&& curl -sL https://deb.nodesource.com/setup_lts.x | bash - \
&& apt-get install -y nodejs \
&& apt-get install -y git
COPY /nginx/default.conf /etc/nginx/sites-enabled
RUN service php8.0-fpm start
WORKDIR /var/www/html/
