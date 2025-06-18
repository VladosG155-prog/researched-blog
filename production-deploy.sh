#!/bin/bash

# Скрипт для развертывания блога researched.xyz в продакшене
# Выполните этот скрипт на продакшн сервере

set -e

echo "🚀 Запуск развертывания блога researched.xyz..."

# Конфигурация
DOMAIN="researched.xyz"
BLOG_DOMAIN="blog.researched.xyz"
WP_PATH="/var/www/blog"
NGINX_PATH="/etc/nginx/sites-available"
SSL_EMAIL="admin@researched.xyz"

echo "📋 Проверка системных требований..."

# Проверка Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker не установлен. Устанавливаем..."
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    systemctl enable docker
    systemctl start docker
fi

# Проверка Docker Compose
if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose не установлен. Устанавливаем..."
    curl -L "https://github.com/docker/compose/releases/download/v2.20.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
fi

echo "✅ Системные требования проверены"

echo "📁 Создание директорий..."
mkdir -p $WP_PATH
mkdir -p /var/log/wordpress
mkdir -p /etc/letsencrypt

echo "📦 Клонирование кода..."
cd $WP_PATH
if [ ! -d ".git" ]; then
    git clone https://github.com/VladosG155-prog/researched-blog.git .
else
    git pull origin main
fi

echo "🔧 Настройка конфигурации для продакшена..."

# Обновляем docker-compose.yml для продакшена
cat > docker-compose.yml << 'EOF'
services:
  nginx:
    image: nginx:latest
    volumes:
      - ./nginx:/etc/nginx/conf.d
      - ./data/html:/var/www/html
      - ./logs/nginx:/var/log/nginx
      - /etc/letsencrypt:/etc/letsencrypt:ro
    ports:
      - "80:80"
      - "443:443"
    links:
      - wordpress
    restart: unless-stopped

  wordpress:
    depends_on:
      - db
      - redis
    image: wordpress:fpm
    volumes:
      - ./data/html:/var/www/html
      - ./uploads.ini:/usr/local/etc/php/conf.d/uploads.ini
    restart: unless-stopped
    environment:
      WORDPRESS_TABLE_PREFIX: rxz_42_
      WORDPRESS_DB_HOST: db:3306
      WORDPRESS_DB_USER: researched
      WORDPRESS_DB_PASSWORD: ${DB_PASSWORD}
      WORDPRESS_DB_NAME: wordpress
      WORDPRESS_CONFIG_EXTRA: |
        define('WP_REDIS_HOST', 'redis');
        define('WP_REDIS_PORT', 6379);
        define('WP_CACHE', true);

  db:
    image: mysql:8.0
    volumes:
      - ./data/mysql:/var/lib/mysql
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: ${DB_ROOT_PASSWORD}
      MYSQL_DATABASE: wordpress
      MYSQL_USER: researched
      MYSQL_PASSWORD: ${DB_PASSWORD}
    command: --default-authentication-plugin=mysql_native_password

  redis:
    image: redis:7-alpine
    volumes:
      - ./data/redis:/data
    restart: unless-stopped
    command: redis-server --appendonly yes

  elasticsearch:
    image: bitnami/elasticsearch:8.11.1
    environment:
      - "discovery.type=single-node"
      - "xpack.security.enabled=false"
      - "ES_JAVA_OPTS=-Xms1g -Xmx1g"
    volumes:
      - elasticsearch_data:/usr/share/elasticsearch/data
    restart: unless-stopped

volumes:
  elasticsearch_data:
EOF

# Создаем конфигурацию Nginx для продакшена
cat > nginx/nginx.conf << 'EOF'
server {
    listen 80;
    server_name blog.researched.xyz;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name blog.researched.xyz;

    ssl_certificate /etc/letsencrypt/live/blog.researched.xyz/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/blog.researched.xyz/privkey.pem;
    ssl_session_timeout 1d;
    ssl_session_cache shared:SSL:50m;
    ssl_stapling on;
    ssl_stapling_verify on;

    # Безопасность
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header X-Frame-Options DENY always;
    add_header X-Content-Type-Options nosniff always;
    add_header X-XSS-Protection "1; mode=block" always;

    root /var/www/html;
    index index.php;

    access_log /var/log/nginx/blog-access.log;
    error_log /var/log/nginx/blog-error.log;

    # Кэширование статики
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|webp)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass wordpress:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_read_timeout 300;
    }

    # Блокировка доступа к системным файлам
    location ~ /\. {
        deny all;
    }

    location ~* /(?:uploads|files)/.*\.php$ {
        deny all;
    }
}
EOF

# Создаем конфигурацию PHP для загрузок
cat > uploads.ini << 'EOF'
file_uploads = On
memory_limit = 256M
upload_max_filesize = 64M
post_max_size = 64M
max_execution_time = 300
max_input_vars = 3000
EOF

