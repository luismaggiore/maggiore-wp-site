/**
 * ANIMATION CONTROLLER - Maggiore Theme
 * Controlador centralizado que unifica todos los requestAnimationFrame
 * 
 * @version 2.0.0 (Optimized)
 * @author Maggiore Marketing
 * 
 * CARACTERÍSTICAS v2.0:
 * - Un solo RAF para todos los efectos
 * - Sistema de prioridades
 * - Auto-pausa cuando no visible
 * - Respeta prefers-reduced-motion
 * - Monitoreo de performance en tiempo real
 * - Auto-degradación de calidad
 * 
 * USO:
 * window.animationController.register('myEffect', effectObject, priority);
 */

class AnimationController {
  constructor() {
    // Estado del controller
    this.effects = new Map();
    this.isRunning = false;
    this.frameId = null;
    
    // Métricas de tiempo
    this.lastTime = 0;
    this.fps = 0;
    this.frameCount = 0;
    this.fpsUpdateTime = 0;
    
    // Performance tracking
    this.performanceHistory = [];
    this.maxHistoryLength = 60; // 1 minuto a 60fps
    
    // Sistema de calidad adaptativa
    this.currentQuality = 'high';
    this.qualityLocked = false;
    
    // Setup de observers
    this.setupVisibilityObserver();
    this.setupReducedMotionObserver();
    
    if (window.VISUAL_CONFIG?.debug.logEffects) {
      console.log('🎬 Animation Controller v2.0 initialized');
    }
  }

  /**
   * Registra un efecto para ser actualizado en cada frame
   * 
   * @param {string} name - Nombre único del efecto
   * @param {Object} effect - Objeto con método update(deltaTime, currentTime)
   * @param {number} priority - Mayor = se ejecuta primero (default: 0)
   * @returns {AnimationController} this (para chaining)
   */
  register(name, effect, priority = 0) {
    if (this.effects.has(name)) {
      if (window.VISUAL_CONFIG?.debug.logEffects) {
        console.warn(`⚠️ Effect "${name}" already registered, replacing...`);
      }
    }

    this.effects.set(name, { 
      effect, 
      priority, 
      enabled: true,
      updateCount: 0,
      lastUpdateTime: 0,
      avgUpdateTime: 0
    });
    
    if (window.VISUAL_CONFIG?.debug.logEffects) {
      console.log(`✅ Registered effect: ${name} (priority: ${priority})`);
    }

    // Auto-start si es el primero y no hay reduced motion
    if (this.effects.size === 1 && !this.isRunning && !this.reducedMotionEnabled) {
      this.start();
    }

    return this;
  }

  /**
   * Desregistra un efecto
   * 
   * @param {string} name - Nombre del efecto
   * @returns {AnimationController} this
   */
  unregister(name) {
    const existed = this.effects.delete(name);
    
    if (existed && window.VISUAL_CONFIG?.debug.logEffects) {
      console.log(`❌ Unregistered effect: ${name}`);
    }
    
    // Auto-stop si no quedan efectos
    if (this.effects.size === 0) {
      this.stop();
    }
    
    return this;
  }

  /**
   * Habilita o deshabilita un efecto sin desregistrarlo
   * 
   * @param {string} name - Nombre del efecto
   * @param {boolean} enabled - true para habilitar, false para deshabilitar
   * @returns {AnimationController} this
   */
  toggle(name, enabled) {
    const entry = this.effects.get(name);
    
    if (entry) {
      entry.enabled = enabled;
      
      if (window.VISUAL_CONFIG?.debug.logEffects) {
        console.log(`🔄 Effect "${name}" ${enabled ? 'enabled' : 'disabled'}`);
      }
    } else {
      console.warn(`⚠️ Effect "${name}" not found`);
    }
    
    return this;
  }

  /**
   * Obtiene un efecto registrado
   * 
   * @param {string} name - Nombre del efecto
   * @returns {Object|null} El efecto o null si no existe
   */
  getEffect(name) {
    const entry = this.effects.get(name);
    return entry ? entry.effect : null;
  }

  /**
   * Inicia el loop de animación
   * 
   * @returns {AnimationController} this
   */
  start() {
    if (this.isRunning) {
      console.warn('⚠️ Animation Controller already running');
      return this;
    }
    
    if (this.reducedMotionEnabled) {
      console.log('♿ Reduced motion enabled - animations disabled');
      return this;
    }
    
    this.isRunning = true;
    this.lastTime = performance.now();
    this.fpsUpdateTime = this.lastTime;
    this.frameCount = 0;
    this.tick(this.lastTime);
    
    if (window.VISUAL_CONFIG?.debug.logEffects) {
      console.log('▶️ Animation Controller started');
    }
    
    return this;
  }

