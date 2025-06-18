<?php
/*
Template Name: Test Formatting
*/

get_header(); ?>

<div class="min-h-screen text-white p-4 sm:p-6 md:p-8 relative pb-[300px]">
    <div class="bg-[#222223] shadow-xl mt-1 mb-8 sm:mb-12 max-w-5xl mx-auto p-0 md:px-8 md:py-10  overflow-hidden">
        <div class="max-w-[760px] mx-auto px-4 sm:px-6 md:px-0 py-6 sm:py-8 md:py-4 antialiased font-normal break-words leading-relaxed" style="color: #f5f5f5; font-family: Inter, 'Helvetica Neue', Helvetica, sans-serif; font-size: 16px; font-weight: 400; min-height: auto;">
            
            <!-- Test Content -->
            <div class="blog-content">
                <h1>Тестирование форматирования контента</h1>
                
                <p>Это тестовая страница для проверки всех элементов форматирования. Здесь мы можем увидеть как работают <strong>жирный текст</strong>, <em>курсив</em>, <a href="https://example.com">ссылки</a>, и другие элементы.</p>
                
                <h2>Заголовок второго уровня</h2>
                
                <p>Обычный параграф с текстом. В нём есть <code>инлайн код</code> и <mark>выделенный текст</mark>.</p>
                
                <h3>Заголовок третьего уровня</h3>
                
                <p>Список элементов:</p>
                <ul>
                    <li>Первый элемент списка</li>
                    <li>Второй элемент с <strong>жирным текстом</strong></li>
                    <li>Третий элемент с <em>курсивом</em></li>
                    <li>Четвёртый элемент со <a href="#">ссылкой</a></li>
                </ul>
                
                <h4>Заголовок четвёртого уровня</h4>
                
                <p>Нумерованный список:</p>
                <ol>
                    <li>Первый пункт</li>
                    <li>Второй пункт</li>
                    <li>Третий пункт</li>
                </ol>
                
                <h5>Заголовок пятого уровня</h5>
                
                <blockquote>
                    <p>Это цитата. Она должна выделяться от основного текста и иметь особое оформление.</p>
                </blockquote>
                
                <h6>Заголовок шестого уровня</h6>
                
                <p>Блок кода:</p>
                <pre><code>function helloWorld() {
    console.log("Hello, World!");
    return "Привет, мир!";
}</code></pre>
                
                <p>Таблица:</p>
                <table>
                    <thead>
                        <tr>
                            <th>Название</th>
                            <th>Тип</th>
                            <th>Статус</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>WordPress</td>
                            <td>CMS</td>
                            <td>Активный</td>
                        </tr>
                        <tr>
                            <td>Tailwind CSS</td>
                            <td>CSS Framework</td>
                            <td>Активный</td>
                        </tr>
                        <tr>
                            <td>ElasticPress</td>
                            <td>Plugin</td>
                            <td>Установлен</td>
                        </tr>
                    </tbody>
                </table>
                
                <hr>
                
                <p>Различные типы текста:</p>
                <ul>
                    <li><strong>Жирный текст</strong></li>
                    <li><em>Курсивный текст</em></li>
                    <li><code>Инлайн код</code></li>
                    <li><mark>Выделенный текст</mark></li>
                    <li><small>Мелкий текст</small></li>
                    <li><kbd>Ctrl</kbd> + <kbd>C</kbd> - клавиши</li>
                    <li>H<sub>2</sub>O - подстрочный индекс</li>
                    <li>E = mc<sup>2</sup> - надстрочный индекс</li>
                </ul>
                
                <p>Ссылки различных типов:</p>
                <ul>
                    <li><a href="https://example.com">Обычная ссылка</a></li>
                    <li><a href="https://example.com" target="_blank">Ссылка в новом окне</a></li>
                    <li><a href="#top">Внутренняя ссылка</a></li>
                </ul>
                
                <p>Это конец тестового контента. Все элементы должны быть правильно отформатированы.</p>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?> 