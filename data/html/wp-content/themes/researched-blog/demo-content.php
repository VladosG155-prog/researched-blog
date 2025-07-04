<?php
/**
 * Демо-контент для блога researched.xyz
 * 
 * Выполните этот файл через админку WordPress для создания тестового контента
 */

// Предотвращаем прямой доступ
if (!defined('ABSPATH')) {
    exit;
}

function create_researched_demo_content() {
    // Проверяем права администратора
    if (!current_user_can('manage_options')) {
        wp_die('У вас нет прав для выполнения этого действия.');
    }

    $demo_posts = array(
        array(
            'title' => 'Как пользоваться системой researched.xyz: полное руководство',
            'content' => '
<p>Добро пожаловать в мир аналитических исследований! В этой статье мы подробно разберем, как эффективно использовать все возможности платформы researched.xyz.</p>

<h2>Первые шаги в системе</h2>

<p>После регистрации на платформе вы получаете доступ к мощному инструментарию для проведения исследований. Начнем с основ:</p>

<ol>
<li><strong>Создание проекта</strong> — определите цели и задачи вашего исследования</li>
<li><strong>Настройка параметров</strong> — выберите методологию и критерии анализа</li>
<li><strong>Сбор данных</strong> — используйте встроенные инструменты для агрегации информации</li>
</ol>

<h3>Интерфейс платформы</h3>

<p>Основные разделы системы:</p>

<ul>
<li>Дашборд с ключевыми метриками</li>
<li>Модуль создания исследований</li>
<li>Библиотека шаблонов</li>
<li>Аналитические инструменты</li>
<li>Экспорт результатов</li>
</ul>

<blockquote>
<p>Совет: начните с готовых шаблонов — это поможет быстрее освоить возможности платформы.</p>
</blockquote>

<h2>Продвинутые функции</h2>

<p>После освоения базового функционала можно переходить к более сложным задачам:</p>

<pre><code>// Пример API-запроса
const research = await fetch("/api/research", {
  method: "POST",
  headers: {
    "Content-Type": "application/json"
  },
  body: JSON.stringify({
    query: "market analysis",
    filters: ["2023", "technology"]
  })
});
</code></pre>

<p>Помните: качественные данные — основа любого успешного исследования!</p>
            ',
            'category' => 'Руководства',
            'tags' => array('гайд', 'начинающим', 'платформа', 'исследования'),
            'excerpt' => 'Подробное руководство по использованию всех возможностей платформы researched.xyz для начинающих пользователей.'
        ),
        
        array(
            'title' => 'Чек-лист эффективного исследования: 15 ключевых шагов',
            'content' => '
<p>Успешное исследование требует системного подхода. Мы подготовили подробный чек-лист, который поможет вам не упустить важные детали.</p>

<h2>Этап планирования</h2>

<h3>1-5. Определение целей и задач</h3>

<ul>
<li>✅ Сформулировать главную гипотезу исследования</li>
<li>✅ Определить целевую аудиторию</li>
<li>✅ Выбрать методологию исследования</li>
<li>✅ Установить временные рамки</li>
<li>✅ Подготовить бюджет проекта</li>
</ul>

<h3>6-10. Подготовка инструментов</h3>

<ul>
<li>✅ Настроить аналитические инструменты</li>
<li>✅ Подготовить опросники и анкеты</li>
<li>✅ Определить источники данных</li>
<li>✅ Создать систему метрик</li>
<li>✅ Настроить процесс валидации данных</li>
</ul>

<h2>Этап выполнения</h2>

<h3>11-15. Сбор и анализ</h3>

<ul>
<li>✅ Провести сбор первичных данных</li>
<li>✅ Агрегировать вторичные источники</li>
<li>✅ Проверить качество данных</li>
<li>✅ Выполнить статистический анализ</li>
<li>✅ Подготовить итоговый отчет</li>
</ul>

<h2>Частые ошибки</h2>

<p>Избегайте этих распространенных проблем:</p>

<ol>
<li><strong>Размытые цели</strong> — четко определите, что именно вы хотите узнать</li>
<li><strong>Малая выборка</strong> — убедитесь в статистической значимости</li>
<li><strong>Предвзятость</strong> — используйте объективные методы сбора данных</li>
</ol>

<blockquote>
<p>Качественное исследование — это 80% правильной подготовки и 20% исполнения.</p>
</blockquote>

<p>Следуя этому чек-листу, вы значительно повысите качество и достоверность ваших исследований.</p>
            ',
            'category' => 'Методология',
            'tags' => array('чек-лист', 'планирование', 'методология', 'советы'),
            'excerpt' => 'Пошаговый чек-лист из 15 ключевых пунктов для проведения качественных исследований на платформе.'
        ),
        
        array(
            'title' => 'Case Study: как стартап увеличил конверсию на 240% с помощью data-driven подхода',
            'content' => '
<p>В этом кейсе мы разберем реальный пример того, как молодая IT-компания использовала аналитические инструменты для кратного роста бизнес-показателей.</p>

<h2>Исходная ситуация</h2>

<p><strong>Компания:</strong> SaaS-стартап в сфере автоматизации<br>
<strong>Проблема:</strong> низкая конверсия с trial в paid (всего 2.3%)<br>
<strong>Цель:</strong> увеличить конверсию до 8-10%</p>

<h3>Состояние до внедрения</h3>

<ul>
<li>Monthly Recurring Revenue: $12,000</li>
<li>Trial-to-Paid конверсия: 2.3%</li>
<li>Customer Acquisition Cost: $180</li>
<li>Lifetime Value: $420</li>
</ul>

<h2>Методология исследования</h2>

<p>Команда использовала комплексный подход:</p>

<ol>
<li><strong>Behavioral Analytics</strong> — отслеживание действий пользователей</li>
<li><strong>Cohort Analysis</strong> — анализ поведения различных групп</li>
<li><strong>A/B Testing</strong> — тестирование гипотез</li>
<li><strong>User Interviews</strong> — качественная обратная связь</li>
</ol>

<h3>Ключевые инсайты</h3>

<p>Исследование выявило критические проблемы:</p>

<blockquote>
<p>65% пользователей не понимали основную ценность продукта в первые 48 часов trial-периода</p>
</blockquote>

<table>
<thead>
<tr>
<th>Проблема</th>
<th>% пользователей</th>
<th>Влияние на конверсию</th>
</tr>
</thead>
<tbody>
<tr>
<td>Сложный onboarding</td>
<td>78%</td>
<td>-40%</td>
</tr>
<tr>
<td>Неочевидная ценность</td>
<td>65%</td>
<td>-35%</td>
</tr>
<tr>
<td>Технические баги</td>
<td>23%</td>
<td>-15%</td>
</tr>
</tbody>
</table>

<h2>Внедренные решения</h2>

<h3>1. Переработка onboarding-процесса</h3>

<pre><code>// Пример прогрессивного onboarding
const onboardingSteps = [
  { step: 1, action: "account_setup", time_limit: "2min" },
  { step: 2, action: "first_project", time_limit: "5min" },
  { step: 3, action: "key_feature_demo", time_limit: "3min" }
];
</code></pre>

<h3>2. Система прогрессивного раскрытия функций</h3>

<ul>
<li>Упрощенный интерфейс для новых пользователей</li>
<li>Контекстные подсказки и туториалы</li>
<li>Геймификация процесса обучения</li>
</ul>

<h3>3. Персонализированные email-кампании</h3>

<p>Основанные на поведенческих данных:</p>

<ul>
<li>День 1: Welcome + Quick Start Guide</li>
<li>День 3: Success Stories + Best Practices</li>
<li>День 7: Personal Demo Offer</li>
<li>День 10: Limited Time Discount</li>
</ul>

<h2>Результаты</h2>

<h3>Ключевые метрики через 6 месяцев:</h3>

<ul>
<li>✅ Trial-to-Paid конверсия: <strong>7.8%</strong> (рост на 240%)</li>
<li>✅ Monthly Recurring Revenue: <strong>$38,400</strong> (рост на 220%)</li>
<li>✅ Customer Acquisition Cost: <strong>$95</strong> (снижение на 47%)</li>
<li>✅ Lifetime Value: <strong>$1,180</strong> (рост на 180%)</li>
</ul>

<blockquote>
<p>Самое важное открытие: данные не врут. Если слушать пользователей и принимать решения на основе фактов, результат не заставит себя ждать.</p>
</blockquote>

<h2>Выводы</h2>

<p>Этот кейс показывает силу data-driven подхода:</p>

<ol>
<li><strong>Измеряйте всё</strong> — без метрик невозможно улучшение</li>
<li><strong>Слушайте пользователей</strong> — качественная обратная связь критично важна</li>
<li><strong>Тестируйте гипотезы</strong> — не полагайтесь на интуицию</li>
<li><strong>Итерируйте быстро</strong> — маленькие изменения могут дать большой эффект</li>
</ol>

<p>Хотите провести подобное исследование для вашего продукта? Начните с базовой аналитики и постепенно углубляйтесь в данные.</p>
            ',
            'category' => 'Кейсы',
            'tags' => array('case-study', 'конверсия', 'стартап', 'аналитика', 'SaaS'),
            'excerpt' => 'Реальный кейс IT-стартапа, который увеличил конверсию на 240% благодаря правильному анализу данных и пользовательского поведения.'
        )
    );

    $created_posts = array();

    foreach ($demo_posts as $post_data) {
        // Создаем категорию если не существует
        $category = wp_create_category($post_data['category']);
        
        // Создаем пост
        $post_id = wp_insert_post(array(
            'post_title' => $post_data['title'],
            'post_content' => $post_data['content'],
            'post_excerpt' => $post_data['excerpt'],
            'post_status' => 'publish',
            'post_type' => 'post',
            'post_category' => array($category),
            'meta_input' => array(
                '_yoast_wpseo_metadesc' => $post_data['excerpt'],
                '_yoast_wpseo_focuskw' => $post_data['tags'][0] ?? '',
            )
        ));

        if ($post_id && !is_wp_error($post_id)) {
            // Добавляем теги
            wp_set_post_tags($post_id, $post_data['tags']);
            
            $created_posts[] = array(
                'id' => $post_id,
                'title' => $post_data['title'],
                'url' => get_permalink($post_id)
            );
        }
    }

    return $created_posts;
}

// Если файл вызван напрямую через админку
if (isset($_GET['create_demo_content']) && $_GET['create_demo_content'] === '1') {
    $results = create_researched_demo_content();
    
    echo '<div style="font-family: -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,sans-serif; max-width: 600px; margin: 50px auto; padding: 20px;">';
    echo '<h1>✅ Демо-контент создан успешно!</h1>';
    echo '<p>Было создано ' . count($results) . ' статей:</p>';
    echo '<ul>';
    foreach ($results as $post) {
        echo '<li><a href="' . $post['url'] . '" target="_blank">' . $post['title'] . '</a></li>';
    }
    echo '</ul>';
    echo '<p><a href="' . home_url() . '">← Вернуться на главную</a></p>';
    echo '</div>';
}
?> 