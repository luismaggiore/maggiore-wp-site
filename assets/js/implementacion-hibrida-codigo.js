// ============================================================================
// IMPLEMENTACIÓN HÍBRIDA - CÓDIGO LISTO PARA USAR
// ============================================================================
// Mejora el sistema actual con mínimos cambios, máximo impacto
// Tiempo estimado: 1.5 semanas

// ============================================================================
// 1. ANIMATION CONTROLLER (Nuevo archivo: animation-controller.js)
// ============================================================================

/**
 * Controlador central que unifica todos los requestAnimationFrame
 * Evita tener múltiples RAF corriendo simultáneamente
 */
class AnimationController {
  constructor() {
    this.effects = new Map();
    this.isRunning = false;
    this.frameId = null;
    this.lastTime = 0;
    this.fps = 0;
    
    // IntersectionObserver para pausar cuando no visible
    this.setupVisibilityObserver();
  }

  /**
   * Registra un efecto para ser actualizado en cada frame
   * @param {string} name - Nombre único del efecto
   * @param {Object} effect - Objeto con método update(deltaTime)
   * @param {number} priority - Mayor = se ejecuta primero (default: 0)
   */
  register(name, effect, priority = 0) {
    if (this.effects.has(name)) {
      console.warn(`Effect "${name}" already registered`);
      return;
    }

    this.effects.set(name, { effect, priority, enabled: true });
    console.log(`✅ Registered effect: ${name}`);

    // Auto-start si es el primero
    if (this.effects.size === 1 && !this.isRunning) {
      this.start();
    }
  }

  /**
   * Desregistra un efecto
   */
  unregister(name) {
    this.effects.delete(name);
    if (this.effects.size === 0) {
      this.stop();
    }
  }

  /**
   * Habilita/deshabilita un efecto sin desregistrarlo
   */
  toggle(name, enabled) {
    const entry = this.effects.get(name);
    if (entry) {
      entry.enabled = enabled;
    }
  }

  /**
   * Inicia el loop de animación
   */
  start() {
    if (this.isRunning) return;
    
    this.isRunning = true;
    this.lastTime = performance.now();
    this.tick(this.lastTime);
    
    console.log('🎬 Animation Controller started');
  }

  /**
   * Detiene el loop
   */
  stop() {
    this.isRunning = false;
    if (this.frameId) {
      cancelAnimationFrame(this.frameId);
      this.frameId = null;
    }
    
    console.log('⏸️ Animation Controller stopped');
  }

  /**
   * Loop principal - UN SOLO RAF PARA TODO
   */
  tick(currentTime) {
    if (!this.isRunning) return;

    // Calcula delta time
    const deltaTime = (currentTime - this.lastTime) / 1000;
    this.lastTime = currentTime;

    // Calcula FPS
    this.fps = 1 / deltaTime;

    // Ordena efectos por prioridad
    const sortedEffects = Array.from(this.effects.entries())
      .filter(([_, entry]) => entry.enabled)
      .sort((a, b) => b[1].priority - a[1].priority);

    // Actualiza cada efecto
    sortedEffects.forEach(([name, entry]) => {
      try {
        // Verifica si el efecto tiene shouldUpdate y lo respeta
        const shouldUpdate = !entry.effect.shouldUpdate || 
                           entry.effect.shouldUpdate();
        
        if (shouldUpdate) {
          entry.effect.update(deltaTime, currentTime);
        }
      } catch (error) {
        console.error(`Error updating effect "${name}":`, error);
      }
    });

    // Siguiente frame
    this.frameId = requestAnimationFrame((t) => this.tick(t));
  }

  /**
   * Pausa automáticamente cuando la página no es visible
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
   * Obtiene métricas de performance
   */
  getStats() {
    return {
      fps: Math.round(this.fps),
      effectCount: this.effects.size,
      isRunning: this.isRunning
    };
  }
}

// Exportar como singleton
window.AnimationController = AnimationController;
window.animationController = new AnimationController();


// ============================================================================
// 2. CONFIGURATION (Nuevo archivo: visual-config.js)
// ============================================================================

/**
 * Configuración centralizada de todos los efectos visuales
 */
