/**
 * DEBUG & TESTING UTILITIES
 * Herramientas para debugging y testing del sistema de animaciones
 * 
 * Cómo usar:
 * 1. Pega estos códigos en la consola del navegador (F12)
 * 2. O agrégalos temporalmente a main.js para testing
 */

// ============================================================================
// 1. PANEL DE DEBUG VISUAL (HTML + CSS + JS)
// ============================================================================

/**
 * Agrega un panel de debug en la esquina superior derecha
 * Ejecutar en consola o agregar al final de main.js
 */
function createDebugPanel() {
  // Si ya existe, no crear otro
  if (document.getElementById('debug-panel')) return;

  // HTML del panel
  const panel = document.createElement('div');
  panel.id = 'debug-panel';
  panel.innerHTML = `
    <div class="debug-header">
      <span>⚙️ Animation Debug</span>
      <button id="debug-toggle">_</button>
    </div>
    <div class="debug-content">
      <div class="debug-row">
        <span class="debug-label">FPS:</span>
        <span class="debug-value" id="debug-fps">--</span>
      </div>
      <div class="debug-row">
        <span class="debug-label">Effects:</span>
        <span class="debug-value" id="debug-effects">--</span>
      </div>
      <div class="debug-row">
        <span class="debug-label">Aurora:</span>
        <span class="debug-value" id="debug-aurora">--</span>
      </div>
      <div class="debug-row">
        <span class="debug-label">Constellation:</span>
        <span class="debug-value" id="debug-constellation">--</span>
      </div>
      <div class="debug-row">
        <span class="debug-label">Points:</span>
        <span class="debug-value" id="debug-points">--</span>
      </div>
      <div class="debug-row">
        <span class="debug-label">Lines:</span>
        <span class="debug-value" id="debug-lines">--</span>
      </div>
      <div class="debug-row">
        <span class="debug-label">Running:</span>
        <span class="debug-value" id="debug-running">--</span>
      </div>
    </div>
  `;

  // CSS del panel
  const style = document.createElement('style');
  style.textContent = `
    #debug-panel {
      position: fixed;
      top: 10px;
      right: 10px;
      background: rgba(0, 0, 0, 0.9);
      color: #0f0;
      padding: 0;
      font-family: 'Courier New', monospace;
      font-size: 12px;
      z-index: 999999;
      border-radius: 8px;
      box-shadow: 0 4px 20px rgba(0, 255, 0, 0.3);
      min-width: 250px;
      backdrop-filter: blur(10px);
    }
    .debug-header {
      background: #0a0a0a;
      padding: 10px;
      border-radius: 8px 8px 0 0;
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 1px solid #0f0;
    }
    .debug-header button {
      background: none;
      border: 1px solid #0f0;
      color: #0f0;
      padding: 2px 8px;
      cursor: pointer;
      border-radius: 3px;
      font-size: 14px;
    }
    .debug-header button:hover {
      background: #0f0;
      color: #000;
    }
    .debug-content {
      padding: 10px;
    }
    .debug-row {
      display: flex;
      justify-content: space-between;
      padding: 5px 0;
      border-bottom: 1px solid #0f02;
    }
    .debug-row:last-child {
      border-bottom: none;
    }
    .debug-label {
      color: #0aa;
      font-weight: bold;
    }
    .debug-value {
      color: #0f0;
    }
    .debug-value.warning {
      color: #ffa500;
    }
    .debug-value.error {
      color: #f00;
    }
    .debug-value.success {
      color: #0f0;
    }
  `;

  document.head.appendChild(style);
  document.body.appendChild(panel);

  // Toggle collapse
  document.getElementById('debug-toggle').addEventListener('click', () => {
    const content = document.querySelector('.debug-content');
    const btn = document.getElementById('debug-toggle');
    if (content.style.display === 'none') {
      content.style.display = 'block';
      btn.textContent = '_';
    } else {
      content.style.display = 'none';
      btn.textContent = '+';
    }
  });

  // Actualizar cada segundo
  setInterval(updateDebugPanel, 1000);
  
  console.log('✅ Debug panel created');
}

