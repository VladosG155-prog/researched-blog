# 🚀 Чек-лист подготовки к продакшену

## ✅ Что уже сделано

### Безопасность
- [x] Изменен префикс таблиц БД на `rxz_42_`
- [x] Отключен XML-RPC
- [x] Скрыта версия WordPress
- [x] Ограничен REST API
- [x] Добавлена защита в .htaccess
- [x] Настроены заголовки безопасности
- [x] Защита от SQL-инъекций
- [x] Блокировка доступа к системным файлам

### SEO
- [x] Canonical URLs
- [x] JSON-LD схемы (BlogPosting, BreadcrumbList)
- [x] Robots.txt
- [x] Мета-теги для статей
- [x] Structured data

### Производительность
- [x] Сжатие GZIP
- [x] Кэширование статических файлов
- [x] Lazy loading изображений
- [x] WebP поддержка
- [x] Оптимизация скриптов
- [x] Cache-Control заголовки

### Функциональность
- [x] Полностью настроенная тема
- [x] Система поиска с ElasticPress
- [x] Связанные статьи
- [x] Время чтения
- [x] Responsive дизайн
- [x] Демо-контент готов к загрузке

## 📋 Задачи для продакшена

### 1. Подготовка сервера
```bash
# На продакшн сервере выполните:
curl -O https://your-repo.com/production-deploy.sh
chmod +x production-deploy.sh
sudo ./production-deploy.sh
```

### 2. Настройка DNS
- [ ] Добавить A-запись: `blog.researched.xyz → IP_СЕРВЕРА`
- [ ] Настроить Cloudflare (рекомендуется)
- [ ] Проверить propagation: `dig blog.researched.xyz`

### 3. SSL сертификат
- [ ] Получен автоматически через Let's Encrypt
- [ ] Настроено автообновление
- [ ] Проверка: `curl -I https://blog.researched.xyz`

### 4. Первоначальная настройка WordPress

#### Войти в админку
```
URL: https://blog.researched.xyz/wp-admin/
Создать пользователя-администратора
```

#### Активировать плагины
- [ ] Yoast SEO
- [ ] ElasticPress
- [ ] WP Fastest Cache
- [ ] Wordfence Security
- [ ] Advanced Custom Fields

#### Загрузить демо-контент
```
URL: https://blog.researched.xyz/wp-content/themes/researched-blog/demo-content.php?create_demo_content=1
```

### 5. Настройка плагинов

#### Yoast SEO
- [ ] Настроить general settings
- [ ] Включить XML sitemap
- [ ] Настроить социальные сети
- [ ] Добавить Google Search Console

#### ElasticPress
- [ ] Подключить к Elasticsearch
- [ ] Запустить полную индексацию
- [ ] Настроить синонимы
- [ ] Проверить поиск

#### WP Fastest Cache
- [ ] Включить кэширование
- [ ] Настроить минификацию CSS/JS
- [ ] Добавить правила исключений
- [ ] Настроить preload

#### Wordfence Security
- [ ] Запустить scan
- [ ] Настроить firewall
- [ ] Включить 2FA для админов
- [ ] Настроить email уведомления

### 6. Интеграция с основным сайтом

#### Единая навигация
- [ ] Реализовать вариант из `INTEGRATION.md`
- [ ] Проверить cross-domain cookies
- [ ] Настроить единые стили

#### Аналитика
- [ ] Добавить Google Analytics
- [ ] Настроить цели конверсии
- [ ] Настроить отслеживание переходов

#### Уведомления
- [ ] Webhook для Slack (опционально)
- [ ] Webhook для Telegram (опционально)

### 7. Monitoring & Alerting

#### Uptime monitoring
- [ ] Настроить мониторинг доступности
- [ ] Добавить alerting в Slack/Email
- [ ] Проверить мониторинг раз в день

#### Performance monitoring
- [ ] Google PageSpeed Insights
- [ ] GTmetrix анализ
- [ ] Core Web Vitals мониторинг

### 8. Backup & Recovery

#### Автоматические бэкапы
- [ ] Проверить работу ежедневных бэкапов
- [ ] Тест восстановления из бэкапа
- [ ] Настроить off-site backup (S3/Google Drive)

#### Disaster recovery plan
- [ ] Документировать процедуру восстановления
- [ ] Тестовое восстановление на staging

### 9. Тестирование

#### Функциональное тестирование
- [ ] Создание/редактирование статей
- [ ] Работа поиска
- [ ] Комментарии (если включены)
- [ ] Контактные формы

#### Кроссбраузерное тестирование
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Мобильные браузеры

#### Performance тестирование
- [ ] Load testing с помощью Apache Bench
- [ ] Stress testing
- [ ] Memory usage analysis

### 10. Security audit

#### Проверки безопасности
- [ ] Scan с помощью WPScan
- [ ] SSL Labs test (A+ rating)
- [ ] Security headers check
- [ ] Penetration testing

#### Hardening
- [ ] Отключить file editing в админке
- [ ] Ограничить login attempts
- [ ] Настроить fail2ban
- [ ] Регулярные обновления

## 🔧 Полезные команды

### Docker операции
```bash
# Просмотр логов
docker-compose logs -f

# Перезапуск сервисов
docker-compose restart

# Обновление контейнеров
docker-compose pull && docker-compose up -d

# Очистка кэша WordPress
docker-compose exec wordpress wp cache flush --path=/var/www/html
```

### Мониторинг
```bash
# Проверка дискового пространства
df -h

# Мониторинг использования памяти
free -h

# Проверка нагрузки
htop

# Логи nginx
tail -f /var/log/nginx/blog-*.log
```

### Debugging
```bash
# Включить debug режим
echo "define('WP_DEBUG', true);" >> wp-config.php

# Проверить PHP ошибки
docker-compose exec wordpress tail -f /var/log/php_errors.log

# Elasticsearch статус
curl http://localhost:9200/_cluster/health?pretty
```

## 📞 Контакты поддержки

В случае проблем:
1. Проверьте логи: `docker-compose logs`
2. Проверьте мониторинг
3. Обратитесь к DevOps команде

## 🎯 Критерии готовности

Блог готов к продакшену когда:
- [ ] SSL сертификат работает (A+ rating)
- [ ] Все плагины активированы и настроены
- [ ] Поиск работает корректно
- [ ] Демо-контент загружен
- [ ] Backup система работает
- [ ] Мониторинг настроен
- [ ] Performance тесты пройдены
- [ ] Security audit завершен
- [ ] Integration тестирование пройдено

**Estimated time:** 4-6 часов для полной настройки 