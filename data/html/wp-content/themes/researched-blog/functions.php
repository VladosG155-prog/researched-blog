<?php

/**
 * Подсчет времени чтения статьи.
 */
function researched_calculate_reading_time($content) {
    $words_per_minute = 200;
    // Очищаем контент от HTML тегов
    $clean_content = strip_tags($content);
    // Убираем лишние пробелы и переносы
    $clean_content = preg_replace('/\s+/', ' ', trim($clean_content));
    
    // Считаем слова (работает для кириллицы и латиницы)
    $word_count = count(preg_split('/\s+/', $clean_content, -1, PREG_SPLIT_NO_EMPTY));
    
    // Возвращаем время в минутах (минимум 1 минута)
    return max(1, ceil($word_count / $words_per_minute));
}

/**
 * Автоматическое исправление URL и настройка блога при первом запуске на продакшене
 */
function researched_auto_fix_urls_on_production() {
    // Проверяем, запущен ли уже скрипт
    if (get_option('researched_urls_fixed')) {
        return;
    }

    // Проверяем, что мы на продакшене (не localhost)
    $current_domain = $_SERVER['HTTP_HOST'] ?? '';
    if (strpos($current_domain, 'localhost') !== false || strpos($current_domain, '127.0.0.1') !== false) {
        return;
    }

    global $wpdb;
    
    // Получаем текущий URL сайта
    $current_home = get_option('home');
    $current_siteurl = get_option('siteurl');
    
    // Если URL содержат localhost, исправляем их
    if (strpos($current_home, 'localhost') !== false || strpos($current_siteurl, 'localhost') !== false) {
        $new_url = 'https://researched.xyz/blog';
        
        // Обновляем основные настройки
        update_option('home', $new_url);
        update_option('siteurl', $new_url);
        
        // Обновляем URL в контенте
        $wpdb->query($wpdb->prepare("
            UPDATE {$wpdb->posts} 
            SET post_content = REPLACE(post_content, %s, %s)
        ", 'http://localhost:8080', $new_url));
        
        $wpdb->query($wpdb->prepare("
            UPDATE {$wpdb->posts} 
            SET post_content = REPLACE(post_content, %s, %s)
        ", 'http://localhost', $new_url));
        
        // Обновляем мета данные
        $wpdb->query($wpdb->prepare("
            UPDATE {$wpdb->postmeta} 
            SET meta_value = REPLACE(meta_value, %s, %s)
        ", 'http://localhost:8080', $new_url));
        
        $wpdb->query($wpdb->prepare("
            UPDATE {$wpdb->postmeta} 
            SET meta_value = REPLACE(meta_value, %s, %s)
        ", 'http://localhost', $new_url));
        
        // WordPress уже находится в /blog, дополнительная страница не нужна
        
        // Настраиваем постоянные ссылки
        researched_setup_permalinks();
        
        // Очищаем кэш
        wp_cache_flush();
        
        // Отмечаем, что скрипт выполнен
        update_option('researched_urls_fixed', true);
    }
}

/**
 * Создание страницы блога /blog
 */
function researched_create_blog_page() {
    // Проверяем, существует ли уже страница блога
    $blog_page = get_page_by_path('blog');
    
    if (!$blog_page) {
        // Создаем страницу блога
        $blog_page_id = wp_insert_post([
            'post_title' => 'Блог',
            'post_name' => 'blog',
            'post_content' => '',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_author' => 1,
        ]);
        
        if ($blog_page_id && !is_wp_error($blog_page_id)) {
            // Устанавливаем эту страницу как страницу для постов
            update_option('page_for_posts', $blog_page_id);
            
            // Устанавливаем статичную главную страницу
            $front_page = get_page_by_path('home') ?: get_page_by_path('main');
            if (!$front_page) {
                // Создаем главную страницу если её нет
                $front_page_id = wp_insert_post([
                    'post_title' => 'Главная',
                    'post_name' => 'home',
                    'post_content' => '<h1>Добро пожаловать на researched.xyz</h1>',
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_author' => 1,
                ]);
                if ($front_page_id && !is_wp_error($front_page_id)) {
                    update_option('page_on_front', $front_page_id);
                }
            } else {
                update_option('page_on_front', $front_page->ID);
            }
            
            // Включаем статичные страницы
            update_option('show_on_front', 'page');
        }
    }
}

/**
 * Настройка постоянных ссылок для researched.xyz/blog
 */
function researched_setup_permalinks() {
    // Устанавливаем структуру постоянных ссылок
    update_option('permalink_structure', '/%postname%/');
    
    // Обновляем правила перезаписи
    flush_rewrite_rules();
}

add_action('init', 'researched_auto_fix_urls_on_production');

/**
 * Получение похожих постов на основе категорий и тегов.
 */
function researched_get_similar_posts($post_id) {
    $categories = wp_get_post_categories($post_id, ['fields' => 'ids']);
    $tags = wp_get_post_tags($post_id, ['fields' => 'ids']);

    $args = [
        'post_type' => 'post',
        'post__not_in' => [$post_id],
        'posts_per_page' => 3,
        'tax_query' => [
            'relation' => 'OR',
            [
                'taxonomy' => 'category',
                'field'    => 'term_id',
                'terms'    => $categories,
            ],
            [
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => $tags,
            ],
        ],
    ];

    $query = new WP_Query($args);
    $posts = $query->posts;

    // Если найдено меньше 3 постов, дополняем последними опубликованными
    if ($query->post_count < 3) {
        $exclude_ids = array_merge([$post_id], wp_list_pluck($posts, 'ID'));
        $additional_args = [
            'post_type' => 'post',
            'post__not_in' => $exclude_ids,
            'posts_per_page' => 3 - $query->post_count,
            'orderby' => 'date',
            'order' => 'DESC',
        ];
        $additional_query = new WP_Query($additional_args);
        $posts = array_merge($posts, $additional_query->posts);
        
        // Сбрасываем глобальные данные
        wp_reset_postdata();
    }

    // Сбрасываем глобальные данные
    wp_reset_postdata();
    
    return $posts;
}

/**
 * Регистрация сайдбаров.
 */
function researched_register_sidebars() {
    register_sidebar(array(
        'name' => 'Blog Sidebar',
        'id' => 'blog-sidebar',
        'description' => 'Боковая панель для страниц блога',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ));
}
add_action('widgets_init', 'researched_register_sidebars');

/**
 * Основная функция настройки темы: подключение стилей, скриптов и т.д.
 */
if (!function_exists('researched_blog_theme_setup')) {
    function researched_blog_theme_setup()
    {
        add_theme_support('title-tag');
        add_theme_support('post-thumbnails');

        add_action('wp_enqueue_scripts', function () {
            // Версия темы (для продакшена используем статичную версию)
            $theme_version = wp_get_theme()->get('Version') ?: '1.0.8';
            
            // Принудительно подключаем jQuery первым
            wp_enqueue_script('jquery');
            
            // Подключаем CSS для контента статей
            wp_enqueue_style('article-content-styles', get_template_directory_uri() . '/assets/css/article-content.css', [], $theme_version);
            
            // Подключаем JavaScript файлы
            // Live search подключаем только на страницах с поиском
            if (is_home() || is_front_page() || is_archive() || is_search() || is_404()) {
            wp_enqueue_script('instant-search', get_template_directory_uri() . '/js/instant-search.js', ['jquery'], $theme_version, true);
                
                // Локализация для AJAX только там, где нужно
                wp_localize_script('instant-search', 'researched_ajax_obj', [
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('researched_live_search_nonce')
                ]);
            }
            
            wp_enqueue_script('footer-script', get_template_directory_uri() . '/js/footer-script.js', [], $theme_version, true);
            
        });
        
        // Оптимизация для продакшена
        add_action('init', function() {
            // Отключаем XML-RPC если не используется
            add_filter('xmlrpc_enabled', '__return_false');
            
            // Удаляем ненужные мета-теги
            remove_action('wp_head', 'wp_generator');
            remove_action('wp_head', 'rsd_link');
            remove_action('wp_head', 'wlwmanifest_link');
            remove_action('wp_head', 'wp_shortlink_wp_head');
        });
        
        // Disable XML-RPC
        add_filter('xmlrpc_enabled', '__return_false');
    }
}
add_action('after_setup_theme', 'researched_blog_theme_setup');

// Функция для проверки подключения к Elasticsearch
function check_elasticsearch_connection() {
    // Для продакшена - можете изменить на ваш Elasticsearch сервер
    $es_url = defined('ELASTICSEARCH_URL') ? ELASTICSEARCH_URL : 'http://localhost:9200';
    
    $response = wp_remote_get($es_url, [
        'timeout' => 5,
        'headers' => [
            'Content-Type' => 'application/json'
        ]
    ]);
    
    return !is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200;
}

// Улучшенный обработчик live search с поддержкой ElasticPress
function researched_live_search_handler()
{
    if (!wp_verify_nonce($_POST['nonce'], 'researched_live_search_nonce')) {
        wp_die('Security check failed');
    }

    $search_query = sanitize_text_field($_POST['query']);
    
    // Если ElasticPress активен и доступен, используем его
    $use_elasticsearch = function_exists('ep_is_activated') && ep_is_activated() && check_elasticsearch_connection();
    
    $query_args = [
        's' => $search_query,
        'post_type' => 'post',
        'posts_per_page' => 20,
        'post_status' => 'publish',
        'orderby' => $use_elasticsearch ? 'relevance' : 'date',
        'order' => 'DESC'
    ];
    
    // Если используем ElasticPress, добавляем специальные параметры
    if ($use_elasticsearch) {
        $query_args['ep_integrate'] = true;
        $query_args['search_fields'] = [
            'post_title' => 5,
            'post_content' => 1,
            'post_excerpt' => 3,
            'taxonomies' => 2
        ];
    }
    
    $query = new WP_Query($query_args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_tags = wp_get_post_tags(get_the_ID(), array('fields' => 'names'));
            $reading_time = researched_calculate_reading_time(get_the_content());
            ?>
            <article class="bg-[#222223] shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105  overflow-hidden" onclick="window.location.href='<?php echo esc_url(get_permalink()); ?>'">
                <div class="h-48 relative overflow-hidden">
                    <?php if (has_post_thumbnail()) : ?>
                        <?php the_post_thumbnail('medium_large', array(
                            'class' => 'w-full h-full object-cover',
                            'style' => 'width: 100%; height: 100%;'
                        )); ?>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <?php else : ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/cooltmp.webp" alt="<?php the_title(); ?>" class="w-full h-full object-cover" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/20 to-transparent"></div>
                    <?php endif; ?>
                    <div class="absolute bottom-4 left-4 right-4">
                        <h2 class="text-white font-bold text-lg leading-tight line-clamp-2" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;"><?php the_title(); ?></h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center text-sm text-gray-400 mb-3 space-x-4">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                            </svg>
                            <?php echo get_the_date('j F Y'); ?>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                            <?php echo $reading_time; ?> мин
                        </div>
                    </div>
                    <div class="text-gray-300 text-sm leading-relaxed line-clamp-3 mb-4" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                        <?php echo wp_trim_words(strip_tags(get_the_content()), 20); ?>
                    </div>
                    <?php if ($post_tags) : ?>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach (array_slice($post_tags, 0, 3) as $tag) : ?>
                                <span class="inline-flex items-center px-2.5 py-0.5  text-xs font-medium bg-[#D06E31] bg-opacity-20 text-[#D06E31]">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                    </svg>
                                    <?php echo esc_html($tag); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </article>
            <?php
        }
    } else {
        echo '<div class="text-center py-12 col-span-full"><h3 class="text-xl font-semibold text-gray-400 mb-2">Статьи не найдены</h3><p class="text-gray-500">Попробуйте изменить запрос.</p></div>';
    }

    wp_reset_postdata();
    wp_die();
}

add_action('wp_ajax_researched_live_search', 'researched_live_search_handler');
add_action('wp_ajax_nopriv_researched_live_search', 'researched_live_search_handler');

// Стили для пагинации и поисковых карточек
function researched_pagination_styles() {
    ?>
    <style>
    .page-numbers {
        display: inline-block !important;
        padding: 8px 12px !important;
        margin: 0 4px !important;
        background-color: #222223 !important;
        color: #ffffff !important;
        border: 1px solid #404040 !important;
        border-radius: 0 !important;
        text-decoration: none !important;
        font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif !important;
        font-size: 14px !important;
        transition: all 0.3s ease !important;
    }
    
    .page-numbers:hover {
        background-color: #D06E31 !important;
        border-color: #D06E31 !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
    }
    
    .page-numbers.current {
        background-color: #D06E31 !important;
        border-color: #D06E31 !important;
        color: #ffffff !important;
    }
    
    .page-numbers.dots {
        background-color: transparent !important;
        border: none !important;
        color: #666666 !important;
    }
    
    .page-numbers.prev,
    .page-numbers.next {
        font-weight: 600 !important;
    }
    
    .wp-pagenavi {
        text-align: center !important;
        margin: 20px 0 !important;
    }
    
    .wp-pagenavi .pages {
        color: #999999 !important;
        margin-right: 10px !important;
        font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif !important;
    }
    
    /* Стили для карточек статей с правильными hover-эффектами */
    #posts-grid article {
        cursor: pointer !important;
        transition: all 0.3s ease !important;
    }
    
    #posts-grid article:hover,
    #posts-grid article.hover-active {
        transform: translateY(-2px) scale(1.02) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3) !important;
    }
    
    /* Состояние загрузки */
    #posts-grid.loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    /* Анимация для состояния поиска */
    .search-active {
        border-color: #D06E31 !important;
        box-shadow: 0 0 0 1px #D06E31 !important;
    }
    
    /* Плавные переходы для результатов поиска */
    .no-results {
        animation: fadeInUp 0.3s ease;
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
    <?php
}
add_action('wp_head', 'researched_pagination_styles');

// Очистка кэша похожих статей при изменении постов
function clear_related_posts_cache($post_id) {
    wp_cache_delete('researched_similar_posts_' . $post_id, 'researched');
}
add_action('save_post', 'clear_related_posts_cache');



// Безопасность: отключаем XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Безопасность: удаляем версию WordPress из head
function remove_wp_version_strings($src) {
    global $wp_version;
    parse_str(parse_url($src, PHP_URL_QUERY), $query);
    if (isset($query['ver']) && $query['ver'] === $wp_version) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
add_filter('script_loader_src', 'remove_wp_version_strings');
add_filter('style_loader_src', 'remove_wp_version_strings');

// Безопасность: скрываем версию WP
function researched_remove_version() {
    return '';
}
add_filter('the_generator', 'researched_remove_version');

// Безопасность: ограничиваем REST API только для авторизованных пользователей
function researched_restrict_rest_api($access) {
    if (!is_user_logged_in()) {
        return new WP_Error('rest_forbidden', __('REST API ограничен'), array('status' => 401));
    }
    return $access;
}
add_filter('rest_authentication_errors', 'researched_restrict_rest_api');

// SEO: добавляем canonical URLs
function researched_add_canonical() {
    if (is_singular()) {
        echo '<link rel="canonical" href="' . get_permalink() . '">' . "\n";
    } elseif (is_category()) {
        echo '<link rel="canonical" href="' . get_category_link(get_queried_object_id()) . '">' . "\n";
    } elseif (is_tag()) {
        echo '<link rel="canonical" href="' . get_tag_link(get_queried_object_id()) . '">' . "\n";
    } elseif (is_author()) {
        echo '<link rel="canonical" href="' . get_author_posts_url(get_queried_object_id()) . '">' . "\n";
    }
}
add_action('wp_head', 'researched_add_canonical');

// SEO: добавляем JSON-LD схемы
function researched_add_json_ld_schema() {
    if (is_single()) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => get_the_title(),
            'image' => get_the_post_thumbnail_url($post->ID, 'large'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta('display_name', $post->post_author)
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_site_icon_url()
                )
            ),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'description' => get_the_excerpt(),
            'mainEntityOfPage' => get_permalink()
        );
        
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
    }
    
    // Добавляем BreadcrumbList schema
    if (!is_home() && !is_front_page()) {
        $breadcrumbs = array();
        $breadcrumbs[] = array(
            '@type' => 'ListItem',
            'position' => 1,
            'name' => 'Главная',
            'item' => home_url()
        );
        
        if (is_single()) {
            $breadcrumbs[] = array(
                '@type' => 'ListItem', 
                'position' => 2,
                'name' => 'Блог',
                'item' => home_url()
            );
            $breadcrumbs[] = array(
                '@type' => 'ListItem',
                'position' => 3,
                'name' => get_the_title(),
                'item' => get_permalink()
            );
        }
        
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        );
        
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>' . "\n";
    }
}
add_action('wp_head', 'researched_add_json_ld_schema');

// Производительность: отключаем лишние скрипты и стили
function researched_optimize_scripts() {
    // Отключаем emoji
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    
    // Отключаем Dashicons для неавторизованных пользователей
    if (!is_admin() && !is_user_logged_in()) {
        wp_deregister_style('dashicons');
    }
    
    // Отключаем лишние генераторы
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
}
add_action('init', 'researched_optimize_scripts');

// WebP поддержка
function researched_webp_support() {
    if (function_exists('imagewebp')) {
        add_filter('wp_generate_attachment_metadata', 'researched_generate_webp_images');
    }
}
add_action('init', 'researched_webp_support');

function researched_generate_webp_images($metadata) {
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/' . $metadata['file'];
    
    if (file_exists($file_path)) {
        $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $file_path);
        
        $image_type = wp_check_filetype($file_path)['type'];
        
        switch ($image_type) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($file_path);
                break;
            case 'image/png':
                $image = imagecreatefrompng($file_path);
                break;
            default:
                return $metadata;
        }
        
        if ($image && imagewebp($image, $webp_path, 85)) {
            imagedestroy($image);
        }
    }
    
    return $metadata;
}

// Lazy loading для изображений
function researched_add_lazy_loading($content) {
    if (is_feed() || is_admin()) {
        return $content;
    }
    
    $content = preg_replace('/<img(.*?)src=/i', '<img$1loading="lazy" src=', $content);
    return $content;
}
add_filter('the_content', 'researched_add_lazy_loading');

?>
