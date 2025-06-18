<!DOCTYPE html>
<html lang="ru" style="background-color: #121212;">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Описание страницы" />
  <meta name="keywords" content="example, tags, here" />
  <title>researched.xyz</title>
  
  <!-- Предотвращаем мигание белым -->
  <style>
    html { background-color: #121212 !important; }
    body { 
      font-family: 'Inter', sans-serif;
      background-color: #121212 !important;
      margin: 0;
      padding: 0;
    }
    .modal {
      background-color: rgba(0, 0, 0, 0.6);
    }
    /* Скрываем контент до полной загрузки */
    .content-loading {
      opacity: 0;
      transition: opacity 0.2s ease-in;
    }
    .content-loaded {
      opacity: 1;
    }
  </style>
  
  <?php wp_head(); ?>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700&display=swap" rel="stylesheet" />
</head>
<body class="text-white content-loading" style="background-color: #121212;">

<script>
// Убираем мигание при загрузке
document.addEventListener('DOMContentLoaded', function() {
  document.body.classList.remove('content-loading');
  document.body.classList.add('content-loaded');
});
</script>

  <!-- HEADER -->
  <header class="w-full max-w-[1260px] mx-auto flex items-center z-[60] justify-between h-[70px] px-4 pl-0">
    <!-- Title / Home Button -->
    <button id="iconBurstButton" class="text-lg sm:text-xl font-semibold text-neutral-300 hover:text-white transition-colors h-full">
      researched.xyz
    </button>

    <!-- Desktop Navigation -->
    <div class="hidden md:flex items-center h-full gap-4">
      <a href="/blog" class="text-white hover:text-neutral-300 transition-colors text-[18px] px-4">Блог</a>
      <button id="openModal" class="h-[70px] px-6 flex items-center justify-center bg-[#D9D7D5] text-black hover:bg-[#C9C7C5] transition-colors text-[18px]">
        Зачем мы тебе?
      </button>
      <a href="https://t.me/researchedxyz_bot" target="_blank" rel="noopener noreferrer" class="h-[70px] px-6 flex items-center justify-center bg-[#2C2C2C] hover:bg-[#3C3C3C] transition-colors text-white text-[18px]">
        <!-- Telegram Icon -->
        <svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10.6 14.16L15.47 9.31M20.43 6.36L16.34 19.65C15.97 20.84 15.79 21.44 15.47 21.64C15.2 21.81 14.86 21.84 14.56 21.71C14.22 21.57 13.94 21.01 13.38 19.9L10.79 14.71C10.7 14.54 10.66 14.45 10.6 14.37C10.54 14.3 10.48 14.24 10.42 14.19C10.34 14.13 10.26 14.09 10.09 14L4.89 11.41C3.77 10.85 3.22 10.57 3.08 10.23C2.95 9.93 2.98 9.59 3.15 9.31C3.35 8.99 3.95 8.81 5.14 8.45L18.43 4.36C19.37 4.07 19.84 3.92 20.15 4.04C20.43 4.14 20.65 4.36 20.75 4.63C20.86 4.95 20.72 5.42 20.43 6.36Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
    </div>

    <!-- Mobile Navigation -->
    <div class="flex md:hidden items-center h-full gap-2">
      <a href="/blog" class="h-[40px] px-3 flex items-center justify-center text-white hover:text-neutral-300 transition-colors text-[14px]">Блог</a>
      <button id="openModalMobile" class="h-[40px] w-[40px] flex items-center justify-center bg-[#D9D7D5] text-black hover:bg-[#C9C7C5] transition-colors" title="Зачем мы тебе?">?</button>
      <a href="https://t.me/researchedxyz_bot" target="_blank" rel="noopener noreferrer" class="h-[40px] w-[40px] flex items-center justify-center bg-[#2C2C2C] hover:bg-[#3C3C3C] transition-colors" title="Telegram">
        <!-- same icon as above -->
        <svg width="24" height="24" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M10.6 14.16L15.47 9.31M20.43 6.36L16.34 19.65C15.97 20.84 15.79 21.44 15.47 21.64C15.2 21.81 14.86 21.84 14.56 21.71C14.22 21.57 13.94 21.01 13.38 19.9L10.79 14.71C10.7 14.54 10.66 14.45 10.6 14.37C10.54 14.3 10.48 14.24 10.42 14.19C10.34 14.13 10.26 14.09 10.09 14L4.89 11.41C3.77 10.85 3.22 10.57 3.08 10.23C2.95 9.93 2.98 9.59 3.15 9.31C3.35 8.99 3.95 8.81 5.14 8.45L18.43 4.36C19.37 4.07 19.84 3.92 20.15 4.04C20.43 4.14 20.65 4.36 20.75 4.63C20.86 4.95 20.72 5.42 20.43 6.36Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
    </div>
  </header>

  <!-- Modal -->
  <div id="modal" class="modal fixed inset-0 z-50 hidden items-center justify-center">
    <div class="bg-white text-black p-6 rounded shadow-lg max-w-md w-full text-center">
      <p class="text-lg mb-4">Вот зачем мы тебе... (сюда можно вставить контент)</p>
      <button id="closeModal" class="mt-4 px-4 py-2 bg-gray-800 text-white hover:bg-gray-700 transition">Закрыть</button>
    </div>
  </div>

  <script>
    // Modal Logic
    const modal = document.getElementById('modal');
    document.getElementById('openModal').addEventListener('click', () => modal.classList.remove('hidden'));
    document.getElementById('openModalMobile').addEventListener('click', () => modal.classList.remove('hidden'));
    document.getElementById('closeModal').addEventListener('click', () => modal.classList.add('hidden'));

    // Icon Burst
    const iconSources = [
      'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/handpie-krMyEJUWwsr5fjT1AB8oPllk03Kil9.webp',
      'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/green-heart-cVae3IoBP0nzLUn04gX8FOdY0pFZzn.webp'
    ];
    const preloadedIcons = iconSources.map(src => {
      const img = new Image();
      img.src = src;
      return img;
    });

    document.getElementById('iconBurstButton').addEventListener('click', (event) => {
      const centerX = event.clientX;
      const centerY = event.clientY;
      const iconsCount = 6;
      const fragment = document.createDocumentFragment();

      for (let i = 0; i < iconsCount; i++) {
        const icon = document.createElement('img');
        const isHeart = Math.random() > 0.5;
        icon.src = isHeart ? preloadedIcons[1].src : preloadedIcons[0].src;
        const size = isHeart ? 16 : 24;
        icon.style.position = 'fixed';
        icon.style.width = `${size}px`;
        icon.style.height = `${size}px`;
        icon.style.left = `${centerX}px`;
        icon.style.top = `${centerY}px`;
        icon.style.transform = 'translate(-50%, -50%)';
        icon.style.pointerEvents = 'none';
        icon.style.zIndex = '1000';
        fragment.appendChild(icon);

        // Animate
        requestAnimationFrame(() => {
          document.body.appendChild(icon);
          const angle = Math.random() * 360;
          const distance = 30 + Math.random() * 60;
          const duration = 1200 + Math.random() * 400;

          icon.style.transition = `transform ${duration}ms ease-out, opacity ${duration * 0.6}ms`;
          setTimeout(() => {
            icon.style.transform = `translate(calc(-50% + ${Math.cos(angle * Math.PI / 180) * distance}px), calc(-50% + ${Math.sin(angle * Math.PI / 180) * distance}px)) rotate(${Math.random() * 180 - 90}deg) scale(0.8)`;
            icon.style.opacity = '0';
          }, 20);
          setTimeout(() => icon.remove(), duration + 100);
        });
      }
    });
  </script>
