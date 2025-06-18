<?php
/**
 * Template Name: Blog Home
 * Template for blog homepage
 */

get_header(); ?>

<div class="min-h-screen text-white p-4 sm:p-6 md:p-8 relative pb-[200px] sm:pb-[160px] md:pb-[120px]">
    <!-- Blog Header -->
    <div class="text-center mb-12">
        <h1 class="mb-4 leading-tight text-white" style="font-size: 42px; sm:font-size: 48px; font-weight: 700; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
            Блог researched.xyz
        </h1>
        <p class="text-lg sm:text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
            Экспертные статьи, кейсы и аналитика от команды researched.xyz <br class="hidden sm:block"/>
            Полезная информация о Web3, крипте, мультиаккаунтинге и не только.
        </p>
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
            $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
            $args = array(
                'post_type' => 'post',
                'posts_per_page' => 9,
                'paged' => $paged,
                'post_status' => 'publish'
            );
            $blog_query = new WP_Query($args);
            
            if ($blog_query->have_posts()) : while ($blog_query->have_posts()) : $blog_query->the_post(); ?>
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
                                <?php echo researched_calculate_reading_time(get_the_content()); ?> мин
                            </div>
                        </div>
                        <div class="text-gray-300 text-sm leading-relaxed line-clamp-3 mb-4" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                            <?php echo wp_trim_words(strip_tags(get_the_content()), 20); ?>
                        </div>
                        

                        
                        <!-- Tags -->
                        <?php $post_tags = wp_get_post_tags(get_the_ID()); if ($post_tags) : ?>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach (array_slice($post_tags, 0, 3) as $tag) : ?>
                                    <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="inline-flex items-center px-2.5 py-0.5  text-xs font-medium bg-[#D06E31] bg-opacity-20 text-[#D06E31] hover:bg-opacity-30 hover:scale-105 hover:shadow-md transition-all duration-200" onclick="event.stopPropagation();">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                                        </svg>
                                        <?php echo esc_html($tag->name); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endwhile; 
            
            // Pagination
            if ($blog_query->max_num_pages > 1) : ?>
                <div class="col-span-full mt-12">
                    <div class="flex justify-center">
                        <?php
                        echo paginate_links(array(
                            'total' => $blog_query->max_num_pages,
                            'current' => $paged,
                            'prev_text' => '← Предыдущая',
                            'next_text' => 'Следующая →',
                            'mid_size' => 2,
                            'type' => 'list'
                        ));
                        ?>
                    </div>
                </div>
            <?php endif;
            
            else : ?>
                <div class="text-center py-12 col-span-full">
                    <h3 class="text-xl font-semibold text-gray-400 mb-2">Постов пока нет</h3>
                    <p class="text-gray-500">Скоро здесь появятся интересные статьи</p>
                </div>
            <?php endif;
            wp_reset_postdata(); ?>
        </div>
    </div>
</div>

<?php get_footer(); ?> 