function updateDebugPanel() {
  const controller = window.animationController;
  if (!controller) return;

  const stats = controller.getStats();
  const constellation = window.constellationEffect;
  const aurora = window.auroraEffect;

  // FPS
  const fpsEl = document.getElementById('debug-fps');
  if (fpsEl) {
    fpsEl.textContent = stats.fps;
    fpsEl.className = 'debug-value ' + 
      (stats.fps >= 55 ? 'success' : stats.fps >= 30 ? 'warning' : 'error');
  }

  // Effects
  const effectsEl = document.getElementById('debug-effects');
  if (effectsEl) {
    effectsEl.textContent = `${stats.activeEffects}/${stats.effectCount}`;
  }

  // Aurora
  const auroraEl = document.getElementById('debug-aurora');
  if (auroraEl && aurora) {
    auroraEl.textContent = aurora.isVisible ? '👁️ ON' : '🙈 OFF';
    auroraEl.className = 'debug-value ' + (aurora.isVisible ? 'success' : 'warning');
  }

  // Constellation
  const constEl = document.getElementById('debug-constellation');
  if (constEl && constellation) {
    constEl.textContent = constellation.enabled ? '⭐ ON' : '⚫ OFF';
    constEl.className = 'debug-value ' + (constellation.enabled ? 'success' : 'warning');
  }

  // Points
  const pointsEl = document.getElementById('debug-points');
  if (pointsEl && constellation) {
    const constStats = constellation.getStats();
    pointsEl.textContent = constStats.points;
  }

  // Lines
  const linesEl = document.getElementById('debug-lines');
  if (linesEl && constellation) {
    const constStats = constellation.getStats();
    linesEl.textContent = constStats.lines;
  }

  // Running
  const runningEl = document.getElementById('debug-running');
  if (runningEl) {
    runningEl.textContent = stats.isRunning ? '▶️ YES' : '⏸️ NO';
    runningEl.className = 'debug-value ' + (stats.isRunning ? 'success' : 'error');
  }
}

// Auto-inicializar si WP_DEBUG está activo
if (window.maggioreData?.isDebug) {
  window.addEventListener('load', createDebugPanel);
}


// ============================================================================
// 2. COMANDOS DE CONSOLA ÚTILES
// ============================================================================

/**
 * Colección de funciones útiles para debugging
 * Ejecutar directamente en consola
 */
