function createDebugPanel() {
  if (document.getElementById("debug-panel")) return;
  const e = document.createElement("div");
  ((e.id = "debug-panel"),
    (e.innerHTML =
      '\n    <div class="debug-header">\n      <span>⚙️ Animation Debug</span>\n      <button id="debug-toggle">_</button>\n    </div>\n    <div class="debug-content">\n      <div class="debug-row">\n        <span class="debug-label">FPS:</span>\n        <span class="debug-value" id="debug-fps">--</span>\n      </div>\n      <div class="debug-row">\n        <span class="debug-label">Effects:</span>\n        <span class="debug-value" id="debug-effects">--</span>\n      </div>\n      <div class="debug-row">\n        <span class="debug-label">Aurora:</span>\n        <span class="debug-value" id="debug-aurora">--</span>\n      </div>\n      <div class="debug-row">\n        <span class="debug-label">Constellation:</span>\n        <span class="debug-value" id="debug-constellation">--</span>\n      </div>\n      <div class="debug-row">\n        <span class="debug-label">Points:</span>\n        <span class="debug-value" id="debug-points">--</span>\n      </div>\n      <div class="debug-row">\n        <span class="debug-label">Lines:</span>\n        <span class="debug-value" id="debug-lines">--</span>\n      </div>\n      <div class="debug-row">\n        <span class="debug-label">Running:</span>\n        <span class="debug-value" id="debug-running">--</span>\n      </div>\n    </div>\n  '));
  const n = document.createElement("style");
  ((n.textContent =
    "\n    #debug-panel {\n      position: fixed;\n      top: 10px;\n      right: 10px;\n      background: rgba(0, 0, 0, 0.9);\n      color: #0f0;\n      padding: 0;\n      font-family: 'Courier New', monospace;\n      font-size: 12px;\n      z-index: 999999;\n      border-radius: 8px;\n      box-shadow: 0 4px 20px rgba(0, 255, 0, 0.3);\n      min-width: 250px;\n      backdrop-filter: blur(10px);\n    }\n    .debug-header {\n      background: #0a0a0a;\n      padding: 10px;\n      border-radius: 8px 8px 0 0;\n      display: flex;\n      justify-content: space-between;\n      align-items: center;\n      border-bottom: 1px solid #0f0;\n    }\n    .debug-header button {\n      background: none;\n      border: 1px solid #0f0;\n      color: #0f0;\n      padding: 2px 8px;\n      cursor: pointer;\n      border-radius: 3px;\n      font-size: 14px;\n    }\n    .debug-header button:hover {\n      background: #0f0;\n      color: #000;\n    }\n    .debug-content {\n      padding: 10px;\n    }\n    .debug-row {\n      display: flex;\n      justify-content: space-between;\n      padding: 5px 0;\n      border-bottom: 1px solid #0f02;\n    }\n    .debug-row:last-child {\n      border-bottom: none;\n    }\n    .debug-label {\n      color: #0aa;\n      font-weight: bold;\n    }\n    .debug-value {\n      color: #0f0;\n    }\n    .debug-value.warning {\n      color: #ffa500;\n    }\n    .debug-value.error {\n      color: #f00;\n    }\n    .debug-value.success {\n      color: #0f0;\n    }\n  "),
    document.head.appendChild(n),
    document.body.appendChild(e),
    document.getElementById("debug-toggle").addEventListener("click", () => {
      const e = document.querySelector(".debug-content"),
        n = document.getElementById("debug-toggle");
      "none" === e.style.display
        ? ((e.style.display = "block"), (n.textContent = "_"))
        : ((e.style.display = "none"), (n.textContent = "+"));
    }),
    setInterval(updateDebugPanel, 1e3),
    console.log("✅ Debug panel created"));
}
function updateDebugPanel() {
  const e = window.animationController;
  if (!e) return;
  const n = e.getStats(),
    o = window.constellationEffect,
    t = window.auroraEffect,
    a = document.getElementById("debug-fps");
  a &&
    ((a.textContent = n.fps),
    (a.className =
      "debug-value " +
      (n.fps >= 55 ? "success" : n.fps >= 30 ? "warning" : "error")));
  const s = document.getElementById("debug-effects");
  s && (s.textContent = `${n.activeEffects}/${n.effectCount}`);
  const l = document.getElementById("debug-aurora");
  l &&
    t &&
    ((l.textContent = t.isVisible ? "👁️ ON" : "🙈 OFF"),
    (l.className = "debug-value " + (t.isVisible ? "success" : "warning")));
  const r = document.getElementById("debug-constellation");
  r &&
    o &&
    ((r.textContent = o.enabled ? "⭐ ON" : "⚫ OFF"),
    (r.className = "debug-value " + (o.enabled ? "success" : "warning")));
  const i = document.getElementById("debug-points");
  if (i && o) {
    const e = o.getStats();
    i.textContent = e.points;
  }
  const d = document.getElementById("debug-lines");
  if (d && o) {
    const e = o.getStats();
    d.textContent = e.lines;
  }
  const c = document.getElementById("debug-running");
  c &&
    ((c.textContent = n.isRunning ? "▶️ YES" : "⏸️ NO"),
    (c.className = "debug-value " + (n.isRunning ? "success" : "error")));
}
window.maggioreData?.isDebug &&
  window.addEventListener("load", createDebugPanel);
