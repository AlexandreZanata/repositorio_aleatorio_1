/**
 * Sistema de gerenciamento de tema (claro/escuro)
 * Detecta preferências do sistema e permite override manual
 */
class ThemeManager {
  constructor() {
    this.darkModeToggle = document.getElementById('darkModeToggle');
    this.darkModeIcon = document.getElementById('darkModeIcon');
    this.themeKey = 'user-theme-preference';
    
    this.init();
  }
  
  init() {
    this.loadSavedTheme();
    this.setupEventListeners();
    this.updateThemeIcon();
  }
  
  // Carregar tema salvo ou usar preferência do sistema
  loadSavedTheme() {
    const savedTheme = localStorage.getItem(this.themeKey);
    
    if (savedTheme) {
      // Se há tema salvo, aplicá-lo
      this.applyTheme(savedTheme);
    } else {
      // Caso contrário, verificar preferência do sistema
      this.checkSystemPreference();
    }
  }
  
  // Verificar preferência do tema do sistema
  checkSystemPreference() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
      this.applyTheme('dark');
    } else {
      this.applyTheme('light');
    }
    
    // Monitorar alterações na preferência do sistema
    window.matchMedia('(prefers-color-scheme: dark)')
      .addEventListener('change', e => {
        // Somente alterar automaticamente se o usuário não tiver uma preferência salva
        if (!localStorage.getItem(this.themeKey)) {
          this.applyTheme(e.matches ? 'dark' : 'light');
        }
      });
  }
  
  // Configurar os listeners de eventos
  setupEventListeners() {
    if (this.darkModeToggle) {
      this.darkModeToggle.addEventListener('click', () => {
        this.toggleTheme();
      });
    }
  }
  
  // Alternar tema atual
  toggleTheme() {
    if (document.body.classList.contains('dark-theme')) {
      this.applyTheme('light');
    } else {
      this.applyTheme('dark');
    }
    
    // Salvar preferência do usuário
    localStorage.setItem(this.themeKey, this.getCurrentTheme());
    
    // Atualizar ícone
    this.updateThemeIcon();
  }
  
  // Aplicar tema específico (claro ou escuro)
  applyTheme(theme) {
    if (theme === 'dark') {
      document.body.classList.remove('light-theme');
      document.body.classList.add('dark-theme');
    } else {
      document.body.classList.remove('dark-theme');
      document.body.classList.add('light-theme');
    }
  }
  
  // Obter o tema atual
  getCurrentTheme() {
    return document.body.classList.contains('dark-theme') ? 'dark' : 'light';
  }
  
  // Atualizar ícone de acordo com o tema
  updateThemeIcon() {
    if (!this.darkModeIcon) return;
    
    const isDarkTheme = document.body.classList.contains('dark-theme');
    
    if (isDarkTheme) {
      // Ícone para tema escuro (sol)
      this.darkModeIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>`;
    } else {
      // Ícone para tema claro (lua)
      this.darkModeIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"></path></svg>`;
    }
  }
}

// Inicializar o gerenciador de tema quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
  const themeManager = new ThemeManager();
  
  // Expor para uso global
  window.themeManager = themeManager;
});