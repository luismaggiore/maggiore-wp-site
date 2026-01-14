/**
 * Constellation Effect
 * Conecta puntos SVG dinámicamente según proximidad
 * Versión optimizada con integración al Animation Controller
 * 
 * @version 2.0.0
 * @author Maggiore Marketing
 */

document.addEventListener("DOMContentLoaded", () => {
  // Verificar que el elemento existe
  if (!document.querySelector(".constelacion")) {
    console.log('⚠️ Constellation: .constelacion element not found');
    return;
  }

  const svg = document.querySelector(".constelacion svg");
  if (!svg) {
    console.error('❌ Constellation: SVG not found inside .constelacion');
    return;
  }

  // Obtener dependencias globales
  const controller = window.animationController;
  const config = window.VISUAL_CONFIG?.constellation;

  if (!controller) {
    console.error('❌ Constellation: AnimationController not found');
    return;
  }

  if (!config || !config.enabled) {
    console.log('⚠️ Constellation disabled in config');
    return;
  }

  // ============================================================
  // CONFIGURACIÓN
  // ============================================================
  
  const MAX_DISTANCE = config.maxDistance;
  const NEIGHBORS = config.maxNeighbors;
  const MOVEMENT_THRESHOLD = config.movementThreshold || 1;
  const LINE_STYLE = config.lineStyle;

  // ============================================================
  // HELPERS
  // ============================================================
  
  /**
   * Calcula distancia euclidiana entre dos puntos
   */
  function distance(a, b) {
    const dx = a.cx - b.cx;
    const dy = a.cy - b.cy;
    return Math.sqrt(dx * dx + dy * dy);
  }

  /**
   * Crea o obtiene el grupo de líneas
   */
  function getLineGroup() {
    let lineGroup = svg.querySelector("g[data-constellation-lines]");
    if (!lineGroup) {
      lineGroup = document.createElementNS("http://www.w3.org/2000/svg", "g");
      lineGroup.setAttribute("data-constellation-lines", "true");
      svg.insertBefore(lineGroup, svg.firstChild);
    }
    return lineGroup;
  }

  const lineGroup = getLineGroup();

  // ============================================================
  // POSITION TRACKING
  // ============================================================
  
  let previousPositions = null;
  let lastUpdateTime = 0;

  /**
   * Detecta si las posiciones cambiaron significativamente
   * @returns {boolean} true si cambió alguna posición
   */
  function checkPositionChanges() {
    const circles = Array.from(svg.querySelectorAll("circle"));
    
    const currentPositions = circles.map(c => ({
      cx: parseFloat(c.getAttribute("cx")),
      cy: parseFloat(c.getAttribute("cy"))
    }));

    // Primera vez: siempre actualizar
    if (!previousPositions) {
      previousPositions = currentPositions;
      return true;
    }

    // Verificar si alguna posición cambió más del threshold
    const changed = currentPositions.some((pos, i) => {
      const prev = previousPositions[i];
      if (!prev) return true;
      
      return Math.abs(pos.cx - prev.cx) > MOVEMENT_THRESHOLD || 
             Math.abs(pos.cy - prev.cy) > MOVEMENT_THRESHOLD;
    });

    if (changed) {
      previousPositions = currentPositions;
    }

    return changed;
  }

  // ============================================================
  // LINE DRAWING
  // ============================================================
  
  /**
   * Redibuja las líneas de conexión
   * Usa algoritmo de vecinos más cercanos
   */
  function redrawLines() {
    const circles = Array.from(svg.querySelectorAll("circle"));

    // Extraer posiciones actuales
    const points = circles.map((c, index) => {
      const cx = parseFloat(c.getAttribute("cx"));
      const cy = parseFloat(c.getAttribute("cy"));
      return { index, cx, cy };
    });

    // Limpiar líneas existentes
    lineGroup.innerHTML = "";
    
    // Set para evitar líneas duplicadas
    const drawnPairs = new Set();

    // Para cada punto, encontrar sus vecinos más cercanos
    points.forEach((p) => {
      // Calcular distancias a todos los otros puntos
      const nearest = points
        .filter((o) => o.index !== p.index)
        .map((o) => ({ o, d: distance(p, o) }))
        .filter((x) => x.d <= MAX_DISTANCE)
        .sort((a, b) => a.d - b.d)
        .slice(0, NEIGHBORS);

      // Dibujar líneas a los vecinos más cercanos
      nearest.forEach(({ o }) => {
        const a = Math.min(p.index, o.index);
        const b = Math.max(p.index, o.index);
        const key = `${a}:${b}`;
        
        // Evitar duplicados
        if (drawnPairs.has(key)) return;
        drawnPairs.add(key);

        // Crear línea SVG
        const line = document.createElementNS(
          "http://www.w3.org/2000/svg",
          "line"
        );
        
        line.setAttribute("x1", p.cx);
        line.setAttribute("y1", p.cy);
        line.setAttribute("x2", o.cx);
        line.setAttribute("y2", o.cy);
        line.setAttribute("stroke", LINE_STYLE.stroke);
        line.setAttribute("stroke-width", LINE_STYLE.strokeWidth);
        line.setAttribute("opacity", LINE_STYLE.opacity);

        lineGroup.appendChild(line);
      });
    });
  }

  // ============================================================
  // CONSTELLATION EFFECT OBJECT
  // ============================================================
  
  const constellationEffect = {
    name: 'constellation',
    dirty: true, // Forzar primer render
    enabled: true,
    updateCount: 0,

    /**
     * Determina si debe actualizarse
     * Solo actualiza si los puntos se movieron
     */
    shouldUpdate(currentTime) {
      // Si está marcado como dirty, actualizar
      if (this.dirty) {
        return true;
      }

      // Throttle: no revisar posiciones en cada frame
      // Solo cada 50ms para reducir overhead
      if (currentTime - lastUpdateTime < 50) {
        return false;
      }

      lastUpdateTime = currentTime;
      
      // Verificar si cambió posición
      return checkPositionChanges();
    },

    /**
     * Método de actualización llamado por el controller
     * @param {number} deltaTime - Tiempo desde el último frame (segundos)
     * @param {number} currentTime - Tiempo actual (ms)
     */
    update(deltaTime, currentTime) {
      redrawLines();
      this.dirty = false;
      this.updateCount++;
    },

    /**
     * Marca como "necesita actualización"
     * Llamado externamente (por GSAP) cuando se animan los puntos
     */
    markDirty() {
      this.dirty = true;
    },

    /**
     * Fuerza una actualización inmediata
     */
    forceUpdate() {
      this.dirty = true;
      redrawLines();
    },

    /**
     * Obtiene estadísticas
     */
    getStats() {
      const circles = svg.querySelectorAll("circle").length;
      const lines = lineGroup.querySelectorAll("line").length;
      
      return {
        points: circles,
        lines: lines,
        updateCount: this.updateCount
      };
    }
  };

  // ============================================================
  // INICIALIZACIÓN
  // ============================================================
  
  // Registrar en el controller con prioridad 2 (antes que aurora)
  controller.register('constellation', constellationEffect, 2);

  // Render inicial
  constellationEffect.forceUpdate();

  console.log('⭐ Constellation effect initialized');
  
  if (config.debug?.logEffects) {
    console.log('Constellation stats:', constellationEffect.getStats());
  }

  // ============================================================
  // EXPORT GLOBAL
  // ============================================================
  
  // Exportar globalmente para que GSAP pueda llamar markDirty()
  window.constellationEffect = constellationEffect;

  // Helper para debugging desde consola
  window.constellationStats = () => {
    const stats = constellationEffect.getStats();
    console.table(stats);
    return stats;
  };
});