const VISUAL_CONFIG = {
  // Configuración Aurora
  aurora: {
    enabled: true,
    barCount: 18,
    palette: [
      '#00d0ff',
      '#00ffff',
      '#00ff99',
      '#00ff91',
      '#041e59',
      '#00b7ff'
    ],
    amplitudeX: 615,
    amplitudeScale: 0.2,
    speedX: 0.07,
    speedScale: 0.07,
    speedMultiplier: 1.1,
    blur: {
      min: 25,
      max: 160,
      responsive: true // Usa clamp en CSS
    }
  },

  // Configuración Constelación
  constellation: {
    enabled: true,
    maxDistance: 220,
    maxNeighbors: 2,
    lineStyle: {
      stroke: 'gray',
      strokeWidth: 0.9,
      opacity: 0.8
    },
    // Estados para morphing (las diferentes formas)
    states: {
      robinHood: [
        { cx: 300, cy: 250 },
        { cx: 240, cy: 130 },
        { cx: 300, cy: 80 },
        { cx: 300, cy: 410 },
        { cx: 140, cy: 510 },
        { cx: 360, cy: 130 },
        { cx: 460, cy: 510 }
      ],
      inteligencia: [
        { cx: 340, cy: 260 },
        { cx: 280, cy: 140 },
        { cx: 340, cy: 90 },
        { cx: 340, cy: 420 },
        { cx: 180, cy: 520 },
        { cx: 400, cy: 140 },
        { cx: 500, cy: 520 }
      ],
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

  // Configuración de Performance
  performance: {
    targetFPS: 55,
    enableMonitoring: true,
    pauseWhenHidden: true,
    respectReducedMotion: true
  },

  // Debugging
  debug: {
    showFPS: false, // Cambiar a true en desarrollo
    logEffects: true,
    showPerformanceWarnings: true
  }
};

window.VISUAL_CONFIG = VISUAL_CONFIG;


// ============================================================================
// 3. AURORA MEJORADA (Modificar aurora.js existente)
// ============================================================================

// Agregar al inicio del archivo después de los imports
const controller = window.animationController;
const config = window.VISUAL_CONFIG.aurora;

// Cambiar la variable COUNT por:
const COUNT = config.enabled ? config.barCount : 0;

// Reemplazar el loop de animación (líneas 174-190) por:

/**
 * NUEVO SISTEMA: Aurora se registra en el controller
 */
const auroraEffect = {
  name: 'aurora',
  isVisible: true,
  lastRenderTime: 0,
  renderInterval: 1000 / 60, // 60fps target

  // Verifica si debe actualizarse
  shouldUpdate() {
    return this.isVisible && config.enabled;
  },

  // Método de actualización llamado por el controller
  update(deltaTime, currentTime) {
    // Throttle: solo renderiza cada 16ms (~60fps)
    if (currentTime - this.lastRenderTime < this.renderInterval) {
      return;
    }
    this.lastRenderTime = currentTime;

    const t = currentTime / 1000;

    // Actualiza cada barra
    for (const b of bars) {
      const dx = Math.sin(t * b.speedX * 2 * Math.PI + b.phaseX) * b.ampX;
      const s = 1 + Math.sin(t * b.speedS * 2 * Math.PI + b.phaseS) * b.ampS;

      b.mesh.position.x = b.x0 + dx;
      b.mesh.scale.x = b.baseScaleX * s;
      b.mesh.scale.y = b.baseScaleY * s;
    }

    renderer.render(scene, camera);
  },

  // Setup de IntersectionObserver para pausar cuando no visible
  setupVisibilityObserver() {
    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach(entry => {
          this.isVisible = entry.isIntersecting;
          if (config.debug?.logEffects) {
            console.log(`Aurora ${this.isVisible ? 'visible' : 'hidden'}`);
          }
        });
      },
      { threshold: 0.1 }
    );

    const auroraElement = document.querySelector('.aurora');
    if (auroraElement) {
      observer.observe(auroraElement);
    }
  }
};

// Inicializar y registrar
auroraEffect.setupVisibilityObserver();
controller.register('aurora', auroraEffect, 1); // Priority 1

// Eliminar el requestAnimationFrame original (líneas 190)
// El controller ahora maneja el loop


// ============================================================================
// 4. CONSTELACIÓN MEJORADA (Modificar constelacion.js existente)
// ============================================================================

