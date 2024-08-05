FROM php:8.3-fpm

# Установка необходимых расширений PHP
RUN docker-php-ext-install pdo pdo_mysql

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка зависимостей и копирование исходного кода
WORKDIR /var/www/html
COPY . .

RUN composer install

# Настройка прав доступа
RUN chown -R www-data:www-data /var/www/html

# Экспонирование порта
EXPOSE 9000
