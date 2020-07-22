FROM node:12-alpine as web

WORKDIR /cw-forwarder

ARG APP_URL

ENV APP_URL=${APP_URL}

COPY package.json package-lock.json ./

RUN npm ci

COPY . .

RUN npm run production
