#!/bin/bash

echo "🚀 Деплой researched.xyz"
echo "========================"

# Проверяем наличие Docker
if ! command -v docker &> /dev/null; then
    echo "❌ Docker не установлен!"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo "❌ Docker Compose не установлен!"
    exit 1
fi

# Копируем переменные окружения
if [ ! -f .env ]; then
    echo "📝 Создаю .env из production.env..."
    cp production.env .env
    echo "✅ Файл .env создан"
else
    echo "✅ Файл .env уже существует"
fi

# Останавливаем старые контейнеры
echo "🛑 Останавливаю старые контейнеры..."
docker-compose down

# Собираем и запускаем
echo "🏗️  Запускаю новые контейнеры..."
docker-compose up -d --build

# Проверяем статус
echo "🔍 Проверяю статус контейнеров..."
docker-compose ps

echo ""
echo "✅ Деплой завершен!"
echo "🌐 Ваш сайт доступен на порту 8080/blog"
echo "📊 Для мониторинга: docker-compose logs -f"
echo ""
echo "🔧 Настройте прокси для:"
echo "   - Внутренний адрес: http://localhost:8080/blog"
echo "   - Внешний домен: https://researched.xyz/blog"
echo ""
echo "📁 WordPress установлен в подпапку /blog" 