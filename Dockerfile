# Etapa 1: Definir a imagem base do PHP
FROM php:8.2-fpm

# Etapa 2: Instalar dependências do sistema e PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    git \
    unzip \
    nginx \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql

# Etapa 3: Instalar Node.js e npm
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash - && apt-get install -y nodejs

# Etapa 4: Instalar Composer (Gerenciador de Dependências PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Etapa 5: Definir o diretório de trabalho
WORKDIR /var/www

# Etapa 6: Copiar os arquivos do projeto para o container
COPY . .

# Etapa 7: Instalar dependências do Laravel
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache
RUN composer install --no-dev --optimize-autoloader

# Etapa 8: Instalar dependências do Vue.js com npm
RUN npm install

# Etapa 9: Configurar Nginx.
COPY ./nginx/default.conf /etc/nginx/sites-available/default

# Etapa 10: Expor a porta 80 para o Nginx
EXPOSE 80

# Etapa 11: Rodar o Nginx e PHP-FPM
CMD service nginx start && php-fpm


