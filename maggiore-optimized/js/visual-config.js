/**
 * VISUAL CONFIGURATION - Maggiore Theme
 * Configuración centralizada de todos los efectos visuales
 * 
 * @version 2.0.0 (Optimized)
 * @author Maggiore Marketing
 * 
 * CHANGELOG v2.0:
 * - Agregada detección automática de dispositivos
 * - Configuraciones adaptativas por tipo de device
 * - Sistema de degradación automática
 * - Performance presets
 */

const VISUAL_CONFIG = {
  
  // ============================================================
  // AURORA EFFECT
  // ============================================================
  aurora: {
    enabled: true,
    
    // Número de barras verticales (se ajusta automáticamente por dispositivo)
    barCount: 18,
    
    // Paleta de colores
    palette: [
      '#00d0ff',
      '#00ffff',
      '#00ff99',
      '#00ff91',
      '#041e59',
      '#00b7ff'
    ],
    
    // Amplitudes de animación
    amplitudeX: 615,        // Movimiento horizontal máximo (px)
    amplitudeScale: 0.2,    // Escala de pulsación
    
    // Velocidades
    speedX: 0.07,           // Ciclos por segundo - horizontal
    speedScale: 0.07,       // Ciclos por segundo - pulsación
    speedMultiplier: 1.1,   // Multiplicador general
    
    // Variación aleatoria (para naturalidad)
    speedJitter: 0.15,      // ±15% variación de velocidad
    amplitudeJitter: 0.2,   // ±20% variación de amplitud
    
    // Blur
    blur: {
      min: 25,
      max: 160,
      responsive: true
    },
    
    // Opacidad
    opacity: {
      min: 0.55,
      max: 0.9
    },
    
    // Performance
    targetFPS: 60,
    renderThrottle: 16      // ms entre renders (16ms ≈ 60fps)
  },

  // ============================================================
  // CONSTELLATION EFFECT
  // ============================================================
  constellation: {
    enabled: true,
    
    // Conexiones
    maxDistance: 220,       // Distancia máxima para conectar puntos
    maxNeighbors: 2,        // Vecinos más cercanos a conectar
    
    // Estilo de líneas
    lineStyle: {
      stroke: 'gray',
      strokeWidth: 0.9,
      opacity: 0.8
    },
    
    // Detección de cambios
    movementThreshold: 1,   // px - umbral para detectar movimiento
    updateThrottle: 32,     // ms - throttle de actualización
    
    // Estados/formas para morphing (definidos en main.js)
    enableMorphing: true
  },

  // ============================================================
  // SCROLL ANIMATIONS (GSAP)
  // ============================================================
  scrollAnimations: {
    enabled: true,
    
    // ScrollTrigger defaults
    markers: false,         // Cambiar a true para debug
    
    // Scrub suavizado
    scrub: true,
    scrubDuration: 1,
    
    // Animaciones de texto
    textAnimations: {
      splitType: 'words, chars',
      stagger: 0.03,
      duration: 1,
      ease: 'power3.out'
    }
  },

  // ============================================================
  // PERFORMANCE
  // ============================================================
  performance: {
    // FPS objetivo
    targetFPS: 55,
    
    // Degradación automática
    autoDegradeThreshold: 30,  // Si baja de 30 FPS, degradar
    autoUpgradeThreshold: 55,   // Si sube a 55 FPS, mejorar
    
    // Monitoreo
    enableMonitoring: true,
    monitoringInterval: 2000,   // ms
    
    // Pausa automática
    pauseWhenHidden: true,
    
    // Respeto por preferencias del usuario
    respectReducedMotion: true,
    
    // Detección de dispositivo
    detectLowEnd: true,
    
    // Presets por calidad
    qualityPresets: {
      high: {
        auroraCount: 18,
        constellationEnabled: true,
        renderThrottle: 16,
        textAnimations: true
      },
      medium: {
        auroraCount: 12,
        constellationEnabled: true,
        renderThrottle: 24,
        textAnimations: true
      },
      low: {
        auroraCount: 6,
        constellationEnabled: false,
        renderThrottle: 32,
        textAnimations: false
      }
    }
  },

  // ============================================================
  // DEBUG
  // ============================================================
  debug: {
    showFPS: false,                    // Panel FPS en pantalla
    logEffects: true,                  // Log de registro de efectos
    logPerformance: false,             // Métricas detalladas
    showPerformanceWarnings: true,     // Avisos de bajo rendimiento
    
    colors: {
      info: '#00d0ff',
      warning: '#ffa500',
      error: '#ff0000',
      success: '#00ff99'
    }
  },

  // ============================================================
  // HELPERS - Funciones utilitarias
  // ============================================================
  
  /**
   * Detecta el tipo de dispositivo
   * @returns {string} 'mobile' | 'tablet' | 'desktop'
   */
  getDeviceType() {
    const width = window.innerWidth;
    if (width < 768) return 'mobile';
    if (width < 1024) return 'tablet';
    return 'desktop';
  },

  /**
   * Detecta si es un dispositivo de baja potencia
   * @returns {boolean}
   */
  isLowEndDevice() {
    const cores = navigator.hardwareConcurrency || 2;
    const memory = navigator.deviceMemory || 4;
    const isMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);
    
    return cores <= 2 || memory <= 4 || isMobile;
  },

  /**
   * Obtiene la configuración óptima según el dispositivo
   * @returns {Object} Configuración adaptada
   */
  getOptimalConfig() {
    const deviceType = this.getDeviceType();
    const isLowEnd = this.isLowEndDevice();
    
    // Seleccionar preset
    let preset = 'high';
    if (isLowEnd || deviceType === 'mobile') {
      preset = 'low';
    } else if (deviceType === 'tablet') {
      preset = 'medium';
    }
    
    const qualityConfig = this.performance.qualityPresets[preset];
    
    // Log para debug
    if (this.debug.logPerformance) {
      console.log('%c📱 Device Detection', `color: ${this.debug.colors.info}`);
      console.log(`Type: ${deviceType}`);
      console.log(`Low-end: ${isLowEnd}`);
      console.log(`Preset: ${preset}`);
      console.log('Config:', qualityConfig);
    }
    
    return {
      ...qualityConfig,
      deviceType,
      isLowEnd,
      preset
    };
  },

  /**
   * Aplica configuración adaptativa automáticamente
   */
  applyAdaptiveConfig() {
    const optimal = this.getOptimalConfig();
    
    // Aplicar a Aurora
    this.aurora.barCount = optimal.auroraCount;
    this.aurora.renderThrottle = optimal.renderThrottle;
    
    // Aplicar a Constelación
    this.constellation.enabled = optimal.constellationEnabled;
    
    // Aplicar a animaciones de texto
    this.scrollAnimations.textAnimations.enabled = optimal.textAnimations;
    
    if (this.debug.logEffects) {
      console.log('%c⚙️ Adaptive config applied', `color: ${this.debug.colors.success}`);
    }
  }
};

// ============================================================
// AUTO-INICIALIZACIÓN
// ============================================================
if (typeof window !== 'undefined') {
  window.VISUAL_CONFIG = VISUAL_CONFIG;
  
  // Aplicar configuración adaptativa al cargar
  if (VISUAL_CONFIG.performance.detectLowEnd) {
    VISUAL_CONFIG.applyAdaptiveConfig();
  }
  
  // Log inicial
  if (VISUAL_CONFIG.debug.logEffects) {
    console.log(
      '%c🎨 Visual Config v2.0 Loaded', 
      `color: ${VISUAL_CONFIG.debug.colors.info}; font-weight: bold; font-size: 14px;`
    );
    console.log(`Device: ${VISUAL_CONFIG.getDeviceType()} | Low-end: ${VISUAL_CONFIG.isLowEndDevice()}`);
  }
}

// Export para módulos ES6
if (typeof module !== 'undefined' && module.exports) {
  module.exports = VISUAL_CONFIG;
}
