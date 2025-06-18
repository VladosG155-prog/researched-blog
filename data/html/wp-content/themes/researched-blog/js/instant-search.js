jQuery(document).ready(function($) {
    console.log('🚀 Live search script started!');
    
    // Проверяем существование необходимых элементов
    const $searchInput = $('#search-input');
    const $postsGrid = $('#posts-grid');
    const $loading = $('#search-loading');
    const $searchForm = $('#live-search-form');
    
    console.log('Elements found:', {
        searchInput: $searchInput.length,
        postsGrid: $postsGrid.length,
        loading: $loading.length,
        searchForm: $searchForm.length
    });
    
    // Проверяем AJAX параметры
    if (typeof researched_ajax_obj !== 'undefined') {
        console.log('✅ AJAX object found:', researched_ajax_obj);
    } else {
        console.error('❌ AJAX object NOT found');
        return;
    }
    
    // Если элементы не найдены, тихо завершаем (это нормально для страниц без поиска)
    if (!$searchInput.length || !$postsGrid.length || !$searchForm.length) {
        console.log('ℹ️ Live search: Search elements not found on this page (this is normal for single post pages)');
        return;
    }

    let searchTimeout;
    const originalPostsGridContent = $postsGrid.html();
    const searchDelay = 150; // Ускоренный поиск - 150ms
    let isSearching = false; // Флаг для предотвращения множественных запросов

    // Функция для анимации результатов
    function animateResults() {
        $postsGrid.find('article').each(function(index) {
            $(this).css({
                'opacity': '0',
                'transform': 'translateY(20px)'
            }).delay(index * 50).animate({
                'opacity': '1'
            }, 300, function() {
                $(this).css('transform', 'translateY(0)');
            });
        });
    }

    // Функция для инициализации событий на карточках статей
    function initializeArticleEvents() {
        console.log('🔄 Re-initializing article events...');
        
        // Убираем старые обработчики чтобы избежать дублирования
        $postsGrid.off('click', 'article');
        $postsGrid.off('mouseenter mouseleave', 'article');
        
        // Добавляем обработчик клика через делегирование событий
        $postsGrid.on('click', 'article', function(e) {
            // Проверяем что клик был именно на статье, а не на вложенных ссылках
            if (e.target !== this && $(e.target).closest('a').length > 0) {
                return; // Пропускаем клик если это ссылка внутри карточки
            }
            
            e.preventDefault();
            
            // Попробуем извлечь URL из onclick атрибута
            const onclick = $(this).attr('onclick');
            if (onclick) {
                const match = onclick.match(/window\.location\.href='([^']+)'/);
                if (match && match[1]) {
                    console.log('🔗 Navigating to:', match[1]);
                    window.location.href = match[1];
                    return;
                }
            }
            
            // Если onclick не найден, попробуем найти ссылку внутри карточки
            const link = $(this).find('h2 a, .read-more a').first();
            if (link.length > 0) {
                console.log('🔗 Navigating via internal link to:', link.attr('href'));
                window.location.href = link.attr('href');
                return;
            }
            
            console.log('⚠️ No valid link found in article');
        });
        
        // Добавляем hover-эффекты через делегирование событий
        $postsGrid.on('mouseenter', 'article', function() {
            $(this).addClass('hover-active');
        });
        
        $postsGrid.on('mouseleave', 'article', function() {
            $(this).removeClass('hover-active');
        });
        
        console.log('✅ Article events re-initialized');
    }

    // Функция для показа состояния загрузки
    function showLoadingState() {
        $postsGrid.addClass('loading');
        if ($loading.length) {
            $loading.removeClass('hidden');
        }
        isSearching = true;
    }

    // Функция для скрытия состояния загрузки
    function hideLoadingState() {
        $postsGrid.removeClass('loading');
        if ($loading.length) {
            $loading.addClass('hidden');
        }
        isSearching = false;
    }

    function performSearch() {
        const searchQuery = $searchInput.val().trim();
        console.log('🔍 Performing search for:', searchQuery);

        // Если поле пустое, возвращаем оригинальный контент
        if (searchQuery === '') {
            console.log('📝 Empty query, restoring original content');
            $postsGrid.fadeOut(200, function() {
                $(this).html(originalPostsGridContent).fadeIn(200);
                animateResults();
                initializeArticleEvents(); // Реинициализируем события
            });
            hideLoadingState();
            return;
        }

        // Не ищем, если запрос слишком короткий
        if (searchQuery.length < 1) {
            console.log('⏰ Query too short, waiting...');
            return;
        }

        // Предотвращаем множественные запросы
        if (isSearching) {
            console.log('⚠️ Search already in progress, skipping...');
            return;
        }

        console.log('🚀 Starting AJAX search...');
        showLoadingState();

        $.ajax({
            url: researched_ajax_obj.ajax_url,
            type: 'POST',
            data: {
                action: 'researched_live_search',
                nonce: researched_ajax_obj.nonce,
                query: searchQuery
            },
            beforeSend: function() {
                console.log('📤 Sending AJAX request to:', researched_ajax_obj.ajax_url);
            },
            success: function(response) {
                console.log('✅ AJAX success, response length:', response ? response.length : 0);
                $postsGrid.fadeOut(200, function() {
                    if (response && response.trim() !== '') {
                        $(this).html(response).fadeIn(200, function() {
                            animateResults();
                            initializeArticleEvents(); // Реинициализируем события для новых элементов
                        });
                    } else {
                        console.log('⚠️ Empty response from server');
                        $(this).html('<div class="text-center py-12 col-span-full no-results"><h3 class="text-xl font-semibold text-gray-400 mb-2">Статьи не найдены</h3><p class="text-gray-500">Попробуйте изменить запрос.</p></div>').fadeIn(200);
                    }
                });
            },
            error: function(xhr, status, error) {
                console.error('❌ AJAX error:', {
                    error: error,
                    status: status,
                    responseText: xhr.responseText,
                    statusCode: xhr.status
                });
                $postsGrid.fadeOut(200, function() {
                    $(this).html('<div class="text-center py-12 col-span-full no-results"><h3 class="text-xl font-semibold text-red-400 mb-2">Ошибка поиска</h3><p class="text-gray-500">Проверьте консоль браузера для деталей</p></div>').fadeIn(200);
                });
            },
            complete: function() {
                console.log('🏁 AJAX request completed');
                hideLoadingState();
            }
        });
    }

    // Live search на событие ввода с debouncing
    $searchInput.on('input', function() {
        clearTimeout(searchTimeout);
        const currentValue = $(this).val().trim();
        
        // Добавляем визуальную обратную связь
        if (currentValue.length >= 1) {
            $(this).addClass('search-active');
        } else {
            $(this).removeClass('search-active');
        }
        
        searchTimeout = setTimeout(performSearch, searchDelay);
    });

    // Предотвращаем стандартную отправку формы
    $searchForm.on('submit', function(event) {
        event.preventDefault();
        clearTimeout(searchTimeout);
        performSearch();
    });

    // Очистка поиска при потере фокуса на пустом поле
    $searchInput.on('blur', function() {
        const currentValue = $(this).val().trim();
        if (currentValue === '') {
            $(this).removeClass('search-active');
        }
    });

    // Обработка клавиш для лучшего UX
    $searchInput.on('keydown', function(e) {
        // Обработка Escape для очистки поиска
        if (e.key === 'Escape') {
            $(this).val('').trigger('input');
            $(this).blur();
        }
    });

    // Добавляем плавность при изменении размера окна
    $(window).on('resize', function() {
        clearTimeout(searchTimeout);
        if ($searchInput.val().trim().length >= 1) {
            searchTimeout = setTimeout(performSearch, searchDelay);
        }
    });

    // Инициализируем события при первой загрузке страницы
    initializeArticleEvents();

    console.log('Live search initialized successfully');
}); 