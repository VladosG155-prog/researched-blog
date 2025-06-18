# Интеграция блога researched.xyz

## Обзор

Данная документация описывает процесс интеграции WordPress блога с основным сайтом researched.xyz.

## 1. Архитектура интеграции

### Вариант A: Поддомен (Рекомендуемый)
```
https://blog.researched.xyz -> WordPress
https://researched.xyz -> Основной сайт
```

### Вариант B: Подпапка
```
https://researched.xyz/articles/ -> WordPress
https://researched.xyz -> Основной сайт
```

## 2. Единая шапка и подвал

### Способ 1: PHP Include
Создать API endpoint на основном сайте:

```php
// /api/header.php
<?php
header('Content-Type: application/json');
echo json_encode([
    'html' => '<header>...</header>',
    'css' => 'https://researched.xyz/assets/header.css',
    'js' => 'https://researched.xyz/assets/header.js'
]);
?>
```

В WordPress header.php:
```php
// Получаем шапку с основного сайта
$header_data = file_get_contents('https://researched.xyz/api/header.php');
$header = json_decode($header_data, true);
echo $header['html'];
```

### Способ 2: JavaScript интеграция
```javascript
// В header.php WordPress
fetch('https://researched.xyz/api/header.php')
  .then(response => response.json())
  .then(data => {
    document.getElementById('main-header').innerHTML = data.html;
    // Подключаем стили и скрипты
  });
```

## 3. Уведомления о публикации

### Webhook для Slack
Добавить в functions.php:

```php
function notify_slack_on_publish($post_id) {
    if (get_post_status($post_id) === 'publish') {
        $webhook_url = 'https://hooks.slack.com/services/YOUR/WEBHOOK/URL';
        
        $message = [
            'text' => '📝 Новая статья опубликована!',
            'attachments' => [[
                'title' => get_the_title($post_id),
                'title_link' => get_permalink($post_id),
                'text' => get_the_excerpt($post_id),
                'color' => '#D06E31'
            ]]
        ];
        
        wp_remote_post($webhook_url, [
            'body' => json_encode($message),
            'headers' => ['Content-Type' => 'application/json']
        ]);
    }
}
add_action('wp_insert_post', 'notify_slack_on_publish');
```

### Webhook для Telegram
```php
function notify_telegram_on_publish($post_id) {
    if (get_post_status($post_id) === 'publish') {
        $bot_token = 'YOUR_BOT_TOKEN';
        $chat_id = 'YOUR_CHAT_ID';
        
        $message = "📝 *Новая статья!*\n\n";
        $message .= "*" . get_the_title($post_id) . "*\n";
        $message .= get_the_excerpt($post_id) . "\n\n";
        $message .= "[Читать статью](" . get_permalink($post_id) . ")";
        
        wp_remote_get("https://api.telegram.org/bot{$bot_token}/sendMessage?" . http_build_query([
            'chat_id' => $chat_id,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]));
    }
}
add_action('wp_insert_post', 'notify_telegram_on_publish');
```

## 4. SEO интеграция

### Canonical URLs
Убедитесь, что canonical URLs указывают на правильные адреса:

```php
// В functions.php уже добавлено
function researched_add_canonical() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . get_permalink() . '">' . "\n";
    }
    // ...
}
```

### Sitemap интеграция
Если на основном сайте есть sitemap, добавьте ссылку на WordPress sitemap:

```xml
<!-- В основном sitemap.xml -->
<sitemap>
    <loc>https://blog.researched.xyz/sitemap_index.xml</loc>
    <lastmod>2024-01-01</lastmod>
</sitemap>
```

## 5. Аналитика

### Google Analytics
Добавить в header.php одинаковый GA код для отслеживания пользователей между сайтами.

### Единые цели конверсии
Настроить отслеживание переходов между основным сайтом и блогом.

## 6. Авторизация (опционально)

### Single Sign-On (SSO)
Если нужна единая авторизация:

```php
// Проверка авторизации с основного сайта
function check_main_site_auth() {
    $auth_token = $_COOKIE['main_site_token'] ?? null;
    
    if ($auth_token) {
        $response = wp_remote_get('https://researched.xyz/api/verify-token?token=' . $auth_token);
        $user_data = json_decode(wp_remote_retrieve_body($response), true);
        
        if ($user_data['valid']) {
            // Автоматически авторизуем в WordPress
            wp_set_current_user($user_data['wp_user_id']);
        }
    }
}
add_action('init', 'check_main_site_auth');
```

## 7. Производительность

### CDN интеграция
Используйте один CDN для обоих сайтов:

```php
// В wp-config.php
define('WP_CONTENT_URL', 'https://cdn.researched.xyz/wp-content');
```

### Shared кэш
Настройте Redis/Memcached для кэширования между сайтами.

## 8. Мониторинг

### Uptime monitoring
Добавьте мониторинг доступности блога:
- https://blog.researched.xyz/wp-admin/
- https://blog.researched.xyz/feed/

### Performance monitoring
Настройте алерты для:
- TTFB > 200ms
- Page Load Time > 3s
- Memory usage > 80%

## 9. Backup стратегия

### Автоматические бэкапы
```bash
#!/bin/bash
# /home/scripts/backup-blog.sh

# Backup database
mysqldump -u user -p database > /backups/blog-db-$(date +%Y%m%d).sql

# Backup files
tar -czf /backups/blog-files-$(date +%Y%m%d).tar.gz /var/www/blog/

# Clean old backups (keep 30 days)
find /backups/ -name "blog-*" -mtime +30 -delete
```

Добавить в cron:
```bash
0 2 * * * /home/scripts/backup-blog.sh
```

## 10. Deployment

### Git hooks
```bash
#!/bin/bash
# /var/www/blog/.git/hooks/post-receive

cd /var/www/blog
git --git-dir=/var/www/blog/.git --work-tree=/var/www/blog checkout -f

# Clear cache
wp cache flush --path=/var/www/blog

# Restart services if needed
systemctl reload nginx
```

### Staging environment
```bash
# Sync production to staging
rsync -av --exclude=wp-config.php production:/var/www/blog/ staging:/var/www/blog-staging/
```

## Контакты

Для вопросов по интеграции обращайтесь к техническому специалисту проекта. 