const MaggioreDebug = {
  stats() {
    console.table(window.animationController.getStats());
  },
  config() {
    console.log(window.VISUAL_CONFIG);
  },
  pause() {
    (window.animationController.stop(), console.log("⏸️ Animations paused"));
  },
  resume() {
    (window.animationController.start(), console.log("▶️ Animations resumed"));
  },
  disable(e) {
    (window.animationController.toggle(e, !1), console.log(`❌ ${e} disabled`));
  },
  enable(e) {
    (window.animationController.toggle(e, !0), console.log(`✅ ${e} enabled`));
  },
  effects() {
    const e = [];
    (window.animationController.effects.forEach((n, o) => {
      e.push({
        name: o,
        enabled: n.enabled,
        priority: n.priority,
        updates: n.updateCount,
      });
    }),
      console.table(e));
  },
  measureFPS(e = 5) {
    console.log(`📊 Measuring FPS for ${e} seconds...`);
    const n = [],
      o = setInterval(() => {
        n.push(window.animationController.fps);
      }, 100);
    setTimeout(() => {
      clearInterval(o);
      const e = n.reduce((e, n) => e + n, 0) / n.length,
        t = Math.min(...n),
        a = Math.max(...n);
      console.log(
        `\n        📊 FPS Results:\n        Average: ${Math.round(e)}\n        Min: ${Math.round(t)}\n        Max: ${Math.round(a)}\n        Samples: ${n.length}\n      `,
      );
    }, 1e3 * e);
  },
  updateConstellation() {
    (window.constellationEffect?.forceUpdate(),
      console.log("⭐ Constellation force updated"));
  },
  constellationStats() {
    console.table(window.constellationEffect?.getStats());
  },
  benchmark() {
    console.log("🏁 Running benchmark...");
    const e = performance.now();
    let n = 0;
    requestAnimationFrame(function o() {
      if ((n++, performance.now() - e < 3e3)) requestAnimationFrame(o);
      else {
        const e = n / 3;
        console.log(
          `\n          🏁 Benchmark Results:\n          Average FPS: ${Math.round(e)}\n          Total Frames: ${n}\n          ${e >= 55 ? "✅ Good" : e >= 30 ? "⚠️ Acceptable" : "❌ Poor"}\n        `,
        );
      }
    });
  },
  stressTest() {
    console.warn("⚠️ Running stress test - may cause lag");
    const e = window.VISUAL_CONFIG.aurora.barCount;
    ((window.VISUAL_CONFIG.aurora.barCount = 50),
      setTimeout(() => {
        ((window.VISUAL_CONFIG.aurora.barCount = e),
          console.log("✅ Stress test complete"));
      }, 5e3));
  },
  exportConfig() {
    const e = JSON.stringify(window.VISUAL_CONFIG, null, 2);
    (console.log("📋 Current configuration:"),
      console.log(e),
      navigator.clipboard.writeText(e).then(() => {
        console.log("✅ Configuration copied to clipboard");
      }));
  },
  help() {
    console.log(
      "\n      🎨 Maggiore Debug Commands:\n      \n      MaggioreDebug.stats()            - Ver estadísticas\n      MaggioreDebug.config()           - Ver configuración\n      MaggioreDebug.pause()            - Pausar animaciones\n      MaggioreDebug.resume()           - Reanudar animaciones\n      MaggioreDebug.disable('name')    - Deshabilitar efecto\n      MaggioreDebug.enable('name')     - Habilitar efecto\n      MaggioreDebug.effects()          - Ver todos los efectos\n      MaggioreDebug.measureFPS(5)      - Medir FPS por N segundos\n      MaggioreDebug.updateConstellation() - Forzar update\n      MaggioreDebug.constellationStats()  - Stats de constelación\n      MaggioreDebug.benchmark()        - Benchmark simple\n      MaggioreDebug.stressTest()       - Test de stress\n      MaggioreDebug.exportConfig()     - Exportar config\n      MaggioreDebug.help()             - Esta ayuda\n    ",
    );
  },
};
function runTests() {
  console.log("🧪 Running automated tests...\n");
  const e = {
    "Controller exists": () => void 0 !== window.animationController,
    "Config loaded": () => void 0 !== window.VISUAL_CONFIG,
    "Aurora effect registered": () =>
      window.animationController.effects.has("aurora"),
    "Constellation effect registered": () =>
      window.animationController.effects.has("constellation"),
    "Controller is running": () => window.animationController.isRunning,
    "FPS is acceptable": () => window.animationController.fps >= 30,
    "Aurora canvas exists": () => null !== document.getElementById("aurora"),
    "Constellation SVG exists": () =>
      null !== document.querySelector(".constelacion svg"),
    "GSAP loaded": () => "undefined" != typeof gsap,
    "ScrollTrigger loaded": () => "undefined" != typeof ScrollTrigger,
  };
  let n = 0,
    o = 0;
  return (
    Object.entries(e).forEach(([e, t]) => {
      try {
        t() ? (console.log(`✅ ${e}`), n++) : (console.error(`❌ ${e}`), o++);
      } catch (n) {
        (console.error(`❌ ${e} - Error: ${n.message}`), o++);
      }
    }),
    console.log(`\n📊 Test Results: ${n} passed, ${o} failed`),
    0 === o
      ? console.log(
          "%c✅ All tests passed!",
          "color: #0f0; font-weight: bold; font-size: 14px;",
        )
      : console.log(
          "%c⚠️ Some tests failed. Check errors above.",
          "color: #ffa500; font-weight: bold; font-size: 14px;",
        ),
    { passed: n, failed: o }
  );
}
function startPerformanceMonitor(e = 6e4) {
  console.log(`📊 Starting performance monitor for ${e / 1e3}s...`);
  const n = { fps: [], updateCounts: {}, timestamps: [] },
    o = setInterval(() => {
      const e = window.animationController.getStats();
      (n.fps.push(e.fps),
        n.timestamps.push(Date.now()),
        Object.entries(e.effects).forEach(([e, o]) => {
          (n.updateCounts[e] || (n.updateCounts[e] = []),
            n.updateCounts[e].push(o.updateCount));
        }));
    }, 1e3);
  setTimeout(() => {
    clearInterval(o);
    const t = n.fps.reduce((e, n) => e + n, 0) / n.fps.length,
      a = Math.min(...n.fps),
      s = Math.max(...n.fps);
    (console.log(
      `\n      📊 Performance Monitor Results (${e / 1e3}s):\n      \n      FPS:\n        Average: ${Math.round(t)}\n        Min: ${Math.round(a)}\n        Max: ${Math.round(s)}\n        \n      Effects Update Frequency:\n    `,
    ),
      Object.entries(n.updateCounts).forEach(([n, o]) => {
        const t = o[0],
          a = (o[o.length - 1] - t) / (e / 1e3);
        console.log(`  ${n}: ${Math.round(a)} updates/s`);
      }));
  }, e);
}
((window.MaggioreDebug = MaggioreDebug),
  window.maggioreData?.isDebug &&
    (console.log(
      "%c🎨 Maggiore Debug Tools Loaded",
      "color: #00d0ff; font-weight: bold; font-size: 16px;",
    ),
    console.log("Type MaggioreDebug.help() for available commands")),
  (window.runTests = runTests),
  (window.startPerformanceMonitor = startPerformanceMonitor));
