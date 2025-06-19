<?php
/*
 * Template for displaying a single blog post, matching BlogClient.tsx single post view
 */
get_header();

// Удалена дублирующая функция - используем researched_calculate_reading_time() из functions.php

// Advanced function to get truly related posts
function get_related_posts($post_id, $categories, $tags) {
    $related_posts = array();
    $current_post = get_post($post_id);
    $current_title = $current_post->post_title;
    
    // Step 1: Find posts with BOTH matching categories AND tags (highest relevance)
    if (!empty($categories) && !empty($tags)) {
        $step1_args = array(
            'post__not_in' => array($post_id),
            'posts_per_page' => 3,
            'tax_query' => array(
                'relation' => 'AND',
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
        $step1_posts = get_posts($step1_args);
        $related_posts = array_merge($related_posts, $step1_posts);
    }
    
    // Step 2: If still need more, find posts with matching categories OR tags
    if (count($related_posts) < 3) {
        $exclude_ids = array_merge(array($post_id), wp_list_pluck($related_posts, 'ID'));
        $remaining = 3 - count($related_posts);
        
        $step2_args = array(
            'post__not_in' => $exclude_ids,
            'posts_per_page' => $remaining,
            'tax_query' => array(
                'relation' => 'OR',
            ),
        );
        
        // Add category query if categories exist
        if (!empty($categories)) {
            $step2_args['tax_query'][] = array(
                'taxonomy' => 'category',
                'field' => 'term_id',
                'terms' => $categories,
            );
        }
        
        // Add tag query if tags exist
        if (!empty($tags)) {
            $step2_args['tax_query'][] = array(
                'taxonomy' => 'post_tag',
                'field' => 'term_id',
                'terms' => $tags,
            );
        }
        
        $step2_posts = get_posts($step2_args);
        $related_posts = array_merge($related_posts, $step2_posts);
    }
    
    // Step 3: If still need more, search by similar title words
    if (count($related_posts) < 3) {
        $exclude_ids = array_merge(array($post_id), wp_list_pluck($related_posts, 'ID'));
        $remaining = 3 - count($related_posts);
        
        // Extract meaningful words from title (remove common words)
        $stop_words = array('и', 'в', 'на', 'с', 'по', 'для', 'как', 'что', 'это', 'или', 'но', 'а', 'да', 'нет', 'не', 'о', 'об', 'к', 'от', 'из', 'при', 'до', 'после', 'через', 'под', 'над', 'между', 'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by', 'from', 'how', 'what', 'why', 'when', 'where');
        $title_words = preg_split('/\s+/', mb_strtolower($current_title));
        $meaningful_words = array_diff($title_words, $stop_words);
        $meaningful_words = array_filter($meaningful_words, function($word) {
            return mb_strlen($word) > 3; // Only words longer than 3 characters
        });
        
        if (!empty($meaningful_words)) {
            // Create search query with title words
            $search_terms = implode(' ', array_slice($meaningful_words, 0, 3)); // Use up to 3 most meaningful words
            
            $step3_args = array(
                'post__not_in' => $exclude_ids,
                'posts_per_page' => $remaining,
                's' => $search_terms,
                'orderby' => 'relevance',
            );
            
            $step3_posts = get_posts($step3_args);
            $related_posts = array_merge($related_posts, $step3_posts);
        }
    }
    
    // Step 4: If still need more, get most popular posts (by comment count)
    if (count($related_posts) < 3) {
        $exclude_ids = array_merge(array($post_id), wp_list_pluck($related_posts, 'ID'));
        $remaining = 3 - count($related_posts);
        
        $step4_args = array(
            'post__not_in' => $exclude_ids,
            'posts_per_page' => $remaining,
            'orderby' => 'comment_count',
            'order' => 'DESC',
            'meta_query' => array(
                array(
                    'key' => '_wp_old_slug',
                    'compare' => 'NOT EXISTS', // Exclude redirects
                ),
            ),
        );
        
        $step4_posts = get_posts($step4_args);
        $related_posts = array_merge($related_posts, $step4_posts);
    }
    
    // Step 5: Last resort - most recent posts
    if (count($related_posts) < 3) {
        $exclude_ids = array_merge(array($post_id), wp_list_pluck($related_posts, 'ID'));
        $remaining = 3 - count($related_posts);
        
        $step5_args = array(
            'post__not_in' => $exclude_ids,
            'posts_per_page' => $remaining,
            'orderby' => 'date',
            'order' => 'DESC',
        );
        
        $step5_posts = get_posts($step5_args);
        $related_posts = array_merge($related_posts, $step5_posts);
    }
    
    // Ensure we have unique posts and limit to 3
    $unique_posts = array();
    $seen_ids = array();
    
    foreach ($related_posts as $post) {
        if (!in_array($post->ID, $seen_ids) && count($unique_posts) < 3) {
            $unique_posts[] = $post;
            $seen_ids[] = $post->ID;
        }
    }
    
    return $unique_posts;
}

if (have_posts()) : while (have_posts()) : the_post();
    $post_id = get_the_ID();
    $categories = wp_get_post_categories($post_id, array('fields' => 'names'));
    $tags = wp_get_post_tags($post_id, array('fields' => 'names'));
    $reading_time = researched_calculate_reading_time(get_the_content());
?>
<div class="min-h-screen text-white p-4 sm:p-6 md:pt-4 md:px-8 md:pb-8 relative pb-[200px] sm:pb-[160px] md:pb-[120px]" style="background-color: #121212;">
    <!-- Breadcrumbs -->
    <div class="max-w-[760px] mx-auto mb-1 sm:mb-2 md:mb-1 px-4 sm:px-0">
        <nav class="flex items-center space-x-1 sm:space-x-2 text-xs sm:text-sm text-gray-400 overflow-x-auto whitespace-nowrap">
            <a href="<?php echo home_url(); ?>" class="hover:text-white transition-colors cursor-pointer flex-shrink-0" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                <span class="hidden sm:inline">Главная</span>
                <svg class="w-4 h-4 sm:hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                </svg>
            </a>
            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <a href="<?php echo home_url(); ?>" class="hover:text-white transition-colors cursor-pointer flex-shrink-0" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">Блог</a>
            <svg class="w-3 h-3 sm:w-4 sm:h-4 text-gray-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-300 truncate max-w-[200px] sm:max-w-none" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;" title="<?php the_title(); ?>"><?php the_title(); ?></span>
        </nav>
    </div>

    <div class="bg-[#121212] shadow-xl mt-0 mb-8 sm:mb-12 max-w-5xl mx-auto p-0 md:px-8 md:py-6 overflow-hidden">
        <div class="max-w-[760px] mx-auto px-4 sm:px-6 md:px-0 py-4 sm:py-6 md:py-2 antialiased font-normal break-words leading-relaxed" style="color: #f5f5f5; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 16px; font-weight: 400; min-height: auto;">
            <!-- Post Title -->
            <h1 class="mb-3 sm:mb-4 md:mb-3 leading-tight" style="font-size: 28px; sm:font-size: 32px; md:font-size: 38px; font-weight: 700; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;"><?php the_title(); ?></h1>

            <!-- Meta Information -->
            <div class="mb-4 sm:mb-5 flex flex-wrap gap-3 sm:gap-4 text-sm text-gray-400">
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                    </svg>
                    <span><?php echo get_the_date('j F Y'); ?></span>
                </div>
                <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                    </svg>
                    <span><?php echo $reading_time; ?> мин чтения</span>
                </div>

            </div>

            <!-- Featured Image -->
            <?php if (has_post_thumbnail()) : ?>
                <div class="mb-6 sm:mb-8">
                    <?php 
                    $thumbnail_id = get_post_thumbnail_id();
                    $thumbnail_alt = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                    $thumbnail_caption = wp_get_attachment_caption($thumbnail_id);
                    ?>
                    <figure class=" overflow-hidden">
                        <?php the_post_thumbnail('large', array(
                            'class' => 'w-full h-auto  object-cover',
                            'style' => 'max-width: 100%; height: auto;',
                            'alt' => $thumbnail_alt ? $thumbnail_alt : get_the_title()
                        )); ?>
                        <?php if ($thumbnail_caption) : ?>
                            <figcaption class="mt-2 text-sm text-gray-400 text-center italic" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                                <?php echo esc_html($thumbnail_caption); ?>
                            </figcaption>
                        <?php endif; ?>
                    </figure>
                </div>
            <?php endif; ?>

            <!-- Post Content -->
            <div class="blog-content" style="font-size: 16px; sm:font-size: 17px; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-weight: 400; line-height: 1.7;">
                <?php the_content(); ?>
            </div>

            <!-- Tags -->
            <?php 
            $post_tags = wp_get_post_tags(get_the_ID()); 
            if ($post_tags) : 
            ?>
                <div class="mt-4 pt-3 border-t border-gray-600">
                    <div class="flex items-center gap-2 mb-2">
                        <svg class="w-4 h-4 text-[#D06E31]" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium text-gray-400" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">Теги:</span>
                    </div>
                    <div class="flex flex-wrap gap-1.5">
                        <?php foreach ($post_tags as $tag) : ?>
                            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="inline-flex items-center px-2.5 py-1  text-xs font-medium bg-[#D06E31] bg-opacity-15 text-[#D06E31] hover:bg-opacity-25 hover:shadow-md transition-all duration-200 cursor-pointer">
                                <svg class="w-2.5 h-2.5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                </svg>
                                <?php echo esc_html($tag->name); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Related Posts -->
            <?php
            // Кэширование результатов для производительности
            $cache_key = 'related_posts_' . $post_id;
            $related_posts = wp_cache_get($cache_key);
            
            if (false === $related_posts) {
                $related_posts = get_related_posts($post_id, wp_get_post_categories($post_id), wp_get_post_tags($post_id, array('fields' => 'ids')));
                wp_cache_set($cache_key, $related_posts, '', 3600); // Cache for 1 hour
            }
            
            if ($related_posts) :
            ?>
                <div class="mt-6 pt-4 border-t border-gray-600">
                    <div class="flex items-center gap-3 mb-4">
                        <h3 class="text-2xl font-semibold" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">Похожие статьи</h3>
                        <div class="flex items-center gap-1 px-3 py-1 bg-[#D06E31] bg-opacity-20 ">
                            <svg class="w-4 h-4 text-[#D06E31]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-xs text-[#D06E31] font-medium">Умный подбор</span>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($related_posts as $related_post) : setup_postdata($related_post); ?>
                            <article class="bg-[#222223] shadow-lg hover:shadow-2xl transition-all duration-300 cursor-pointer transform hover:scale-[1.02] hover:-translate-y-1  overflow-hidden group" onclick="window.location.href='<?php echo esc_url(get_permalink($related_post->ID)); ?>'">
                                <!-- Header with image -->
                                <div class="h-32 sm:h-36 relative overflow-hidden">
                                    <?php if (has_post_thumbnail($related_post->ID)) : ?>
                                        <?php echo get_the_post_thumbnail($related_post->ID, 'medium_large', array(
                                            'class' => 'w-full h-full object-cover',
                                            'alt' => esc_attr($related_post->post_title)
                                        )); ?>
                                        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                    <?php else : ?>
                                        <img src="<?php echo get_template_directory_uri(); ?>/assets/cooltmp.webp" alt="<?php echo esc_attr($related_post->post_title); ?>" class="w-full h-full object-cover" />
                                        <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                    <?php endif; ?>
                                    <div class="absolute bottom-3 left-4 right-4">
                                        <h4 class="text-white font-bold text-sm sm:text-base leading-tight line-clamp-2 group-hover:text-orange-100 transition-colors" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                                            <?php echo esc_html($related_post->post_title); ?>
                                        </h4>
                                    </div>
                                    <!-- Reading indicator -->
                                    <div class="absolute top-3 right-3">
                                        <span class="bg-black/30 backdrop-blur-sm text-white text-xs px-2 py-1 ">
                                            <?php echo researched_calculate_reading_time($related_post->post_content); ?> мин
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="p-4 sm:p-5">
                                    <!-- Excerpt -->
                                    <p class="text-gray-300 text-sm leading-relaxed mb-4 line-clamp-3 group-hover:text-gray-200 transition-colors" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                                        <?php echo esc_html(wp_trim_words(strip_tags($related_post->post_content), 25)); ?>
                                    </p>
                                    
                                    <!-- Meta info -->
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2 text-xs text-gray-400">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd" />
                                            </svg>
                                            <span><?php echo get_the_date('j M Y', $related_post); ?></span>
                                        </div>
                                        
                                        <!-- Read more arrow -->
                                        <div class="flex items-center text-[#D06E31] group-hover:text-[#ff7a3d] transition-colors">
                                            <span class="text-xs font-medium mr-1">Читать</span>
                                            <svg class="w-3 h-3 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </div>
                                    </div>
                                    
                                    <!-- Tags -->
                                    <?php 
                                    $post_tags = wp_get_post_tags($related_post->ID, array('fields' => 'names')); 
                                    if ($post_tags) : 
                                    ?>
                                        <div class="flex flex-wrap gap-1 mt-3">
                                            <?php foreach (array_slice($post_tags, 0, 2) as $tag) : ?>
                                                <span class="inline-flex items-center px-2 py-0.5  text-xs font-medium bg-[#D06E31] bg-opacity-15 text-[#D06E31]">
                                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                                    </svg>
                                                    <?php echo esc_html($tag); ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </article>
                        <?php endforeach; wp_reset_postdata(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php
endwhile; endif;
wp_reset_postdata();

get_footer();
?>