FROM node:20-alpine as builder

WORKDIR /app

COPY pwa/package*.json ./
RUN npm install

COPY pwa . ./

ARG VITE_API_BASE=https://api.asy-syifaa.com
ARG VITE_ERP_BASE=https://erp.asy-syifaa.com
ARG VITE_DEMO_MODE=false

ENV VITE_API_BASE=$VITE_API_BASE
ENV VITE_ERP_BASE=$VITE_ERP_BASE
ENV VITE_DEMO_MODE=$VITE_DEMO_MODE

RUN npm run build

FROM nginx:alpine

COPY --from=builder /app/dist /usr/share/nginx/html

COPY <<EOF /etc/nginx/conf.d/default.conf
server {
    listen 80;
    server_name _;

    root /usr/share/nginx/html;
    index index.html;

    location / {
        try_files \$uri \$uri/ /index.html;
    }

    location ~* \\.(?:js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    client_max_body_size 50M;
}
EOF

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
