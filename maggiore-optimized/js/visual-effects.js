/**
 * VISUAL EFFECTS - Maggiore Theme
 * Efectos visuales unificados: Aurora + Constelación
 * 
 * @version 2.0.0 (Optimized & Unified)
 * @author Maggiore Marketing
 * 
 * ESTE ARCHIVO REEMPLAZA:
 * - aurora.js (efecto de barras aurora boreal)
 * - constelacion.js (efecto de líneas conectando puntos)
 * 
 * BENEFICIOS:
 * - Código más mantenible (un solo archivo)
 * - Menos requests HTTP
 * - Funciones compartidas sin duplicar
 * - ~40% menos código total
 */

// ============================================================================
// AURORA EFFECT
// ============================================================================

class AuroraEffect {
  constructor() {
    this.canvas = null;
    this.ctx = null;
    this.bars = [];
    this.lastRender = 0;
    this.isVisible = false;
    
    // Obtener config
    this.config = window.VISUAL_CONFIG?.aurora || {};
    this.COUNT = this.config.barCount || 18;
    
    // Inicializar
    this.init();
  }

  /**
   * Inicializa el canvas y las barras
   */
  init() {
    // Buscar canvas
    this.canvas = document.getElementById('aurora');
    if (!this.canvas) {
      console.warn('⚠️ Aurora canvas not found');
      return;
    }

    this.ctx = this.canvas.getContext('2d', { 
      alpha: true,
      desynchronized: true // Mejora performance
    });
    
    // Configurar tamaño del canvas
    this.resize();
    window.addEventListener('resize', () => this.resize());

    // Crear barras
    this.createBars();
    
    // Setup visibility observer
    this.setupVisibilityObserver();
    
    if (window.VISUAL_CONFIG?.debug.logEffects) {
      console.log(`✅ Aurora initialized with ${this.COUNT} bars`);
    }
  }

  /**
   * Redimensiona el canvas
   */
  resize() {
    if (!this.canvas) return;
    
    this.canvas.width = this.canvas.offsetWidth;
    this.canvas.height = this.canvas.offsetHeight;
  }

  /**
   * Crea las barras con propiedades aleatorias
   */
  createBars() {
    const palette = this.config.palette || ['#00d0ff', '#00ffff'];
    const speedJitter = this.config.speedJitter || 0.15;
    const amplitudeJitter = this.config.amplitudeJitter || 0.2;
    
    this.bars = [];
    
    for (let i = 0; i < this.COUNT; i++) {
      // Variación aleatoria para naturalidad
      const speedVariation = 1 + (Math.random() - 0.5) * speedJitter;
      const amplitudeVariation = 1 + (Math.random() - 0.5) * amplitudeJitter;
      
      this.bars.push({
        x: (i / (this.COUNT - 1)) * this.canvas.width,
        baseY: this.canvas.height / 2,
        color: palette[Math.floor(Math.random() * palette.length)],
        phase: Math.random() * Math.PI * 2,
        speedX: (this.config.speedX || 0.07) * speedVariation,
        speedScale: (this.config.speedScale || 0.07) * speedVariation,
        amplitudeX: (this.config.amplitudeX || 615) * amplitudeVariation,
        amplitudeScale: this.config.amplitudeScale || 0.2,
        currentX: 0,
        currentScale: 1
      });
    }
  }