  /**
   * Detiene el loop de animación
   * 
   * @returns {AnimationController} this
   */
  stop() {
    if (!this.isRunning) return this;
    
    this.isRunning = false;
    
    if (this.frameId) {
      cancelAnimationFrame(this.frameId);
      this.frameId = null;
    }
    
    if (window.VISUAL_CONFIG?.debug.logEffects) {
      console.log('⏸️ Animation Controller stopped');
    }
    
    return this;
  }

  /**
   * Loop principal - UN SOLO RAF PARA TODO
   * @private
   */
  tick(currentTime) {
    if (!this.isRunning) return;

    // Calcula delta time en segundos
    const deltaTime = (currentTime - this.lastTime) / 1000;
    this.lastTime = currentTime;

    // Actualiza FPS cada segundo
    this.frameCount++;
    if (currentTime - this.fpsUpdateTime >= 1000) {
      this.fps = this.frameCount;
      this.frameCount = 0;
      this.fpsUpdateTime = currentTime;
      
      // Guardar en historial
      this.performanceHistory.push(this.fps);
      if (this.performanceHistory.length > this.maxHistoryLength) {
        this.performanceHistory.shift();
      }
      
      // Check de performance automático
      this.checkPerformance();
    }

    // Ordena efectos por prioridad (mayor primero)
    const sortedEffects = Array.from(this.effects.entries())
      .filter(([_, entry]) => entry.enabled)
      .sort((a, b) => b[1].priority - a[1].priority);

    // Actualiza cada efecto habilitado
    sortedEffects.forEach(([name, entry]) => {
      const updateStart = performance.now();
      
      try {
        // Verifica si el efecto tiene shouldUpdate y lo respeta
        const shouldUpdate = !entry.effect.shouldUpdate || 
                           entry.effect.shouldUpdate(currentTime, deltaTime);
        
        if (shouldUpdate) {
          entry.effect.update(deltaTime, currentTime);
          entry.updateCount++;
          entry.lastUpdateTime = currentTime;
          
          // Calcula tiempo promedio de actualización
          const updateTime = performance.now() - updateStart;
          entry.avgUpdateTime = entry.avgUpdateTime 
            ? (entry.avgUpdateTime * 0.9 + updateTime * 0.1) 
            : updateTime;
        }
      } catch (error) {
        console.error(`❌ Error updating effect "${name}":`, error);
        // Deshabilita el efecto que falla para no bloquear los demás
        entry.enabled = false;
      }
    });

    // Siguiente frame
    this.frameId = requestAnimationFrame((t) => this.tick(t));
  }

  /**
   * Chequeo automático de performance
   * Ajusta la calidad si es necesario
   * @private
   */
  checkPerformance() {
    if (this.qualityLocked || !window.VISUAL_CONFIG?.performance.enableMonitoring) {
      return;
    }
    
    const config = window.VISUAL_CONFIG.performance;
    const avgFPS = this.getAverageFPS();
    
    // Degradar calidad si FPS muy bajo
    if (avgFPS < config.autoDegradeThreshold && this.currentQuality !== 'low') {
      this.degradeQuality();
      
      if (window.VISUAL_CONFIG.debug.showPerformanceWarnings) {
        console.warn(`⚠️ Low FPS detected (${Math.round(avgFPS)}), degrading quality to improve performance`);
      }
    }
    
    // Mejorar calidad si FPS es estable y alto
    if (avgFPS > config.autoUpgradeThreshold && this.currentQuality !== 'high') {
      this.upgradeQuality();
      
      if (window.VISUAL_CONFIG?.debug.logPerformance) {
        console.log(`✅ Good FPS (${Math.round(avgFPS)}), upgrading quality`);
      }
    }
  }

  /**
   * Degrada la calidad para mejorar performance
   * @private
   */
  degradeQuality() {
    const qualityLevels = ['high', 'medium', 'low'];
    const currentIndex = qualityLevels.indexOf(this.currentQuality);
    
    if (currentIndex < qualityLevels.length - 1) {
      this.currentQuality = qualityLevels[currentIndex + 1];
      this.applyQualityPreset(this.currentQuality);
    }
  }

  /**
   * Mejora la calidad
   * @private
   */
  upgradeQuality() {
    const qualityLevels = ['high', 'medium', 'low'];
    const currentIndex = qualityLevels.indexOf(this.currentQuality);
    
    if (currentIndex > 0) {
      this.currentQuality = qualityLevels[currentIndex - 1];
      this.applyQualityPreset(this.currentQuality);
    }
  }

