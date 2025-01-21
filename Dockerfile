FROM node:jod as build-stage
WORKDIR /app
COPY vue/package*.json ./
RUN npm install
COPY ./vue .
RUN npm run build

FROM nginx as production-stage
RUN mkdir /app
COPY --from=build-stage /app/dist /app
COPY nginx.conf /etc/nginx/nginx.conf