  /**
   * Observer para detectar visibilidad del canvas
   */
  setupVisibilityObserver() {
    if (!this.canvas) return;
    
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          this.isVisible = entry.isIntersecting;
        });
      },
      { threshold: 0.1 }
    );

    observer.observe(this.canvas);
  }

  /**
   * Determina si debe actualizar (throttling)
   */
  shouldUpdate(currentTime) {
    if (!this.isVisible) return false;
    
    const throttle = this.config.renderThrottle || 16;
    return (currentTime - this.lastRender) >= throttle;
  }

  /**
   * Actualiza y renderiza las barras
   */
  update(deltaTime, currentTime) {
    if (!this.canvas || !this.ctx) return;
    
    // Clear canvas
    this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    
    // Actualizar cada barra
    this.bars.forEach(bar => {
      // Actualizar fase
      bar.phase += deltaTime * this.config.speedMultiplier;
      
      // Calcular posición X con onda sinusoidal
      bar.currentX = bar.x + Math.sin(bar.phase * bar.speedX) * bar.amplitudeX;
      
      // Calcular escala con onda sinusoidal (respiración)
      bar.currentScale = 1 + Math.sin(bar.phase * bar.speedScale) * bar.amplitudeScale;
      
      // Dibujar barra
      this.drawBar(bar);
    });
    
    this.lastRender = currentTime;
  }

  /**
   * Dibuja una barra individual
   */
  drawBar(bar) {
    if (!this.ctx || !this.canvas) return;
    
    const height = this.canvas.height * bar.currentScale;
    const y = bar.baseY - height / 2;
    
    // Opacity según config
    const opacity = this.config.opacity 
      ? (this.config.opacity.min + Math.random() * (this.config.opacity.max - this.config.opacity.min))
      : 0.7;
    
    this.ctx.fillStyle = bar.color;
    this.ctx.globalAlpha = opacity;
    this.ctx.fillRect(bar.currentX, y, 2, height);
    this.ctx.globalAlpha = 1;
  }

  /**
   * Ajusta la calidad del efecto
   */
  setQuality(preset) {
    if (preset.auroraCount && preset.auroraCount !== this.COUNT) {
      this.COUNT = preset.auroraCount;
      this.createBars();
      
      if (window.VISUAL_CONFIG?.debug.logPerformance) {
        console.log(`🎨 Aurora quality adjusted: ${this.COUNT} bars`);
      }
    }
    
    if (preset.renderThrottle) {
      this.config.renderThrottle = preset.renderThrottle;
    }
  }

  /**
   * Destruye el efecto
   */
  destroy() {
    this.bars = [];
    if (this.canvas) {
      this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
    }
  }
}

// ============================================================================
// CONSTELLATION EFFECT
// ============================================================================

class ConstellationEffect {
  constructor() {
    this.svg = null;
    this.lineGroup = null;
    this.circles = [];
    this.previousPositions = null;
    this.lastUpdate = 0;
    this.dirty = false;
    
    // Config
    this.config = window.VISUAL_CONFIG?.constellation || {};
    this.maxDistance = this.config.maxDistance || 220;
    this.maxNeighbors = this.config.maxNeighbors || 2;
    
    // Inicializar
    this.init();
  }

  /**
   * Inicializa el SVG y detecta círculos
   */
  init() {
    this.svg = document.querySelector('.constelacion svg');
    
    if (!this.svg) {
      console.warn('⚠️ Constellation SVG not found');
      return;
    }

    // Crear grupo para líneas
    this.lineGroup = this.svg.querySelector('g[data-constellation-lines]');
    if (!this.lineGroup) {
      this.lineGroup = document.createElementNS('http://www.w3.org/2000/svg', 'g');
      this.lineGroup.setAttribute('data-constellation-lines', 'true');
      this.svg.insertBefore(this.lineGroup, this.svg.firstChild);
    }

    // Detectar círculos
    this.updateCircles();
    
    // Render inicial
    this.renderLines();
    
    if (window.VISUAL_CONFIG?.debug.logEffects) {
      console.log(`✅ Constellation initialized with ${this.circles.length} points`);
    }
  }

  /**
   * Actualiza la lista de círculos y sus posiciones
   */
  updateCircles() {
    if (!this.svg) return;
    
    this.circles = Array.from(this.svg.querySelectorAll('circle')).map(circle => ({
      element: circle,
      cx: parseFloat(circle.getAttribute('cx')),
      cy: parseFloat(circle.getAttribute('cy'))
    }));
  }

  /**
   * Calcula distancia euclidiana entre dos puntos
   */
  distance(a, b) {
    const dx = a.cx - b.cx;
    const dy = a.cy - b.cy;
    return Math.sqrt(dx * dx + dy * dy);
  }