  /**
   * Aplica un preset de calidad
   * @private
   */
  applyQualityPreset(quality) {
    const preset = window.VISUAL_CONFIG?.performance.qualityPresets[quality];
    if (!preset) return;
    
    // Aplicar a efectos registrados
    const aurora = this.getEffect('aurora');
    if (aurora && aurora.setQuality) {
      aurora.setQuality(preset);
    }
    
    const constellation = this.getEffect('constellation');
    if (constellation) {
      this.toggle('constellation', preset.constellationEnabled);
    }
  }

  /**
   * Calcula el FPS promedio de los últimos frames
   * @returns {number}
   */
  getAverageFPS() {
    if (this.performanceHistory.length === 0) return this.fps;
    
    const sum = this.performanceHistory.reduce((a, b) => a + b, 0);
    return sum / this.performanceHistory.length;
  }

  /**
   * Pausa automáticamente cuando la página no es visible
   * @private
   */
  setupVisibilityObserver() {
    if (!window.VISUAL_CONFIG?.performance.pauseWhenHidden) return;
    
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        this.stop();
        if (window.VISUAL_CONFIG?.debug.logEffects) {
          console.log('👁️ Page hidden - animations paused');
        }
      } else {
        this.start();
        if (window.VISUAL_CONFIG?.debug.logEffects) {
          console.log('👁️ Page visible - animations resumed');
        }
      }
    });
  }

  /**
   * Respeta la preferencia de reduced motion del usuario
   * @private
   */
  setupReducedMotionObserver() {
    if (!window.VISUAL_CONFIG?.performance.respectReducedMotion) return;
    
    const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    
    const handleReducedMotion = (e) => {
      this.reducedMotionEnabled = e.matches;
      
      if (e.matches) {
        this.stop();
        console.log('♿ Reduced motion detected - animations disabled');
        document.body.classList.add('visual-engine-reduced-motion');
      } else {
        document.body.classList.remove('visual-engine-reduced-motion');
        if (this.effects.size > 0) {
          this.start();
        }
      }
    };

    mediaQuery.addEventListener('change', handleReducedMotion);
    handleReducedMotion(mediaQuery);
  }

  /**
   * Obtiene estadísticas completas del controller
   * 
   * @returns {Object} Estadísticas detalladas
   */
  getStats() {
    const effectStats = {};
    
    this.effects.forEach((entry, name) => {
      effectStats[name] = {
        enabled: entry.enabled,
        priority: entry.priority,
        updateCount: entry.updateCount,
        lastUpdate: entry.lastUpdateTime,
        avgUpdateTime: Math.round(entry.avgUpdateTime * 100) / 100
      };
    });

    return {
      fps: Math.round(this.fps),
      avgFPS: Math.round(this.getAverageFPS()),
      effectCount: this.effects.size,
      activeEffects: Array.from(this.effects.entries())
        .filter(([_, e]) => e.enabled)
        .length,
      isRunning: this.isRunning,
      currentQuality: this.currentQuality,
      reducedMotion: this.reducedMotionEnabled || false,
      effects: effectStats
    };
  }

  /**
   * Imprime estadísticas en consola (útil para debugging)
   * 
   * @returns {AnimationController} this
   */
  logStats() {
    const stats = this.getStats();
    console.log('%c📊 Animation Controller Stats', 'color: #00d0ff; font-weight: bold;');
    console.table(stats);
    return this;
  }

  /**
   * Destruye el controller y limpia recursos
   */
  destroy() {
    this.stop();
    this.effects.clear();
    this.performanceHistory = [];
    console.log('💥 Animation Controller destroyed');
  }
}

// ============================================================
// EXPORT & SINGLETON
// ============================================================

if (typeof window !== 'undefined') {
  // Crear singleton global
  window.AnimationController = AnimationController;
  window.animationController = new AnimationController();
  
  // Debug helper accesible desde consola
  if (window.VISUAL_CONFIG?.debug.showFPS) {
    // Mostrar FPS cada 2 segundos
    setInterval(() => {
      const stats = window.animationController.getStats();
      console.log(`FPS: ${stats.fps} | Avg: ${stats.avgFPS} | Quality: ${stats.currentQuality} | Effects: ${stats.activeEffects}/${stats.effectCount}`);
    }, 2000);
  }
}

// Export para módulos ES6
if (typeof module !== 'undefined' && module.exports) {
  module.exports = AnimationController;
}
