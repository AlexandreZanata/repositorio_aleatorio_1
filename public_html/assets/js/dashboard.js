/**
 * Biblioteca para renderização de gráficos do dashboard
 * Utiliza Chart.js internamente
 */
class DashboardCharts {
  constructor() {
    this.charts = {};
    this.loadChartJS();
  }
  
  // Carregar Chart.js dinamicamente
  loadChartJS() {
    if (typeof Chart !== 'undefined') {
      this.initCharts();
      return;
    }
    
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
    script.onload = () => this.initCharts();
    document.head.appendChild(script);
  }
  
  // Inicializar todos os gráficos
  initCharts() {
    this.setupChartDefaults();
    this.renderTripChart();
    this.renderFuelChart();
  }
  
  // Configurar padrões para todos os gráficos
  setupChartDefaults() {
    if (typeof Chart === 'undefined') return;
    
    Chart.defaults.font.family = getComputedStyle(document.body).getPropertyValue('--font-sans') || 'Inter, sans-serif';
    Chart.defaults.color = getComputedStyle(document.body).getPropertyValue('--chart-text');
    Chart.defaults.borderColor = getComputedStyle(document.body).getPropertyValue('--chart-grid');
    
    // Observar mudanças de tema
    const observer = new MutationObserver((mutations) => {
      mutations.forEach((mutation) => {
        if (
          mutation.type === 'attributes' &&
          mutation.attributeName === 'class' &&
          (mutation.target.classList.contains('dark-theme') || mutation.target.classList.contains('light-theme'))
        ) {
          this.updateChartsTheme();
        }
      });
    });
    
    observer.observe(document.body, { attributes: true });
  }
  
  // Atualizar tema dos gráficos quando o tema do site mudar
  updateChartsTheme() {
    Chart.defaults.color = getComputedStyle(document.body).getPropertyValue('--chart-text');
    Chart.defaults.borderColor = getComputedStyle(document.body).getPropertyValue('--chart-grid');
    
    // Atualizar cada gráfico
    Object.values(this.charts).forEach(chart => {
      if (chart && chart.update) {
        chart.update();
      }
    });
  }
  
  // Renderizar gráfico de corridas (com cores ajustadas para Power BI)
  renderTripChart() {
    const ctx = document.getElementById('tripsChart');
    if (!ctx || typeof Chart === 'undefined') return;
    
    const primaryColor = getComputedStyle(document.body).getPropertyValue('--chart-line-1');
    const bgColor = getComputedStyle(document.body).getPropertyValue('--chart-bg-1');
    
    this.charts.trips = new Chart(ctx, {
      type: 'line',
      data: {
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago'],
        datasets: [{
          label: 'Corridas Realizadas',
          data: [65, 59, 80, 81, 56, 55, 72, 90],
          fill: true,
          backgroundColor: bgColor,
          borderColor: primaryColor,
          tension: 0.4,
          pointBackgroundColor: primaryColor,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            backgroundColor: 'rgba(255,255,255,0.8)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: 'rgba(200,200,200,0.5)',
            borderWidth: 1
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              drawBorder: false,
              color: getComputedStyle(document.body).getPropertyValue('--chart-grid')
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        },
        interaction: {
          intersect: false,
          mode: 'nearest'
        }
      }
    });
  }
  
  // Renderizar gráfico de consumo de combustível (com cores ajustadas para Power BI)
  renderFuelChart() {
    const ctx = document.getElementById('fuelChart');
    if (!ctx || typeof Chart === 'undefined') return;
    
    const secondaryColor = getComputedStyle(document.body).getPropertyValue('--chart-line-1'); // Usando a mesma cor
    const bgColor = getComputedStyle(document.body).getPropertyValue('--chart-bg-1'); // Usando o mesmo bg
    
    this.charts.fuel = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago'],
        datasets: [{
          label: 'Consumo de Combustível (L)',
          data: [120, 190, 150, 170, 180, 120, 140, 160],
          backgroundColor: bgColor,
          borderColor: secondaryColor,
          borderWidth: 1,
          borderRadius: 5
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          },
          tooltip: {
            backgroundColor: 'rgba(255,255,255,0.8)',
            titleColor: '#333',
            bodyColor: '#666',
            borderColor: 'rgba(200,200,200,0.5)',
            borderWidth: 1
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: {
              drawBorder: false,
              color: getComputedStyle(document.body).getPropertyValue('--chart-grid')
            }
          },
          x: {
            grid: {
              display: false
            }
          }
        }
      }
    });
  }
  
  // Alternar período dos gráficos (semanal, mensal, anual)
  setPeriod(chartId, period) {
    const chart = this.charts[chartId];
    if (!chart) return;
    
    // Simulação de dados para diferentes períodos
    let labels, data;
    
    switch (period) {
      case 'week':
        labels = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb', 'Dom'];
        data = [12, 19, 15, 17, 18, 12, 8];
        break;
      case 'month':
        labels = ['Semana 1', 'Semana 2', 'Semana 3', 'Semana 4'];
        data = [45, 65, 55, 70];
        break;
      case 'year':
        labels = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        data = [120, 190, 150, 170, 180, 120, 140, 160, 150, 140, 170, 190];
        break;
      default:
        return;
    }
    
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.update();
    
    // Atualizar estado dos botões
    const container = document.getElementById(`${chartId}Container`);
    if (container) {
      const buttons = container.querySelectorAll('.chart-actions button');
      buttons.forEach(btn => {
        btn.classList.remove('active');
        if (btn.getAttribute('data-period') === period) {
          btn.classList.add('active');
        }
      });
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  // Inicializar gráficos
  const dashboardCharts = new DashboardCharts();
  
  // Expor para uso global
  window.dashboardCharts = dashboardCharts;
  
  // Configurar botões de período
  document.querySelectorAll('[data-chart][data-period]').forEach(button => {
    button.addEventListener('click', (e) => {
      const chartId = e.target.getAttribute('data-chart');
      const period = e.target.getAttribute('data-period');
      dashboardCharts.setPeriod(chartId, period);
    });
  });
});