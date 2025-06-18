<article
    class="bg-[#222223] shadow-lg hover:shadow-xl transition-all duration-300 cursor-pointer transform hover:scale-105  overflow-hidden"
    onclick="window.location.href='<?php echo esc_url(get_permalink()); ?>'"
>
    <!-- Обложка -->
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
            <h2 class="text-white font-bold text-lg leading-tight line-clamp-2" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
                <?php the_title(); ?>
            </h2>
        </div>
    </div>

    <!-- Контент карточки -->
    <div class="p-6">
        <!-- Мета-информация -->
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

        <!-- Описание -->
        <div class="text-gray-300 text-sm leading-relaxed line-clamp-3 mb-4" style="font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif;">
            <?php echo wp_trim_words(get_the_excerpt(), 20); ?>
        </div>

        <!-- Теги -->
        <?php
        $tags = get_the_tags();
        if ($tags) :
            ?>
            <div class="flex flex-wrap gap-2">
                <?php foreach (array_slice($tags, 0, 3) as $tag) : ?>
                    <span class="inline-flex items-center px-2.5 py-0.5  text-xs font-medium bg-[#D06E31] bg-opacity-20 text-[#D06E31]">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <?php echo $tag->name; ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</article>