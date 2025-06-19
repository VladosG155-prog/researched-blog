<?php
/**
 * Template for 404 error page
 */

get_header(); ?>

<div class="min-h-screen text-white p-4 sm:p-6 md:p-8 relative pb-[200px] sm:pb-[160px] md:pb-[120px] flex items-center justify-center">
    <div class="text-center max-w-2xl mx-auto">
        <!-- 404 Animation/Graphic -->
        <div class="mb-6 sm:mb-8">
            <div class="text-[#D06E31] font-bold text-6xl sm:text-7xl md:text-8xl lg:text-9xl leading-none mb-4" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                404
            </div>
            <div class="w-24 sm:w-32 h-1 bg-gradient-to-r from-[#D06E31] to-[#B85A28] mx-auto "></div>
        </div>

        <!-- Error Message -->
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-4" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
            Страница не найдена
        </h1>
        
        <p class="text-base sm:text-lg text-gray-300 mb-6 sm:mb-8 leading-relaxed px-4 sm:px-0" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
            К сожалению, запрашиваемая вами страница не существует или была перемещена. 
            <span class="hidden sm:inline"><br/></span>
            Возможно, она была удалена или вы ошиблись в адресе.
        </p>

        <!-- Search Form -->
        <div class="mb-8">
            <h3 class="text-xl font-semibold text-gray-200 mb-4" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                Попробуйте найти то, что искали:
            </h3>
            <form role="search" method="get" class="relative max-w-md mx-auto" action="<?php echo esc_url( home_url( '/' ) ); ?>" id="live-search-form">
                <input 
                    type="text" 
                    id="search-input" 
                    placeholder="Поиск по сайту..." 
                    name="s"
                    class="w-full px-4 py-3 pl-10 bg-[#222223]  text-white placeholder-gray-400 focus:outline-none focus:shadow-lg transition-all duration-200" 
                    style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;"
                />
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <button type="submit" class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="h-5 w-5 text-[#D06E31] hover:text-[#B85A28] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Navigation Options -->
        <div class="space-y-4 mb-8">
            <h3 class="text-xl font-semibold text-gray-200" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                Или воспользуйтесь навигацией:
            </h3>
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-center sm:gap-4">
                <a href="<?php echo home_url(); ?>" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-[#D06E31] text-white font-semibold  hover:bg-[#B85A28] transition-all duration-300 transform hover:scale-105 w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    На главную
                </a>
                <a href="<?php echo home_url(); ?>" 
                   class="inline-flex items-center justify-center px-6 py-3 bg-[#222223] text-white font-semibold  hover:bg-[#2a2a2a] hover:shadow-lg transition-all duration-300 w-full sm:w-auto">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd" />
                    </svg>
                    В блог
                </a>
            </div>
        </div>

        <!-- Recent Posts -->
        <div class="mt-12">
            <h3 class="text-xl font-semibold text-gray-200 mb-6" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                Популярные статьи:
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php
                $recent_posts = new WP_Query(array(
                    'post_type' => 'post',
                    'posts_per_page' => 4,
                    'post_status' => 'publish',
                    'orderby' => 'date',
                    'order' => 'DESC'
                ));
                
                if ($recent_posts->have_posts()) :
                    while ($recent_posts->have_posts()) : $recent_posts->the_post(); ?>
                        <a href="<?php echo esc_url(get_permalink()); ?>" 
                           class="block bg-[#222223] p-4  hover:bg-[#2a2a2a] hover:shadow-lg transition-all duration-300 text-left">
                            <h4 class="text-white font-semibold mb-2 line-clamp-2" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                                <?php the_title(); ?>
                            </h4>
                            <p class="text-gray-400 text-sm">
                                <?php echo get_the_date('j F Y'); ?>
                            </p>
                        </a>
                    <?php endwhile;
                    wp_reset_postdata();
                else : ?>
                    <div class="col-span-full text-gray-400 text-center py-8">
                        <p>Статей пока нет</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Help Text -->
        <div class="mt-12 p-6 bg-[#222223] shadow-lg ">
            <h4 class="text-lg font-semibold text-gray-200 mb-3" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                Нужна помощь?
            </h4>
            <p class="text-gray-400 text-sm leading-relaxed">
                Если вы считаете, что попали сюда по ошибке или ссылка должна работать, 
                свяжитесь с нами. Мы поможем вам найти нужную информацию.
            </p>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2;
}
</style>

<?php get_footer(); ?> 