const MaggioreDebug = {
  
  // Ver estadísticas completas
  stats() {
    console.table(window.animationController.getStats());
  },

  // Ver configuración actual
  config() {
    console.log(window.VISUAL_CONFIG);
  },

  // Pausar todas las animaciones
  pause() {
    window.animationController.stop();
    console.log('⏸️ Animations paused');
  },

  // Reanudar animaciones
  resume() {
    window.animationController.start();
    console.log('▶️ Animations resumed');
  },

  // Deshabilitar un efecto
  disable(effectName) {
    window.animationController.toggle(effectName, false);
    console.log(`❌ ${effectName} disabled`);
  },

  // Habilitar un efecto
  enable(effectName) {
    window.animationController.toggle(effectName, true);
    console.log(`✅ ${effectName} enabled`);
  },

  // Ver todos los efectos registrados
  effects() {
    const effects = [];
    window.animationController.effects.forEach((entry, name) => {
      effects.push({
        name,
        enabled: entry.enabled,
        priority: entry.priority,
        updates: entry.updateCount
      });
    });
    console.table(effects);
  },

  // Medir FPS durante N segundos
  measureFPS(seconds = 5) {
    console.log(`📊 Measuring FPS for ${seconds} seconds...`);
    const fps = [];
    const interval = setInterval(() => {
      fps.push(window.animationController.fps);
    }, 100);

    setTimeout(() => {
      clearInterval(interval);
      const avg = fps.reduce((a, b) => a + b, 0) / fps.length;
      const min = Math.min(...fps);
      const max = Math.max(...fps);
      
      console.log(`
        📊 FPS Results:
        Average: ${Math.round(avg)}
        Min: ${Math.round(min)}
        Max: ${Math.round(max)}
        Samples: ${fps.length}
      `);
    }, seconds * 1000);
  },

  // Forzar actualización de constelación
  updateConstellation() {
    window.constellationEffect?.forceUpdate();
    console.log('⭐ Constellation force updated');
  },

  // Ver estadísticas de constelación
  constellationStats() {
    console.table(window.constellationEffect?.getStats());
  },

  // Benchmarking simple
  benchmark() {
    console.log('🏁 Running benchmark...');
    const start = performance.now();
    let frames = 0;

    function count() {
      frames++;
      if (performance.now() - start < 3000) {
        requestAnimationFrame(count);
      } else {
        const fps = frames / 3;
        console.log(`
          🏁 Benchmark Results:
          Average FPS: ${Math.round(fps)}
          Total Frames: ${frames}
          ${fps >= 55 ? '✅ Good' : fps >= 30 ? '⚠️ Acceptable' : '❌ Poor'}
        `);
      }
    }
    requestAnimationFrame(count);
  },

  // Test de stress (crea muchos efectos)
  stressTest() {
    console.warn('⚠️ Running stress test - may cause lag');
    const originalCount = window.VISUAL_CONFIG.aurora.barCount;
    window.VISUAL_CONFIG.aurora.barCount = 50;
    
    setTimeout(() => {
      window.VISUAL_CONFIG.aurora.barCount = originalCount;
      console.log('✅ Stress test complete');
    }, 5000);
  },

  // Exportar configuración actual
  exportConfig() {
    const config = JSON.stringify(window.VISUAL_CONFIG, null, 2);
    console.log('📋 Current configuration:');
    console.log(config);
    
    // Copiar al clipboard
    navigator.clipboard.writeText(config).then(() => {
      console.log('✅ Configuration copied to clipboard');
    });
  },

  // Ayuda
  help() {
    console.log(`
      🎨 Maggiore Debug Commands:
      
      MaggioreDebug.stats()            - Ver estadísticas
      MaggioreDebug.config()           - Ver configuración
      MaggioreDebug.pause()            - Pausar animaciones
      MaggioreDebug.resume()           - Reanudar animaciones
      MaggioreDebug.disable('name')    - Deshabilitar efecto
      MaggioreDebug.enable('name')     - Habilitar efecto
      MaggioreDebug.effects()          - Ver todos los efectos
      MaggioreDebug.measureFPS(5)      - Medir FPS por N segundos
      MaggioreDebug.updateConstellation() - Forzar update
      MaggioreDebug.constellationStats()  - Stats de constelación
      MaggioreDebug.benchmark()        - Benchmark simple
      MaggioreDebug.stressTest()       - Test de stress
      MaggioreDebug.exportConfig()     - Exportar config
      MaggioreDebug.help()             - Esta ayuda
    `);
  }
};

// Exponer globalmente
window.MaggioreDebug = MaggioreDebug;

// Mostrar ayuda automáticamente en desarrollo
if (window.maggioreData?.isDebug) {
  console.log('%c🎨 Maggiore Debug Tools Loaded', 
    'color: #00d0ff; font-weight: bold; font-size: 16px;');
  console.log('Type MaggioreDebug.help() for available commands');
}


// ============================================================================
// 3. TESTS AUTOMATIZADOS
// ============================================================================

/**
 * Suite de tests para verificar que todo funciona
 * Ejecutar: runTests()
 */
function runTests() {
  console.log('🧪 Running automated tests...\n');
  
  const tests = {
    'Controller exists': () => {
      return window.animationController !== undefined;
    },
    'Config loaded': () => {
      return window.VISUAL_CONFIG !== undefined;
    },
    'Aurora effect registered': () => {
      return window.animationController.effects.has('aurora');
    },
    'Constellation effect registered': () => {
      return window.animationController.effects.has('constellation');
    },
    'Controller is running': () => {
      return window.animationController.isRunning;
    },
    'FPS is acceptable': () => {
      return window.animationController.fps >= 30;
    },
    'Aurora canvas exists': () => {
      return document.getElementById('aurora') !== null;
    },
    'Constellation SVG exists': () => {
      return document.querySelector('.constelacion svg') !== null;
    },
    'GSAP loaded': () => {
      return typeof gsap !== 'undefined';
    },
    'ScrollTrigger loaded': () => {
      return typeof ScrollTrigger !== 'undefined';
    }
  };

  let passed = 0;
  let failed = 0;

  Object.entries(tests).forEach(([name, test]) => {
    try {
      const result = test();
      if (result) {
        console.log(`✅ ${name}`);
        passed++;
      } else {
        console.error(`❌ ${name}`);
        failed++;
      }
    } catch (error) {
      console.error(`❌ ${name} - Error: ${error.message}`);
      failed++;
    }
  });

  console.log(`\n📊 Test Results: ${passed} passed, ${failed} failed`);
  
  if (failed === 0) {
    console.log('%c✅ All tests passed!', 
      'color: #0f0; font-weight: bold; font-size: 14px;');
  } else {
    console.log('%c⚠️ Some tests failed. Check errors above.', 
      'color: #ffa500; font-weight: bold; font-size: 14px;');
  }

  return { passed, failed };
}

