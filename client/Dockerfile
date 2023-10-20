FROM node:18-alpine3.16

ENV ENV_ENV=development

WORKDIR /var/www/html/client

COPY ["package.json", "package-lock.json", "./"]

RUN npm install --force

COPY . .

RUN npm run build

EXPOSE 3000

CMD [ "npm", "start" ]