const QuickFixes = {
  restart() {
    (console.log("🔄 Restarting animation system..."),
      window.animationController.stop(),
      setTimeout(() => {
        (window.animationController.start(), console.log("✅ Restarted"));
      }, 100));
  },
  fixConstellation() {
    console.log("🔧 Fixing constellation...");
    const e = window.constellationEffect;
    e
      ? ((e.dirty = !0), e.forceUpdate(), console.log("✅ Constellation fixed"))
      : console.error("❌ Constellation effect not found");
  },
  reduceQuality() {
    (console.log("🔧 Reducing quality for better performance..."),
      (window.VISUAL_CONFIG.aurora.barCount = 6),
      (window.VISUAL_CONFIG.aurora.renderThrottle = 32),
      console.log("✅ Quality reduced. Reload page to apply."));
  },
  restoreQuality() {
    (console.log("🔧 Restoring quality..."),
      (window.VISUAL_CONFIG.aurora.barCount = 18),
      (window.VISUAL_CONFIG.aurora.renderThrottle = 16),
      console.log("✅ Quality restored. Reload page to apply."));
  },
};
((window.QuickFixes = QuickFixes),
  console.log(
    "%c🛠️ Debug utilities loaded",
    "color: #00d0ff; font-weight: bold;",
  ),
  console.log("Available tools:"),
  console.log("  - MaggioreDebug (commands)"),
  console.log("  - QuickFixes (quick solutions)"),
  console.log("  - createDebugPanel() (visual panel)"),
  console.log("  - runTests() (automated tests)"),
  console.log("  - startPerformanceMonitor(60000) (monitor)"));