window.runTests = runTests;


// ============================================================================
// 4. PERFORMANCE MONITOR
// ============================================================================

/**
 * Monitor de performance continuo
 * Ejecutar: startPerformanceMonitor()
 */
function startPerformanceMonitor(duration = 60000) {
  console.log(`📊 Starting performance monitor for ${duration/1000}s...`);
  
  const data = {
    fps: [],
    updateCounts: {},
    timestamps: []
  };

  const interval = setInterval(() => {
    const stats = window.animationController.getStats();
    data.fps.push(stats.fps);
    data.timestamps.push(Date.now());
    
    Object.entries(stats.effects).forEach(([name, effect]) => {
      if (!data.updateCounts[name]) {
        data.updateCounts[name] = [];
      }
      data.updateCounts[name].push(effect.updateCount);
    });
  }, 1000);

  setTimeout(() => {
    clearInterval(interval);
    
    // Análisis
    const avgFPS = data.fps.reduce((a, b) => a + b, 0) / data.fps.length;
    const minFPS = Math.min(...data.fps);
    const maxFPS = Math.max(...data.fps);
    
    console.log(`
      📊 Performance Monitor Results (${duration/1000}s):
      
      FPS:
        Average: ${Math.round(avgFPS)}
        Min: ${Math.round(minFPS)}
        Max: ${Math.round(maxFPS)}
        
      Effects Update Frequency:
    `);
    
    Object.entries(data.updateCounts).forEach(([name, counts]) => {
      const first = counts[0];
      const last = counts[counts.length - 1];
      const updatesPerSecond = (last - first) / (duration / 1000);
      console.log(`  ${name}: ${Math.round(updatesPerSecond)} updates/s`);
    });
    
  }, duration);
}

window.startPerformanceMonitor = startPerformanceMonitor;


// ============================================================================
// 5. QUICK FIXES
// ============================================================================

/**
 * Soluciones rápidas para problemas comunes
 */
const QuickFixes = {
  
  // Reiniciar todo
  restart() {
    console.log('🔄 Restarting animation system...');
    window.animationController.stop();
    setTimeout(() => {
      window.animationController.start();
      console.log('✅ Restarted');
    }, 100);
  },

  // Limpiar y reinicializar constelación
  fixConstellation() {
    console.log('🔧 Fixing constellation...');
    const constellation = window.constellationEffect;
    if (constellation) {
      constellation.dirty = true;
      constellation.forceUpdate();
      console.log('✅ Constellation fixed');
    } else {
      console.error('❌ Constellation effect not found');
    }
  },

  // Reducir calidad para mejor performance
  reduceQuality() {
    console.log('🔧 Reducing quality for better performance...');
    window.VISUAL_CONFIG.aurora.barCount = 6;
    window.VISUAL_CONFIG.aurora.renderThrottle = 32;
    console.log('✅ Quality reduced. Reload page to apply.');
  },

  // Restaurar calidad
  restoreQuality() {
    console.log('🔧 Restoring quality...');
    window.VISUAL_CONFIG.aurora.barCount = 18;
    window.VISUAL_CONFIG.aurora.renderThrottle = 16;
    console.log('✅ Quality restored. Reload page to apply.');
  }
};

window.QuickFixes = QuickFixes;


// ============================================================================
// AUTO-INICIALIZACIÓN
// ============================================================================

console.log('%c🛠️ Debug utilities loaded', 
  'color: #00d0ff; font-weight: bold;');
console.log('Available tools:');
console.log('  - MaggioreDebug (commands)');
console.log('  - QuickFixes (quick solutions)');
console.log('  - createDebugPanel() (visual panel)');
console.log('  - runTests() (automated tests)');
console.log('  - startPerformanceMonitor(60000) (monitor)');