document.addEventListener("DOMContentLoaded", () => {
  if (!document.querySelector(".constelacion")) return;

  const svg = document.querySelector(".constelacion svg");
  if (!svg) return;

  const controller = window.animationController;
  const config = window.VISUAL_CONFIG.constellation;

  const MAX_DISTANCE = config.maxDistance;
  const NEIGHBORS = config.maxNeighbors;

  // Helper de distancia
  function distance(a, b) {
    const dx = a.cx - b.cx;
    const dy = a.cy - b.cy;
    return Math.sqrt(dx * dx + dy * dy);
  }

  // Grupo para líneas
  let lineGroup = svg.querySelector("g[data-constellation-lines]");
  if (!lineGroup) {
    lineGroup = document.createElementNS("http://www.w3.org/2000/svg", "g");
    lineGroup.setAttribute("data-constellation-lines", "true");
    svg.insertBefore(lineGroup, svg.firstChild);
  }

  // Cache de posiciones anteriores
  let previousPositions = null;
  let positionsChanged = false;

  /**
   * Detecta si las posiciones cambiaron significativamente
   */
  function checkPositionChanges() {
    const circles = Array.from(svg.querySelectorAll("circle"));
    const currentPositions = circles.map(c => ({
      cx: parseFloat(c.getAttribute("cx")),
      cy: parseFloat(c.getAttribute("cy"))
    }));

    if (!previousPositions) {
      previousPositions = currentPositions;
      return true;
    }

    // Verifica si alguna posición cambió más de 1px
    const changed = currentPositions.some((pos, i) => {
      const prev = previousPositions[i];
      return Math.abs(pos.cx - prev.cx) > 1 || 
             Math.abs(pos.cy - prev.cy) > 1;
    });

    if (changed) {
      previousPositions = currentPositions;
      positionsChanged = true;
    }

    return changed;
  }

  /**
   * Redibuja las líneas (solo cuando sea necesario)
   */
  function redraw() {
    const circles = Array.from(svg.querySelectorAll("circle"));

    const points = circles.map((c, index) => {
      const cx = parseFloat(c.getAttribute("cx"));
      const cy = parseFloat(c.getAttribute("cy"));
      return { index, cx, cy };
    });

    lineGroup.innerHTML = "";
    const drawnPairs = new Set();

    points.forEach((p) => {
      const nearest = points
        .filter((o) => o.index !== p.index)
        .map((o) => ({ o, d: distance(p, o) }))
        .filter((x) => x.d <= MAX_DISTANCE)
        .sort((a, b) => a.d - b.d)
        .slice(0, NEIGHBORS);

      nearest.forEach(({ o }) => {
        const a = Math.min(p.index, o.index);
        const b = Math.max(p.index, o.index);
        const key = `${a}:${b}`;
        if (drawnPairs.has(key)) return;
        drawnPairs.add(key);

        const line = document.createElementNS(
          "http://www.w3.org/2000/svg",
          "line"
        );
        line.setAttribute("x1", p.cx);
        line.setAttribute("y1", p.cy);
        line.setAttribute("x2", o.cx);
        line.setAttribute("y2", o.cy);
        line.setAttribute("stroke", config.lineStyle.stroke);
        line.setAttribute("stroke-width", config.lineStyle.strokeWidth);
        line.setAttribute("opacity", config.lineStyle.opacity);

        lineGroup.appendChild(line);
      });
    });

    positionsChanged = false; // Reset flag
  }

  /**
   * NUEVO SISTEMA: Constelación se registra en el controller
   */
  const constellationEffect = {
    name: 'constellation',
    dirty: true, // Forzar primer render

    // Solo actualiza si los puntos se movieron
    shouldUpdate() {
      if (this.dirty) return true;
      return checkPositionChanges();
    },

    // Método llamado por el controller
    update() {
      if (this.shouldUpdate()) {
        redraw();
      }
    },

    // Marca como "necesita actualización" (llamado por GSAP)
    markDirty() {
      this.dirty = true;
    }
  };

  // Registrar en el controller
  controller.register('constellation', constellationEffect, 2); // Priority 2 (antes que aurora)

  // Exponer globalmente para que GSAP pueda llamar markDirty()
  window.constellationEffect = constellationEffect;

  // Eliminar el requestAnimationFrame original
  // El controller ahora maneja el loop
});


