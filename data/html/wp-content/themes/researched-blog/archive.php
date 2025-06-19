<?php
/**
 * Template for archive pages (categories, tags, dates)
 */

get_header(); ?>

<div class="min-h-screen text-white p-4 sm:p-6 md:p-8 relative pb-[200px] sm:pb-[160px] md:pb-[120px]">
    <!-- Breadcrumbs -->
    <div class="max-w-6xl mx-auto mb-6">
        <nav class="flex items-center space-x-2 text-sm text-gray-400">
            <a href="<?php echo home_url(); ?>" class="hover:text-white transition-colors" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 14px;">Главная</a>
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <a href="<?php echo home_url('/blog'); ?>" class="hover:text-white transition-colors" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 14px;">Блог</a>
            <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
            </svg>
            <span class="text-gray-300" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 14px;">
                <?php
                if (is_category()) {
                    echo 'Категория: ' . single_cat_title('', false);
                } elseif (is_tag()) {
                    echo 'Тег: ' . single_tag_title('', false);
                } elseif (is_date()) {
                    echo 'Архив: ' . get_the_date('F Y');
                } else {
                    echo 'Архив';
                }
                ?>
            </span>
        </nav>
    </div>

    <!-- Archive Header -->
    <div class="text-center mb-12">
        <h1 class="mb-4 leading-tight text-white" style="font-size: 36px; sm:font-size: 42px; font-weight: 700; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
            <?php
            if (is_category()) {
                echo 'Категория: ' . single_cat_title('', false);
            } elseif (is_tag()) {
                echo 'Тег: ' . single_tag_title('', false);
            } elseif (is_date()) {
                echo 'Архив за ' . get_the_date('F Y');
            } else {
                echo 'Архив блога researched.xyz';
            }
            ?>
        </h1>
        <?php if (category_description() || tag_description()) : ?>
            <div class="text-base sm:text-lg text-gray-300 max-w-3xl mx-auto leading-relaxed" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                <?php echo category_description() . tag_description(); ?>
            </div>
        <?php else : ?>
            <p class="text-base sm:text-lg text-gray-300 max-w-3xl mx-auto leading-relaxed" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                <?php
                if (is_category()) {
                    echo 'Все статьи из категории "' . single_cat_title('', false) . '"';
                } elseif (is_tag()) {
                    echo 'Все статьи с тегом "' . single_tag_title('', false) . '"';
                } elseif (is_date()) {
                    echo 'Статьи опубликованные в ' . get_the_date('F Y');
                } else {
                    echo 'Архив всех статей блога researched.xyz';
                }
                ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Posts Grid -->
    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
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
            <?php endwhile; ?>
            
            <!-- Pagination -->
            <div class="col-span-full mt-12">
                <div class="flex justify-center">
                    <?php
                    $big = 999999999;
                    echo paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $wp_query->max_num_pages,
                        'prev_text' => '← Предыдущая',
                        'next_text' => 'Следующая →',
                        'mid_size' => 2,
                        'type' => 'list'
                    ));
                    ?>
                </div>
            </div>
            
            <?php else : ?>
                <div class="text-center py-12 col-span-full">
                    <h3 class="text-xl font-semibold text-gray-400 mb-2">Статьи не найдены</h3>
                    <p class="text-gray-500">В данной категории/теге пока нет публикаций</p>
                    <a href="<?php echo home_url('/blog'); ?>" class="inline-block mt-4 px-6 py-3 bg-[#D06E31] text-white font-semibold  hover:bg-[#B85A28] transition-colors">
                        Вернуться к блогу
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php get_footer(); ?> 