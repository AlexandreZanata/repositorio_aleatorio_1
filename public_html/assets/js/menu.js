document.addEventListener('DOMContentLoaded', () => {
  // Elementos do menu mobile
  const mobileMenuToggle = document.getElementById('mobileMenuToggle');
  const drawer = document.getElementById('drawer');
  const drawerBackdrop = document.getElementById('drawerBackdrop');
  const drawerCloseButton = document.getElementById('drawerClose');
  const logoButton = document.getElementById('logoButton'); // Novo elemento para o logo clicável
  
  // Elementos do menu do usuário
  const userMenuToggle = document.getElementById('userMenuToggle');
  const userMenu = document.getElementById('userMenu');
  
  // Toggle do tema escuro
  const darkModeToggle = document.getElementById('darkModeToggle');
  
  // Função para abrir/fechar o menu mobile
  function toggleMobileMenu() {
    const isActive = drawer.classList.contains('active');
    
    if (isActive) {
      mobileMenuToggle.classList.remove('active');
      drawer.classList.remove('active');
      drawerBackdrop.classList.remove('active');
      document.body.style.overflow = '';
    } else {
      mobileMenuToggle.classList.add('active');
      drawer.classList.add('active');
      drawerBackdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }
  
  // Abrir/fechar menu mobile
  if (mobileMenuToggle && drawer && drawerBackdrop) {
    mobileMenuToggle.addEventListener('click', toggleMobileMenu);
    
    // Fechar menu ao clicar no backdrop
    drawerBackdrop.addEventListener('click', toggleMobileMenu);
    
    // Fechar menu ao clicar no botão de fechar
    if (drawerCloseButton) {
      drawerCloseButton.addEventListener('click', toggleMobileMenu);
    }
  }
  
  // Adicionar evento de clique ao logo para abrir a sidebar em mobile
  if (logoButton) {
    logoButton.addEventListener('click', toggleMobileMenu);
  }
  
  // Abrir/fechar menu do usuário
  if (userMenuToggle && userMenu) {
    userMenuToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      userMenu.classList.toggle('active');
    });
    
    // Fechar menu do usuário ao clicar fora
    document.addEventListener('click', (e) => {
      if (!userMenu.contains(e.target) && !userMenuToggle.contains(e.target)) {
        userMenu.classList.remove('active');
      }
    });
  }
  
  // Ativar link baseado na URL atual
  function setActiveLinks() {
    const currentPath = window.location.pathname;
    const navLinks = document.querySelectorAll('.nav-link, .drawer-nav-link, .mobile-bottom-nav-item');
    
    navLinks.forEach(link => {
      const href = link.getAttribute('href');
      if (href && (currentPath === href || currentPath.endsWith(href))) {
        link.classList.add('active');
      }
    });
  }
  
  setActiveLinks();
  
  // Atualizar notificações
  function updateNotifications() {
    const badges = document.querySelectorAll('[data-notifications]');
    badges.forEach(badge => {
      const count = parseInt(badge.dataset.notifications || '0');
      if (count > 0) {
        badge.style.display = 'flex';
        badge.textContent = count > 9 ? '9+' : count;
      } else {
        badge.style.display = 'none';
      }
    });
  }
  
  updateNotifications();
});