// ============================================================================
// 5. MAIN.JS MEJORADO (Modificaciones al existente)
// ============================================================================

// Agregar al inicio del archivo:
const controller = window.animationController;
const constellation = window.constellationEffect;

// En la función que anima la constelación, agregar:
function animateToPositions(tl, positions, stagger = 0.03) {
  const dots = gsap.utils.toArray("#Layer_1 .dot");
  
  dots.forEach((dot, i) => {
    const p = positions[i];
    if (!p) return;

    tl.to(
      dot,
      {
        attr: { cx: p.cx, cy: p.cy },
        duration: 1,
        ease: "none",
        // ✅ CLAVE: Notificar a constelación que debe actualizar
        onUpdate: () => {
          if (constellation) {
            constellation.markDirty();
          }
        }
      },
      i * stagger
    );
  });
}

// En los ScrollTriggers, agregar callbacks:
const tlInteligencia = gsap.timeline({
  scrollTrigger: {
    trigger: "#inteligencia",
    start: "top center",
    end: "center center",
    scrub: true,
    invalidateOnRefresh: true,
    // ✅ Marcar dirty en cada update
    onUpdate: () => {
      if (constellation) constellation.markDirty();
    }
  }
});

// Lo mismo para tlFlexible y otros timelines...


// ============================================================================
// 6. FUNCTIONS.PHP CORREGIDO
// ============================================================================

/**
 * Enqueue de scripts - ORDEN CORRECTO
 */