  /**
   * Detecta si las posiciones cambiaron significativamente
   */
  checkPositionChanges() {
    const currentPositions = this.circles.map(c => ({ cx: c.cx, cy: c.cy }));
    
    if (!this.previousPositions) {
      this.previousPositions = currentPositions;
      return true;
    }

    const threshold = this.config.movementThreshold || 1;
    
    for (let i = 0; i < currentPositions.length; i++) {
      const curr = currentPositions[i];
      const prev = this.previousPositions[i];
      
      if (Math.abs(curr.cx - prev.cx) > threshold || 
          Math.abs(curr.cy - prev.cy) > threshold) {
        this.previousPositions = currentPositions;
        return true;
      }
    }
    
    return false;
  }

  /**
   * Determina si debe actualizar (throttling + change detection)
   */
  shouldUpdate(currentTime) {
    const throttle = this.config.updateThrottle || 32;
    const timePassed = (currentTime - this.lastUpdate) >= throttle;
    
    if (!timePassed) return false;
    
    // Actualizar posiciones actuales
    this.updateCircles();
    
    // Solo render si cambió algo
    return this.checkPositionChanges() || this.dirty;
  }

  /**
   * Actualiza y renderiza las líneas
   */
  update(deltaTime, currentTime) {
    this.renderLines();
    this.lastUpdate = currentTime;
    this.dirty = false;
  }

  /**
   * Renderiza las líneas de conexión
   */
  renderLines() {
    if (!this.lineGroup) return;
    
    // Limpiar líneas existentes
    this.lineGroup.innerHTML = '';
    
    const style = this.config.lineStyle || {};
    
    // Para cada círculo, conectar con sus vecinos más cercanos
    this.circles.forEach((circle, i) => {
      // Calcular distancias a todos los otros círculos
      const distances = this.circles
        .map((other, j) => ({
          index: j,
          distance: i === j ? Infinity : this.distance(circle, other)
        }))
        .filter(d => d.distance <= this.maxDistance)
        .sort((a, b) => a.distance - b.distance)
        .slice(0, this.maxNeighbors);

      // Crear líneas
      distances.forEach(({ index: j }) => {
        // Evitar líneas duplicadas (i < j)
        if (i < j) {
          const other = this.circles[j];
          const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
          
          line.setAttribute('x1', circle.cx);
          line.setAttribute('y1', circle.cy);
          line.setAttribute('x2', other.cx);
          line.setAttribute('y2', other.cy);
          line.setAttribute('stroke', style.stroke || 'gray');
          line.setAttribute('stroke-width', style.strokeWidth || 0.9);
          line.setAttribute('opacity', style.opacity || 0.8);
          
          this.lineGroup.appendChild(line);
        }
      });
    });
  }

  /**
   * Fuerza una actualización en el próximo frame
   */
  forceUpdate() {
    this.dirty = true;
  }

  /**
   * Destruye el efecto
   */
  destroy() {
    if (this.lineGroup) {
      this.lineGroup.innerHTML = '';
    }
  }
}

// ============================================================================
// AUTO-INICIALIZACIÓN & REGISTRO
// ============================================================================

document.addEventListener('DOMContentLoaded', () => {
  // Esperar a que exista el controller
  if (!window.animationController) {
    console.error('❌ Animation Controller not found! Load animation-controller.js first');
    return;
  }

  const config = window.VISUAL_CONFIG || {};
  
  // Inicializar Aurora
  if (config.aurora?.enabled !== false && document.getElementById('aurora')) {
    const aurora = new AuroraEffect();
    window.animationController.register('aurora', aurora, 10); // Priority 10
    
    // Exportar para acceso externo
    window.auroraEffect = aurora;
  }

  // Inicializar Constelación
  if (config.constellation?.enabled !== false && document.querySelector('.constelacion')) {
    const constellation = new ConstellationEffect();
    window.animationController.register('constellation', constellation, 5); // Priority 5
    
    // Exportar para acceso externo
    window.constellationEffect = constellation;
  }

  if (window.VISUAL_CONFIG?.debug.logEffects) {
    console.log('🎨 Visual Effects initialized and registered');
  }
});

// Export para módulos ES6
if (typeof module !== 'undefined' && module.exports) {
  module.exports = { AuroraEffect, ConstellationEffect };
}
