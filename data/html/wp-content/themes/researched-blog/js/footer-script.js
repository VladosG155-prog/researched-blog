const lucideIcons = {
    wifi: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M5 12.55a11 11 0 0 1 14.08 0M8.53 16.11a6 6 0 0 1 6.95 0M12 20h.01"/></svg>`,
    shield: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>`,
    network: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="3"/><path d="M12 2v2m0 16v2m10-10h-2M4 12H2m15.07 7.07l-1.41-1.41M6.34 6.34l-1.41-1.41m0 13.48 1.41-1.41m13.48-13.48-1.41 1.41"/></svg>`,
    'dollar-sign': `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7H14.5a3.5 3.5 0 0 1 0 7H6"/></svg>`,
    'shopping-cart': `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>`,
    'trending-up': `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>`,
    wallet: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M20 12a2 2 0 0 0 0-4H4V6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V10"/><path d="M18 12h.01"/></svg>`,
    bot: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M12 11V5M9 3h6"/><circle cx="8" cy="16" r="1"/><circle cx="16" cy="16" r="1"/></svg>`,
    briefcase: `<svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 3h-8v4h8V3z"/></svg>`,
    x: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>`,
    chevronRight: `<svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mt-1 md:w-6 md:h-6 md:mt-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><polyline points="9 18 15 12 9 6"/></svg>`
  };

  const categories = {
    main: [
      { name: 'Прокси', icon: 'wifi', href: '/proxy-static' },
      { name: 'Анти-детекты', icon: 'shield', href: '/antidetect' },
      { name: 'DePIN прокси', icon: 'network', href: '/proxy-depin', image: '/grasstobutton.webp' },
      { name: 'Комиссии CEX', icon: 'dollar-sign', href: 'https://t.me/researchedxyz_bot', external: true }
    ],
    expanded: [
      { name: 'Аккаунт шопы', icon: 'shopping-cart', href: '/shops' },
      { name: 'CEX', icon: 'trending-up', href: '/cex' },
      { name: 'Трейдинг боты', icon: 'bot', href: '/tradingbots' },
      { name: 'OTC', icon: 'briefcase', href: '/otc' },
      { name: 'Кошельки', icon: 'wallet', href: '/wallets' }
    ]
  };

  const footer = document.getElementById('footer');
  const mainWrapper = document.getElementById('mainCategories');
  const expandedWrapper = document.getElementById('expandedWrapper');
  const expandedGrid = document.getElementById('expandedCategories');
  let expanded = false;

  const createCategoryButton = (cat) => {
    const el = document.createElement('a');
    el.href = cat.href || '#';
    if (cat.external) el.target = '_blank';
    el.className = `relative cursor-pointer flex flex-col items-center justify-center px-2 bg-[#2C2C2C] hover:bg-[#444242] transition-colors text-white overflow-hidden text-[12px] sm:text-sm sm:text-md`;
    el.innerHTML = `
      <div class="flex flex-col items-center justify-center h-full">
        <span class="text-center">${cat.name}</span>
        <div>${lucideIcons[cat.icon] || ''}</div>
      </div>
      ${cat.image ? `<img src="${cat.image}" alt="DePin" class="absolute bottom-0 left-0 w-full object-cover h-[12px] md:h-[20px]" />` : ''}
    `;
    return el;
  };

  const createExpandButton = () => {
    const btn = document.createElement('button');
    btn.className = `flex flex-col cursor-pointer items-center justify-center px-2 bg-[#2C2C2C] hover:bg-[#444242] transition-colors text-white text-[12px] sm:text-sm sm:text-md`;
    btn.innerHTML = `
      <span>${expanded ? 'Закрыть' : 'Другое'}</span>
      ${expanded ? lucideIcons.x : lucideIcons.chevronRight}
    `;
    btn.onclick = () => {
      expanded = !expanded;
      renderCategories();
    };
    return btn;
  };

  const renderCategories = () => {
    mainWrapper.innerHTML = '';
    categories.main.forEach(cat => mainWrapper.appendChild(createCategoryButton(cat)));
    mainWrapper.appendChild(createExpandButton());

    expandedGrid.innerHTML = '';
    if (expanded) {
      categories.expanded.forEach(cat => expandedGrid.appendChild(createCategoryButton(cat)));
      expandedWrapper.style.height = 'auto';
      expandedWrapper.style.opacity = '1';
    } else {
      expandedWrapper.style.height = '0';
      expandedWrapper.style.opacity = '0';
    }
  };

  window.addEventListener('load', () => {
    renderCategories();
    setTimeout(() => {
      footer.classList.remove('opacity-0');
      footer.classList.add('opacity-100');
      footer.style.pointerEvents = 'auto';
    }, 100);
  }); 