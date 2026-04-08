FROM wordpress:6.9-php8.1-apache

# Instalar extensión PHP Redis para WordPress Object Cache
RUN pecl install redis \
    && docker-php-ext-enable redis

# PHP upload/memory limits
RUN echo "upload_max_filesize = 10M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/uploads.ini

# PHP security hardening
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/security.ini \
    && echo "disable_functions = exec,passthru,shell_exec,system,proc_open,popen,parse_ini_file,show_source" >> /usr/local/etc/php/conf.d/security.ini \
    && echo "allow_url_fopen = Off" >> /usr/local/etc/php/conf.d/security.ini \
    && echo "allow_url_include = Off" >> /usr/local/etc/php/conf.d/security.ini \
    && echo "session.cookie_httponly = 1" >> /usr/local/etc/php/conf.d/security.ini \
    && echo "session.cookie_secure = 1" >> /usr/local/etc/php/conf.d/security.ini \
    && echo "session.use_strict_mode = 1" >> /usr/local/etc/php/conf.d/security.ini