function maggiore_scripts() {
  // CSS
  wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
  wp_enqueue_style('maggiore-style', get_stylesheet_uri());
  wp_enqueue_style('maggiore-main', get_template_directory_uri() . '/assets/css/main.css');
  wp_enqueue_style('bs-override', get_template_directory_uri() . '/assets/css/override.css');

  // JS externos
  wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], null, true);
  
  // GSAP
  wp_enqueue_script('gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js', [], null, true);
  wp_enqueue_script('gsap-scroll', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js', ['gsap'], null, true);
  wp_enqueue_script('gsap-smoother', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollSmoother.min.js', ['gsap-scroll'], null, true);
  wp_enqueue_script('gsap-scrollto', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollToPlugin.min.js', ['gsap'], null, true);
  wp_enqueue_script('gsap-splittext', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/SplitText.min.js', ['gsap'], null, true);
  wp_enqueue_script('gsap-textplugin', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/TextPlugin.min.js', ['gsap'], null, true);

  // ========================================================================
  // SISTEMA VISUAL - ORDEN CRÍTICO
  // ========================================================================
  
  // 1. CORE (primero) - Sin dependencias
  wp_enqueue_script(
    'visual-config',
    get_template_directory_uri() . '/assets/js/visual-config.js',
    [],
    '1.0.0',
    true
  );

  wp_enqueue_script(
    'animation-controller',
    get_template_directory_uri() . '/assets/js/animation-controller.js',
    ['visual-config'],
    '1.0.0',
    true
  );

  // 2. EFECTOS (dependen del controller)
  wp_enqueue_script(
    'aurora',
    get_template_directory_uri() . '/assets/js/aurora.js',
    ['animation-controller'], // ✅ Depende del controller
    '1.0.0',
    true
  );
  wp_script_add_data('aurora', 'type', 'module'); // ✅ ES6 Module

  wp_enqueue_script(
    'constellation-lines', // ✅ Handle único (no duplicado)
    get_template_directory_uri() . '/assets/js/constelacion.js',
    ['animation-controller'], // ✅ Depende del controller
    '1.0.0',
    true
  );

  // 3. MAIN (depende de todo lo anterior)
  wp_enqueue_script(
    'maggiore-main',
    get_template_directory_uri() . '/assets/js/main.js',
    ['gsap', 'gsap-scroll', 'constellation-lines'], // ✅ Dependencias explícitas
    '1.0.0',
    true
  );

  // 4. Otros scripts...
  wp_enqueue_script(
    'admin-media',
    get_template_directory_uri() . '/assets/js/admin-media.js',
    [],
    '1.0.0',
    true
  );

  // Teléfono internacional
  wp_enqueue_style('intl-tel-input', 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/css/intlTelInput.css');
  wp_enqueue_script('intl-tel-input', 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/intlTelInput.min.js', [], null, true);
  wp_enqueue_script('intl-tel-utils', 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js', ['intl-tel-input'], null, true);
  wp_enqueue_script('telefono', get_template_directory_uri() . '/assets/js/telefono.js', ['intl-tel-input'], '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'maggiore_scripts');


// ============================================================================
// 7. CSS ADICIONAL (Agregar a main.css)
// ============================================================================

/**
 * Modo Fallback - Si JS falla o está deshabilitado
 */
.visual-engine-fallback .aurora,
.visual-engine-fallback .overlay,
.visual-engine-fallback .overlay2 {
  display: none !important;
}

.visual-engine-fallback .constelacion {
  opacity: 0.3;
}

/**
 * Performance hints
 */
.aurora,
.overlay,
.overlay2,
.constelacion {
  will-change: transform, opacity;
  contain: layout style paint;
}

/**
 * Prefers-reduced-motion
 */
@media (prefers-reduced-motion: reduce) {
  .aurora,
  .constelacion {
    animation: none !important;
    transition: none !important;
  }
}


// ============================================================================
// 8. DEBUG PANEL (Opcional - para desarrollo)
// ============================================================================

/**
 * Panel de debugging para monitorear performance
 * Agregar este HTML en header.php solo en WP_DEBUG
 */
<?php if (WP_DEBUG): ?>
<div id="debug-panel" style="
  position: fixed;
  top: 10px;
  right: 10px;
  background: rgba(0,0,0,0.8);
  color: #0f0;
  padding: 10px;
  font-family: monospace;
  font-size: 12px;
  z-index: 9999;
  border-radius: 5px;
">
  <div id="debug-fps">FPS: --</div>
  <div id="debug-effects">Effects: --</div>
  <div id="debug-aurora">Aurora: --</div>
  <div id="debug-constellation">Constellation: --</div>
</div>

<script>
// Actualizar panel cada segundo
setInterval(() => {
  if (!window.animationController) return;
  
  const stats = animationController.getStats();
  document.getElementById('debug-fps').textContent = `FPS: ${stats.fps}`;
  document.getElementById('debug-effects').textContent = `Effects: ${stats.effectCount}`;
  
  // Estado de cada efecto
  const auroraEffect = animationController.effects.get('aurora');
  document.getElementById('debug-aurora').textContent = 
    `Aurora: ${auroraEffect?.enabled ? 'ON' : 'OFF'}`;
  
  const constEffect = animationController.effects.get('constellation');
  document.getElementById('debug-constellation').textContent = 
    `Constellation: ${constEffect?.enabled ? 'ON' : 'OFF'}`;
}, 1000);
</script>
<?php endif; ?>


// ============================================================================
// 9. TESTING CHECKLIST
// ============================================================================

/**
 * Verificar que todo funciona:
 * 
 * □ Console muestra: "✅ Registered effect: aurora"
 * □ Console muestra: "✅ Registered effect: constellation"  
 * □ Console muestra: "🎬 Animation Controller started"
 * □ Aurora se renderiza suavemente
 * □ Constelación conecta puntos correctamente
 * □ Al hacer scroll, la constelación cambia de forma
 * □ Al cambiar de pestaña, console muestra "animations paused"
 * □ Al volver, muestra "animations resumed"
 * □ FPS se mantiene > 50
 * □ No hay errores en console
 * □ Funciona en móvil (aunque con menor performance)
 */


// ============================================================================
// 10. PRÓXIMOS PASOS
// ============================================================================

/**
 * UNA VEZ QUE ESTO FUNCIONE:
 * 
 * 1. Agregar PerformanceMonitor
 *    - Detecta FPS bajos
 *    - Reduce calidad automáticamente
 * 
 * 2. Implementar lazy-loading
 *    - Aurora solo carga si scrolleas hacia abajo
 * 
 * 3. Agregar más efectos
 *    - Partículas
 *    - Parallax avanzado
 *    - Todo usando el mismo controller
 * 
 * 4. Panel de control en admin
 *    - Habilitar/deshabilitar efectos
 *    - Ajustar configuración
 */
