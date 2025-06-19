<?php
/*
Template Name: Blog
*/
get_header();

// Удалена дублирующая функция - используем researched_calculate_reading_time() из functions.php

// Function to get related posts
function get_related_posts($post_id, $categories, $tags) {
    $args = array(
        'post__not_in' => array($post_id),
        'posts_per_page' => 3,
        'tax_query' => array(
            'relation' => 'OR',
            array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $categories,
            ),
            array(
                'taxonomy' => 'post_tag',
                'field' => 'term_id',
                'terms' => $tags,
            ),
        ),
    );
    $related_posts = get_posts($args);

    if (count($related_posts) < 3) {
        $remaining = 3 - count($related_posts);
        $recent_args = array(
            'post__not_in' => array_merge(array($post_id), wp_list_pluck($related_posts, 'ID')),
            'posts_per_page' => $remaining,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        $recent_posts = get_posts($recent_args);
        $related_posts = array_merge($related_posts, $recent_posts);
    }

    return $related_posts;
}

// Handle single post view
$slug = isset($_GET['slug']) ? sanitize_text_field($_GET['slug']) : '';
$search_query = get_search_query();

if ($slug) {
    // Single post view (unchanged)
    $args = array(
        'name' => $slug,
        'post_type' => 'post',
        'posts_per_page' => 1,
    );
    $query = new WP_Query($args);
    if ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        $categories = wp_get_post_categories($post_id, array('fields' => 'names'));
        $tags = wp_get_post_tags($post_id, array('fields' => 'names'));
        $reading_time = researched_calculate_reading_time(get_the_content());
        $related_posts = get_related_posts($post_id, wp_get_post_categories($post_id), wp_get_post_tags($post_id, array('fields' => 'ids')));
?>
<div class="min-h-screen text-white p-4 sm:p-6 md:p-8 relative pb-[200px] sm:pb-[160px] md:pb-[120px]">
    <!-- Breadcrumbs -->
    <div class="max-w-[760px] mx-auto mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-400">
            <a href="<?php echo home_url(); ?>" class="hover:text-white transition-colors cursor-pointer" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 14px;">Главная</a>
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <a href="<?php echo home_url('/blog'); ?>" class="hover:text-white transition-colors cursor-pointer" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 14px;">Блог</a>
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-300 line-clamp-1" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 14px;"><?php the_title(); ?></span>
        </nav>
    </div>

    <div class="bg-[#222223] shadow-xl mt-1 mb-12 max-w-5xl mx-auto p-0 md:px-8 md:py-10 ">
        <div class="max-w-[760px] mx-auto px-4 sm:px-0 py-4 antialiased font-normal break-words leading-relaxed" style="color: #f5f5f5; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 17px; font-weight: 400; min-height: 100vh;">
            <h1 class="mb-8 leading-tight no-wrap" style="font-size: 38px; font-weight: 700; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;"><?php the_title(); ?></h1>
            <div class="mb-8 flex flex-wrap gap-4 text-sm text-gray-400">
                <span><?php echo get_the_date('j F Y'); ?></span>
                <span>Время чтения: <?php echo $reading_time; ?> мин</span>
                <?php if ($categories) : ?>
                    <span>Категория: <?php echo implode(', ', $categories); ?></span>
                <?php endif; ?>
            </div>
            <div class="blog-content" style="font-size: 17px; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-weight: 400; line-height: 1.6;">
                <?php the_content(); ?>
            </div>
            <?php if ($tags) : ?>
                <div class="mt-12 pt-8 border-t border-gray-600">
                    <h3 class="text-lg font-semibold mb-4">Теги:</h3>
                    <div class="flex flex-wrap gap-2">
                        <?php foreach ($tags as $tag) : ?>
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 text-sm"><?php echo esc_html($tag); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ($related_posts) : ?>
                <div class="mt-16 pt-8 border-t border-gray-600">
                    <h3 class="text-2xl font-semibold mb-8" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">Похожие статьи</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($related_posts as $related_post) : setup_postdata($related_post); ?>
                            <div class="bg-gray-800 p-4 cursor-pointer hover:bg-gray-700 transition-all duration-300" onclick="window.location.href='<?php echo esc_url(get_permalink($related_post->ID) . '?slug=' . $related_post->post_name); ?>'">
                                <h4 class="text-lg font-semibold mb-2 text-white line-clamp-2"><?php echo esc_html($related_post->post_title); ?></h4>
                                <p class="text-gray-300 text-sm mb-3 line-clamp-2"><?php echo esc_html(wp_trim_words(strip_tags($related_post->post_content), 20)); ?></p>
                                <div class="flex items-center gap-2 text-xs text-gray-400">
                                    <span><?php echo get_the_date('j F Y', $related_post); ?></span>
                                    <span>•</span>
                                    <span><?php echo researched_calculate_reading_time($related_post->post_content); ?> мин</span>
                                </div>
                            </div>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
    } else {
?>
<div class="min-h-screen text-white p-4 sm:p-6 md:p-8 relative pb-[200px] sm:pb-[160px] md:pb-[120px]">
    <div class="bg-[#222223] shadow-xl mt-1 mb-12 max-w-5xl mx-auto p-0 md:px-8 md:py-10">
        <div class="max-w-[760px] mx-auto px-4 sm:px-0 py-4 antialiased font-normal break-words leading-relaxed" style="color: #f5f5f5; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 17px; font-weight: 400; min-height: 100vh;">
            <div class="flex flex-col items-center justify-center min-h-[400px] space-y-4">
                <p class="text-red-400 text-lg">Статья не найдена</p>
                <button onclick="window.location.reload()" class="px-6 py-3 bg-[#D06E31] text-white font-semibold hover:bg-[#B85A28] transition-all">Попробовать еще раз</button>
            </div>
        </div>
    </div>
</div>
<?php
    }
    wp_reset_postdata();
} else {
    // Blog list view
?>
<div class="min-h-screen text-white p-4 sm:p-6 md:p-8 relative pb-[200px] sm:pb-[160px] md:pb-[120px]">
    <!-- Blog Header -->
    <div class="text-center mb-12">
        <h1 class="mb-4 leading-tight text-white" style="font-size: 48px; font-weight: 700; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">Блог researched.xyz</h1>
        <p class="text-xl text-gray-300 max-w-3xl mx-auto">Актуальные материалы о криптовалютах, мультиаккаунтинге, Web3 и заработке в интернете</p>
    </div>

    <!-- Search Form -->
    <div class="max-w-lg mx-auto mb-12">
        <form role="search" method="get" class="relative" action="<?php echo esc_url( home_url( '/' ) ); ?>" id="live-search-form">
            <input 
                type="text" 
                id="search-input" 
                placeholder="Поиск по статьям..." 
                value="<?php echo esc_attr(get_search_query()); ?>" 
                name="s"
                class="w-full px-4 py-3 pl-10 bg-[#222223]  text-white placeholder-gray-400 focus:outline-none focus:shadow-lg transition-all duration-200" 
                style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <div id="search-loading" class="absolute inset-y-0 right-0 pr-3 flex items-center hidden">
                <svg class="animate-spin h-5 w-5 text-[#D06E31]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                </svg>
            </div>
        </form>
    </div>

    <!-- Posts Grid -->
    <div class="max-w-6xl mx-auto">
        <div id="posts-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php
            if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
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
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 2 0 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                                <?php echo researched_calculate_reading_time(get_the_content()); ?> мин
                            </div>
                        </div>
                        <div class="text-gray-300 text-sm leading-relaxed line-clamp-3 mb-4" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                            <?php echo wp_trim_words(strip_tags(get_the_content()), 20); ?>
                        </div>
                        <?php $post_tags = wp_get_post_tags(get_the_ID(), array('fields' => 'names')); if ($post_tags) : ?>
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
            <?php endwhile; else : ?>
                <div class="text-center py-12 col-span-full">
                    <h3 class="text-xl font-semibold text-gray-400 mb-2">Статьи не найдены</h3>
                    <p class="text-gray-500">Попробуйте изменить запрос или вернуться позже</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
}
get_footer();
?>