echo "🔐 Настройка переменных окружения..."
if [ ! -f ".env" ]; then
    cat > .env << EOF
DB_PASSWORD=$(openssl rand -base64 32)
DB_ROOT_PASSWORD=$(openssl rand -base64 32)
EOF
    echo "✅ Сгенерированы пароли для базы данных"
fi

source .env

echo "🌐 Получение SSL сертификата..."
if [ ! -f "/etc/letsencrypt/live/${BLOG_DOMAIN}/fullchain.pem" ]; then
    # Временно запускаем nginx для получения сертификата
    docker-compose up -d nginx

    # Получаем сертификат
    docker run --rm \
        -v /etc/letsencrypt:/etc/letsencrypt \
        -v /var/lib/letsencrypt:/var/lib/letsencrypt \
        -p 80:80 \
        certbot/certbot certonly \
        --standalone \
        --agree-tos \
        --email $SSL_EMAIL \
        -d $BLOG_DOMAIN

    # Останавливаем временный nginx
    docker-compose down
fi

echo "🐳 Запуск Docker контейнеров..."
docker-compose up -d

echo "⏳ Ожидание запуска сервисов..."
sleep 30

echo "📊 Проверка состояния контейнеров..."
docker-compose ps

echo "🏥 Проверка здоровья сервисов..."
# Проверка WordPress
if curl -f -s "https://${BLOG_DOMAIN}/wp-admin/install.php" > /dev/null; then
    echo "✅ WordPress запущен"
else
    echo "❌ WordPress недоступен"
fi

# Проверка Elasticsearch
if curl -f -s "http://localhost:9200/_cluster/health" > /dev/null; then
    echo "✅ Elasticsearch запущен"
else
    echo "❌ Elasticsearch недоступен"
fi

echo "🔄 Настройка автообновления SSL сертификата..."
(crontab -l 2>/dev/null; echo "0 3 * * * certbot renew --quiet && docker-compose restart nginx") | crontab -

echo "📈 Настройка мониторинга..."
cat > /etc/systemd/system/blog-monitor.service << 'EOF'
[Unit]
Description=Blog Monitoring
After=network.target

[Service]
Type=oneshot
ExecStart=/usr/local/bin/check-blog-health.sh

[Install]
WantedBy=multi-user.target
EOF

cat > /usr/local/bin/check-blog-health.sh << 'EOF'
#!/bin/bash
BLOG_URL="https://blog.researched.xyz"
WEBHOOK_URL="YOUR_SLACK_WEBHOOK_URL"

if ! curl -f -s "$BLOG_URL" > /dev/null; then
    curl -X POST -H 'Content-type: application/json' \
        --data '{"text":"🚨 Блог researched.xyz недоступен!"}' \
        "$WEBHOOK_URL"
fi
EOF

chmod +x /usr/local/bin/check-blog-health.sh

# Проверка каждые 5 минут
(crontab -l 2>/dev/null; echo "*/5 * * * * /usr/local/bin/check-blog-health.sh") | crontab -

echo "🗄️ Настройка резервного копирования..."
cat > /usr/local/bin/backup-blog.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/backups/blog"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup database
docker-compose exec -T db mysqldump -u researched -p$DB_PASSWORD wordpress > "$BACKUP_DIR/db_$DATE.sql"

# Backup files
tar -czf "$BACKUP_DIR/files_$DATE.tar.gz" ./data/html/wp-content/

# Clean old backups (keep 7 days)
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup completed: $DATE"
EOF

chmod +x /usr/local/bin/backup-blog.sh

# Ежедневный бэкап в 2:00
(crontab -l 2>/dev/null; echo "0 2 * * * cd $WP_PATH && /usr/local/bin/backup-blog.sh") | crontab -

echo "🎯 Настройка Cloudflare (опционально)..."
echo "Добавьте A-запись в Cloudflare:"
echo "blog.researched.xyz -> $(curl -s ifconfig.me)"

echo ""
echo "🎉 Развертывание завершено!"
echo ""
echo "📋 Следующие шаги:"
echo "1. Перейдите на https://${BLOG_DOMAIN}/wp-admin/"
echo "2. Завершите установку WordPress"
echo "3. Активируйте плагины и загрузите демо-контент"
echo "4. Настройте Cloudflare DNS"
echo "5. Проверьте все функции"
echo ""
echo "📊 Полезные команды:"
echo "docker-compose logs -f          # Просмотр логов"
echo "docker-compose restart          # Перезапуск сервисов"
echo "docker-compose down && docker-compose up -d  # Полный перезапуск"
echo ""
echo "📁 Файлы конфигурации:"
echo "- WordPress: /var/www/blog/data/html/"
echo "- Логи: /var/www/blog/logs/"
echo "- Бэкапы: /backups/blog/"
echo ""
echo "✅ Развертывание прошло успешно!"