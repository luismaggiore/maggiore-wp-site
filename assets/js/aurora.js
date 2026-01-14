/**
 * Aurora Effect
 * Fondo animado tipo "aurora boreal" con barras verticales ondulantes
 * Versión optimizada con integración al Animation Controller
 * 
 * @version 2.0.0
 * @author Maggiore Marketing
 */

import * as THREE from "https://cdn.jsdelivr.net/npm/three@0.161.0/build/three.module.js";

// Esperar a que el controller y config estén disponibles
function initAurora() {
  const controller = window.animationController;
  const config = window.VISUAL_CONFIG?.aurora;

  if (!controller) {
    console.error('❌ Aurora: AnimationController not found');
    return;
  }

  if (!config || !config.enabled) {
    console.log('⚠️ Aurora disabled in config');
    return;
  }

  // Obtener canvas
  const canvas = document.getElementById("aurora");
  if (!canvas) {
    console.error('❌ Aurora: Canvas element #aurora not found');
    return;
  }

  // ============================================================
  // CONFIGURACIÓN desde VISUAL_CONFIG
  // ============================================================
  
  const COUNT = config.barCount;
  const PALETTE = config.palette.map(color => new THREE.Color(color));
  const AMP_X_MAX = config.amplitudeX;
  const SCALE_MAX = config.amplitudeScale;
  const SPEED_X_BASE = config.speedX;
  const SPEED_S_BASE = config.speedScale;
  const SPEED_MULT = config.speedMultiplier;
  const SPEED_JITTER = config.speedJitter;
  const AMP_JITTER = config.amplitudeJitter;
  const AMP_MULT = 1.0;

  // ============================================================
  // SCENE SETUP
  // ============================================================
  
  const renderer = new THREE.WebGLRenderer({
    canvas,
    antialias: true,
    alpha: true,
  });
  renderer.setPixelRatio(Math.min(2, window.devicePixelRatio));

  const scene = new THREE.Scene();

  // Orthographic camera para look plano tipo SVG
  const camera = new THREE.OrthographicCamera();
  scene.add(camera);

  function resize() {
    const w = document.documentElement.clientWidth;
    const h = window.innerHeight;

    renderer.setSize(w, h, false);

    camera.left = -w / 2;
    camera.right = w / 2;
    camera.top = h / 2;
    camera.bottom = -h / 2;
    camera.near = -1000;
    camera.far = 1000;
    camera.position.z = 10;
    camera.updateProjectionMatrix();
  }

  window.addEventListener("resize", resize);
  resize();

  // ============================================================
  // AURORA BAR MATERIAL
  // Shader con bordes suaves y gradiente vertical
  // ============================================================
  
  const auroraMat = new THREE.ShaderMaterial({
    transparent: true,
    depthWrite: false,
    blending: THREE.AdditiveBlending,
    uniforms: {
      uColor: { value: new THREE.Color("#00ffff") },
      uOpacity: { value: 0.75 },
    },
    vertexShader: `
      varying vec2 vUv;
      void main() {
        vUv = uv;
        gl_Position = projectionMatrix * modelViewMatrix * vec4(position, 1.0);
      }
    `,
    fragmentShader: `
      uniform vec3 uColor;
      uniform float uOpacity;
      varying vec2 vUv;

      float softEdge(float x, float a, float b) {
        return smoothstep(a, b, x) * (1.0 - smoothstep(1.0 - b, 1.0 - a, x));
      }

      void main() {
        // Soft horizontal falloff
        float edgeX = softEdge(vUv.x, 0.05, 0.45);

        // Vertical gradient - brighter near center
        float glowY = smoothstep(0.0, 0.6, vUv.y) * (1.0 - smoothstep(0.6, 1.0, vUv.y));
        glowY = pow(glowY * 1.8, 1.1);

        float alpha = edgeX * glowY * uOpacity;

        gl_FragColor = vec4(uColor, alpha);
      }
    `,
  });

  // ============================================================
  // CREATE BARS
  // ============================================================
  
  const bars = [];
  const geo = new THREE.PlaneGeometry(1, 1, 1, 1);

  for (let i = 0; i < COUNT; i++) {
    const mat = auroraMat.clone();
    mat.uniforms.uColor.value = PALETTE[i % PALETTE.length].clone();
    mat.uniforms.uOpacity.value = config.opacity.min + 
      Math.random() * (config.opacity.max - config.opacity.min);

    const mesh = new THREE.Mesh(geo, mat);

    // Dimensiones aleatorias
    const baseW = 290 + Math.random() * 20;
    const baseH = window.innerHeight * 4;

    mesh.scale.set(baseW, baseH, 1);

    // Posición inicial distribuida por pantalla
    const x0 = (Math.random() * 1.2 - 0.6) * window.innerWidth;
    const y0 = (Math.random() * 0.3 - 0.15) * window.innerHeight;

    mesh.position.set(x0, y0, 0);

    // Estado de animación por barra
    const ampX = AMP_X_MAX * AMP_MULT * (1 + (Math.random() * 2 - 1) * AMP_JITTER);
    const ampS = SCALE_MAX * AMP_MULT * (1 + (Math.random() * 2 - 1) * AMP_JITTER);

    const speedX = Math.max(
      0,
      SPEED_X_BASE * SPEED_MULT * (1 + (Math.random() * 2 - 1) * SPEED_JITTER)
    );
    const speedS = Math.max(
      0,
      SPEED_S_BASE * SPEED_MULT * (1 + (Math.random() * 2 - 1) * SPEED_JITTER)
    );

    const phaseX = Math.random() * Math.PI * 2;
    const phaseS = Math.random() * Math.PI * 2;

    bars.push({
      mesh,
      x0,
      baseScaleX: baseW,
      baseScaleY: baseH,
      ampX,
      ampS,
      speedX,
      speedS,
      phaseX,
      phaseS,
    });

    scene.add(mesh);
  }

  // ============================================================
  // AURORA EFFECT OBJECT (para el controller)
  // ============================================================
  
  const auroraEffect = {
    name: 'aurora',
    isVisible: true,
    lastRenderTime: 0,
    renderInterval: config.renderThrottle || 16,

    /**
     * Verifica si debe actualizarse
     * Solo renderiza si es visible y está habilitado
     */
    shouldUpdate(currentTime) {
      if (!this.isVisible || !config.enabled) {
        return false;
      }

      // Throttle: limita la frecuencia de renderizado
      if (currentTime - this.lastRenderTime < this.renderInterval) {
        return false;
      }

      return true;
    },

    /**
     * Método de actualización llamado por el controller
     * @param {number} deltaTime - Tiempo desde el último frame (segundos)
     * @param {number} currentTime - Tiempo actual (ms)
     */
    update(deltaTime, currentTime) {
      this.lastRenderTime = currentTime;
      const t = currentTime / 1000; // Convertir a segundos

      // Actualiza cada barra
      for (const b of bars) {
        const dx = Math.sin(t * b.speedX * 2 * Math.PI + b.phaseX) * b.ampX;
        const s = 1 + Math.sin(t * b.speedS * 2 * Math.PI + b.phaseS) * b.ampS;

        b.mesh.position.x = b.x0 + dx;
        b.mesh.scale.x = b.baseScaleX * s;
        b.mesh.scale.y = b.baseScaleY * s;
      }

      // Renderizar escena
      renderer.render(scene, camera);
    },

    /**
     * Setup de IntersectionObserver para pausar cuando no visible
     */
    setupVisibilityObserver() {
      const observer = new IntersectionObserver(
        (entries) => {
          entries.forEach(entry => {
            const wasVisible = this.isVisible;
            this.isVisible = entry.isIntersecting;
            
            if (config.debug?.logEffects && wasVisible !== this.isVisible) {
              console.log(`Aurora ${this.isVisible ? '👁️ visible' : '🙈 hidden'}`);
            }
          });
        },
        { threshold: 0.1 }
      );

      const auroraElement = document.querySelector('.aurora');
      if (auroraElement) {
        observer.observe(auroraElement);
      }
    },

    /**
     * Cleanup cuando se destruye
     */
    destroy() {
      // Limpiar geometrías y materiales
      bars.forEach(bar => {
        bar.mesh.geometry.dispose();
        bar.mesh.material.dispose();
        scene.remove(bar.mesh);
      });
      
      // Limpiar renderer
      renderer.dispose();
      
      console.log('💥 Aurora effect destroyed');
    }
  };

  // ============================================================
  // INICIALIZACIÓN
  // ============================================================
  
  // Setup del observer de visibilidad
  auroraEffect.setupVisibilityObserver();

  // Registrar en el controller con prioridad 1
  controller.register('aurora', auroraEffect, 1);

  console.log('✨ Aurora effect initialized with', COUNT, 'bars');

  // Exportar globalmente para debugging
  window.auroraEffect = auroraEffect;
}

// ============================================================
// AUTO-INIT
// ============================================================

// Esperar a que el DOM, controller y config estén listos
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    // Pequeño delay para asegurar que controller y config cargaron
    setTimeout(initAurora, 100);
  });
} else {
  setTimeout(initAurora, 100);
}
