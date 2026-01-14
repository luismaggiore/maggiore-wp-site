/**
 * Animation Controller
 * Controlador central que unifica todos los requestAnimationFrame
 * Evita tener múltiples RAF corriendo simultáneamente
 * 
 * @version 1.0.0
 * @author Maggiore Marketing
 */

class AnimationController {
  constructor() {
    this.effects = new Map();
    this.isRunning = false;
    this.frameId = null;
    this.lastTime = 0;
    this.fps = 0;
    this.frameCount = 0;
    this.fpsUpdateTime = 0;
    
    // Setup de observers
    this.setupVisibilityObserver();
    this.setupReducedMotionObserver();
    
    console.log('🎬 Animation Controller initialized');
  }

  /**
   * Registra un efecto para ser actualizado en cada frame
   * @param {string} name - Nombre único del efecto
   * @param {Object} effect - Objeto con método update(deltaTime, currentTime)
   * @param {number} priority - Mayor = se ejecuta primero (default: 0)
   */
  register(name, effect, priority = 0) {
    if (this.effects.has(name)) {
      console.warn(`⚠️ Effect "${name}" already registered, replacing...`);
    }

    this.effects.set(name, { 
      effect, 
      priority, 
      enabled: true,
      updateCount: 0,
      lastUpdateTime: 0
    });
    
    console.log(`✅ Registered effect: ${name} (priority: ${priority})`);

    // Auto-start si es el primero
    if (this.effects.size === 1 && !this.isRunning) {
      this.start();
    }

    return this;
  }

  /**
   * Desregistra un efecto
   * @param {string} name - Nombre del efecto
   */
  unregister(name) {
    const existed = this.effects.delete(name);
    if (existed) {
      console.log(`❌ Unregistered effect: ${name}`);
    }
    
    if (this.effects.size === 0) {
      this.stop();
    }
    
    return this;
  }

  /**
   * Habilita o deshabilita un efecto sin desregistrarlo
   * @param {string} name - Nombre del efecto
   * @param {boolean} enabled - true para habilitar, false para deshabilitar
   */
  toggle(name, enabled) {
    const entry = this.effects.get(name);
    if (entry) {
      entry.enabled = enabled;
      console.log(`🔄 Effect "${name}" ${enabled ? 'enabled' : 'disabled'}`);
    } else {
      console.warn(`⚠️ Effect "${name}" not found`);
    }
    return this;
  }

  /**
   * Obtiene un efecto registrado
   * @param {string} name - Nombre del efecto
   * @returns {Object|null} El efecto o null si no existe
   */
  getEffect(name) {
    const entry = this.effects.get(name);
    return entry ? entry.effect : null;
  }

  /**
   * Inicia el loop de animación
   */
  start() {
    if (this.isRunning) {
      console.warn('⚠️ Animation Controller already running');
      return;
    }
    
    this.isRunning = true;
    this.lastTime = performance.now();
    this.fpsUpdateTime = this.lastTime;
    this.frameCount = 0;
    this.tick(this.lastTime);
    
    console.log('▶️ Animation Controller started');
    return this;
  }

  /**
   * Detiene el loop
   */
  stop() {
    if (!this.isRunning) return;
    
    this.isRunning = false;
    if (this.frameId) {
      cancelAnimationFrame(this.frameId);
      this.frameId = null;
    }
    
    console.log('⏸️ Animation Controller stopped');
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

    // Calcula FPS cada segundo
    this.frameCount++;
    if (currentTime - this.fpsUpdateTime >= 1000) {
      this.fps = this.frameCount;
      this.frameCount = 0;
      this.fpsUpdateTime = currentTime;
    }

    // Ordena efectos por prioridad (mayor primero)
    const sortedEffects = Array.from(this.effects.entries())
      .filter(([_, entry]) => entry.enabled)
      .sort((a, b) => b[1].priority - a[1].priority);

    // Actualiza cada efecto habilitado
    sortedEffects.forEach(([name, entry]) => {
      try {
        // Verifica si el efecto tiene shouldUpdate y lo respeta
        const shouldUpdate = !entry.effect.shouldUpdate || 
                           entry.effect.shouldUpdate(currentTime, deltaTime);
        
        if (shouldUpdate) {
          entry.effect.update(deltaTime, currentTime);
          entry.updateCount++;
          entry.lastUpdateTime = currentTime;
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
   * Pausa automáticamente cuando la página no es visible
   * @private
   */
  setupVisibilityObserver() {
    document.addEventListener('visibilitychange', () => {
      if (document.hidden) {
        this.stop();
        console.log('👁️ Page hidden - animations paused');
      } else {
        this.start();
        console.log('👁️ Page visible - animations resumed');
      }
    });
  }

  /**
   * Respeta la preferencia de reduced motion del usuario
   * @private
   */
  setupReducedMotionObserver() {
    const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    
    const handleReducedMotion = (e) => {
      if (e.matches) {
        this.stop();
        console.log('♿ Reduced motion detected - animations disabled');
        document.body.classList.add('visual-engine-reduced-motion');
      } else {
        this.start();
        document.body.classList.remove('visual-engine-reduced-motion');
      }
    };

    mediaQuery.addEventListener('change', handleReducedMotion);
    handleReducedMotion(mediaQuery);
  }

  /**
   * Obtiene métricas de performance
   * @returns {Object} Estadísticas del controller
   */
  getStats() {
    const effectStats = {};
    this.effects.forEach((entry, name) => {
      effectStats[name] = {
        enabled: entry.enabled,
        priority: entry.priority,
        updateCount: entry.updateCount,
        lastUpdate: entry.lastUpdateTime
      };
    });

    return {
      fps: Math.round(this.fps),
      effectCount: this.effects.size,
      activeEffects: Array.from(this.effects.entries())
        .filter(([_, e]) => e.enabled)
        .length,
      isRunning: this.isRunning,
      effects: effectStats
    };
  }

  /**
   * Imprime estadísticas en consola (útil para debugging)
   */
  logStats() {
    const stats = this.getStats();
    console.log('📊 Animation Controller Stats:', stats);
    return this;
  }

  /**
   * Destruye el controller y limpia recursos
   */
  destroy() {
    this.stop();
    this.effects.clear();
    console.log('💥 Animation Controller destroyed');
  }
}

// Exportar como singleton global
if (typeof window !== 'undefined') {
  window.AnimationController = AnimationController;
  window.animationController = new AnimationController();
  
  // Debug helper - accesible desde la consola
  if (typeof window.VISUAL_CONFIG !== 'undefined' && window.VISUAL_CONFIG.debug?.showFPS) {
    setInterval(() => {
      const stats = window.animationController.getStats();
      console.log(`FPS: ${stats.fps} | Effects: ${stats.activeEffects}/${stats.effectCount}`);
    }, 2000);
  }
}

// Export para módulos ES6 si es necesario
if (typeof module !== 'undefined' && module.exports) {
  module.exports = AnimationController;
}
