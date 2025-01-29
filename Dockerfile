FROM node:jod AS build-stage
WORKDIR /app
COPY vue/package*.json ./
RUN npm install
COPY ./vue .
RUN npm run build-only

FROM php:8.4-fpm-alpine
WORKDIR /app
COPY . ./

RUN rm -rf vue
COPY --from=build-stage /app/dist ./vue

RUN apk add --no-cache --update git unzip nginx \
    && docker-php-ext-install pdo pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && cp .env.example .env \
    && composer install --optimize-autoloader --no-dev

COPY .github/docker/nginx.conf /etc/nginx/nginx.conf

ENTRYPOINT [ ".github/docker/entrypoint.sh" ]
EXPOSE 80
CMD ["php-fpm"]