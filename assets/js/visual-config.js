/**
 * Visual Configuration
 * Configuración centralizada de todos los efectos visuales
 * 
 * @version 1.0.0
 * @author Maggiore Marketing
 */

const VISUAL_CONFIG = {
  
  // ============================================================
  // AURORA EFFECT
  // ============================================================
  aurora: {
    enabled: true,
    
    // Número de barras verticales
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
    
    // Velocidades (ciclos por segundo)
    speedX: 0.07,           // Velocidad de movimiento horizontal
    speedScale: 0.07,       // Velocidad de pulsación
    speedMultiplier: 1.1,   // Multiplicador general de velocidad
    
    // Variación aleatoria
    speedJitter: 0.15,      // ±15% variación de velocidad
    amplitudeJitter: 0.2,   // ±20% variación de amplitud
    
    // Configuración de blur
    blur: {
      min: 25,              // Blur mínimo (px)
      max: 160,             // Blur máximo (px)
      responsive: true      // Usa clamp() en CSS
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
    
    // Parámetros de conexión
    maxDistance: 220,       // Distancia máxima para conectar puntos
    maxNeighbors: 2,        // Número de vecinos más cercanos a conectar
    
    // Estilo de líneas
    lineStyle: {
      stroke: 'gray',
      strokeWidth: 0.9,
      opacity: 0.8
    },
    
    // Umbral de movimiento para detectar cambios (px)
    movementThreshold: 1,
    
    // Estados de la constelación (diferentes formas)
    states: {
      // Estado Robin Hood
      robinHood: [
        { cx: 300, cy: 250 },
        { cx: 240, cy: 130 },
        { cx: 300, cy: 80 },
        { cx: 300, cy: 410 },
        { cx: 140, cy: 510 },
        { cx: 360, cy: 130 },
        { cx: 460, cy: 510 }
      ],
      
      // Estado Inteligencia
      inteligencia: [
        { cx: 340, cy: 260 },
        { cx: 280, cy: 140 },
        { cx: 340, cy: 90 },
        { cx: 340, cy: 420 },
        { cx: 180, cy: 520 },
        { cx: 400, cy: 140 },
        { cx: 500, cy: 520 }
      ],
      
      // Estado Flexible
      flexible: [
        { cx: 50, cy: 290 },
        { cx: 200, cy: 390 },
        { cx: 300, cy: 290 },
        { cx: 150, cy: 190 },
        { cx: 450, cy: 390 },
        { cx: 400, cy: 190 },
        { cx: 550, cy: 290 }
      ]
    }
  },

  // ============================================================
  // SCROLL ANIMATIONS (GSAP)
  // ============================================================
  scrollAnimations: {
    enabled: true,
    
    // Configuración de ScrollSmoother
    smoother: {
      enabled: false,       // Deshabilitar si causa problemas
      smooth: 1.5,
      effects: true
    },
    
    // Configuración de morphing de constelación
    constellationMorph: {
      duration: 1,
      ease: 'none',
      stagger: 0.03
    },
    
    // Parallax
    parallax: {
      enabled: true,
      intensity: 150        // Movimiento máximo en px
    },
    
    // Animaciones de entrada
    entrance: {
      duration: 0.6,
      stagger: 0.4,
      ease: 'power1.out'
    }
  },

  // ============================================================
  // PERFORMANCE
  // ============================================================
  performance: {
    // FPS target
    targetFPS: 55,
    
    // Monitoreo de performance
    enableMonitoring: true,
    monitorInterval: 1000,  // ms entre checks de FPS
    
    // Degradación automática
    autoDegradeThreshold: 30, // FPS mínimo antes de degradar
    
    // Pausa automática
    pauseWhenHidden: true,
    
    // Respeta prefers-reduced-motion
    respectReducedMotion: true,
    
    // Detección de dispositivo
    detectLowEnd: true,
    
    // Configuraciones por tipo de dispositivo
    deviceSettings: {
      desktop: {
        auroraCount: 18,
        constellationEnabled: true,
        highQuality: true
      },
      tablet: {
        auroraCount: 12,
        constellationEnabled: true,
        highQuality: false
      },
      mobile: {
        auroraCount: 6,
        constellationEnabled: false, // Muy pesado para móvil
        highQuality: false
      }
    }
  },

  // ============================================================
  // DEBUGGING
  // ============================================================
  debug: {
    // Mostrar FPS en pantalla
    showFPS: false,         // Cambiar a true durante desarrollo
    
    // Logs en consola
    logEffects: true,       // Log cuando se registran efectos
    logPerformance: false,  // Log de métricas de performance
    logAnimations: false,   // Log de animaciones GSAP
    
    // Panel de debug
    showDebugPanel: false,  // Panel visual con stats
    
    // Warnings
    showPerformanceWarnings: true,
    
    // Color de logs
    colors: {
      info: '#00d0ff',
      warning: '#ffa500',
      error: '#ff0000',
      success: '#00ff99'
    }
  },

  // ============================================================
  // FEATURES FLAGS (para activar/desactivar funcionalidades)
  // ============================================================
  features: {
    aurora: true,
    constellation: true,
    parallax: true,
    textAnimations: true,
    testimonialSlider: true
  }
};

// ============================================================
// DEVICE DETECTION HELPER
// ============================================================
VISUAL_CONFIG.getDeviceType = function() {
  const width = window.innerWidth;
  if (width < 768) return 'mobile';
  if (width < 1024) return 'tablet';
  return 'desktop';
};

// ============================================================
// PERFORMANCE HELPER
// ============================================================
VISUAL_CONFIG.isLowEndDevice = function() {
  // Detecta dispositivos de baja potencia
  const cores = navigator.hardwareConcurrency || 2;
  const memory = navigator.deviceMemory || 4;
  const isMobile = /Android|iPhone|iPad/i.test(navigator.userAgent);
  
  return cores <= 2 || memory <= 4 || isMobile;
};

// ============================================================
// CONFIGURATION GETTER con device detection
// ============================================================
VISUAL_CONFIG.getOptimalConfig = function() {
  const deviceType = this.getDeviceType();
  const isLowEnd = this.isLowEndDevice();
  
  // Clonar config base
  const config = JSON.parse(JSON.stringify(this));
  
  // Aplicar ajustes por dispositivo
  if (isLowEnd || deviceType === 'mobile') {
    config.aurora.barCount = 6;
    config.aurora.targetFPS = 30;
    config.constellation.enabled = false;
    config.scrollAnimations.smoother.enabled = false;
  } else if (deviceType === 'tablet') {
    config.aurora.barCount = 12;
    config.aurora.targetFPS = 45;
  }
  
  return config;
};

// ============================================================
// EXPORT
// ============================================================

// Para uso global en navegador
if (typeof window !== 'undefined') {
  window.VISUAL_CONFIG = VISUAL_CONFIG;
  
  // Log de configuración en desarrollo
  if (VISUAL_CONFIG.debug.logEffects) {
    console.log('%c🎨 Visual Config Loaded', 
      'color: #00d0ff; font-weight: bold; font-size: 14px;');
    console.log('Device:', VISUAL_CONFIG.getDeviceType());
    console.log('Low-end device:', VISUAL_CONFIG.isLowEndDevice());
  }
}

// Para módulos ES6
if (typeof module !== 'undefined' && module.exports) {
  module.exports = VISUAL_CONFIG;
}
