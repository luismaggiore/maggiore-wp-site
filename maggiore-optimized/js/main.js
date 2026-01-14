/**
 * MAIN.JS - Maggiore Theme
 * Script principal del tema
 * 
 * @version 2.0.0 (Optimized)
 * @author Maggiore Marketing
 * 
 * DEPENDENCIAS:
 * - GSAP (core)
 * - ScrollTrigger
 * - SplitText
 * - visual-config.js
 * - animation-controller.js
 * - visual-effects.js
 * 
 * ESTE ARCHIVO MANEJA:
 * - Animaciones GSAP
 * - Scroll effects
 * - Interacciones del usuario
 * - Morphing de constelación (estados)
 */

(function() {
  'use strict';

  // ============================================================
  // WAIT FOR DOM & DEPENDENCIES
  // ============================================================
  
  function waitForDependencies(callback) {
    const checkDeps = setInterval(() => {
      if (typeof gsap !== 'undefined' && 
          typeof ScrollTrigger !== 'undefined' &&
          window.VISUAL_CONFIG &&
          window.animationController) {
        clearInterval(checkDeps);
        callback();
      }
    }, 50);
    
    // Timeout después de 5 segundos
    setTimeout(() => {
      clearInterval(checkDeps);
      console.error('❌ Timeout waiting for dependencies');
    }, 5000);
  }

  // ============================================================
  // INITIALIZE
  // ============================================================
  
  document.addEventListener('DOMContentLoaded', () => {
    waitForDependencies(() => {
      initGSAP();
      initScrollAnimations();
      initInteractions();
      initConstellationMorphing();
      
      if (window.VISUAL_CONFIG?.debug.logEffects) {
        console.log('✅ Main.js initialized');
      }
    });
  });

  // ============================================================
  // GSAP CONFIGURATION
  // ============================================================
  
  function initGSAP() {
    gsap.registerPlugin(ScrollTrigger);
    
    // Configuración global de ScrollTrigger
    ScrollTrigger.defaults({
      markers: window.VISUAL_CONFIG?.scrollAnimations.markers || false,
      toggleActions: 'play none none none'
    });
    
    // Refresh al redimensionar
    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => {
        ScrollTrigger.refresh();
      }, 250);
    });
  }

  // ============================================================
  // SCROLL ANIMATIONS
  // ============================================================
  
  function initScrollAnimations() {
    // Hero title animation
    animateHeroTitle();
    
    // Section fade-ins
    animateSections();
    
    // Text reveals
    animateTextReveals();
  }

  /**
   * Anima el título del hero
   */
  function animateHeroTitle() {
    const heroTitle = document.querySelector('.hero h1, .hero .display-1');
    if (!heroTitle) return;

    gsap.from(heroTitle, {
      opacity: 0,
      y: 50,
      duration: 1,
      ease: 'power3.out',
      scrollTrigger: {
        trigger: heroTitle,
        start: 'top 80%'
      }
    });
  }

  /**
   * Anima las secciones al hacer scroll
   */
  function animateSections() {
    const sections = document.querySelectorAll('section, .section');
    
    sections.forEach(section => {
      gsap.from(section, {
        opacity: 0,
        y: 30,
        duration: 0.8,
        ease: 'power2.out',
        scrollTrigger: {
          trigger: section,
          start: 'top 85%',
          once: true
        }
      });
    });
  }

  /**
   * Anima textos con reveal effect
   */
  function animateTextReveals() {
    const reveals = document.querySelectorAll('[data-reveal]');
    
    reveals.forEach(element => {
      const splitText = new SplitText(element, { 
        type: window.VISUAL_CONFIG?.scrollAnimations.textAnimations.splitType || 'words'
      });

      gsap.from(splitText.words, {
        opacity: 0,
        y: 20,
        stagger: window.VISUAL_CONFIG?.scrollAnimations.textAnimations.stagger || 0.03,
        duration: window.VISUAL_CONFIG?.scrollAnimations.textAnimations.duration || 0.8,
        ease: window.VISUAL_CONFIG?.scrollAnimations.textAnimations.ease || 'power2.out',
        scrollTrigger: {
          trigger: element,
          start: 'top 80%',
          once: true
        }
      });
    });
  }

  // ============================================================
  // CONSTELLATION MORPHING (Estados del SVG)
  // ============================================================
  
  function initConstellationMorphing() {
    if (!window.VISUAL_CONFIG?.constellation.enableMorphing) return;
    if (!document.querySelector('.constelacion')) return;

    const svg = document.querySelector('.constelacion svg');
    if (!svg) return;

    const dots = Array.from(svg.querySelectorAll('circle'));
    if (dots.length === 0) return;

    // Definir posiciones para cada estado/sección
    const positions = {
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
    };

    /**
     * Anima a un conjunto de posiciones
     */
    function animateToPositions(timeline, targetPositions, stagger = 0.03) {
      dots.forEach((dot, i) => {
        const pos = targetPositions[i];
        if (!pos) return;

        timeline.to(dot, {
          attr: { cx: pos.cx, cy: pos.cy },
          duration: 1,
          ease: 'power2.inOut'
        }, i * stagger);
      });
    }

    /**
     * Anima los gráficos adicionales (arco, ajedrez, infinito)
     */
    function showGraphic(timeline, hideSelector, showSelector) {
      if (hideSelector) {
        timeline.to(hideSelector, {
          autoAlpha: 0,
          duration: 0.3
        });
      }

      if (showSelector) {
        timeline.from(showSelector, {
          autoAlpha: 0,
          duration: 0.3
        });
      }
    }

    // Timeline para sección "inteligencia"
    const tlInteligencia = gsap.timeline({
      scrollTrigger: {
        trigger: '#inteligencia',
        start: 'top center',
        end: 'center center',
        scrub: true,
        invalidateOnRefresh: true
      }
    });

    animateToPositions(tlInteligencia, positions.inteligencia);
    showGraphic(tlInteligencia, '.arco', '.ajedrez');

    // Timeline para sección "flexible"
    const tlFlexible = gsap.timeline({
      scrollTrigger: {
        trigger: '#flexible',
        start: 'top center',
        end: 'center center',
        scrub: true,
        immediateRender: false,
        invalidateOnRefresh: true
      }
    });

    animateToPositions(tlFlexible, positions.flexible);
    showGraphic(tlFlexible, '.ajedrez', '.infinito');

    // Forzar actualización de constelación después de cada morphing
    tlInteligencia.call(() => {
      if (window.constellationEffect) {
        window.constellationEffect.forceUpdate();
      }
    });

    tlFlexible.call(() => {
      if (window.constellationEffect) {
        window.constellationEffect.forceUpdate();
      }
    });

    if (window.VISUAL_CONFIG?.debug.logEffects) {
      console.log('✅ Constellation morphing initialized');
    }
  }

  // ============================================================
  // INTERACTIVE ELEMENTS
  // ============================================================
  
  function initInteractions() {
    // Smooth scroll para links anchor
    initSmoothScroll();
    
    // Tooltips de Bootstrap (si existen)
    initTooltips();
    
    // Otros eventos interactivos
    initCustomInteractions();
  }

  /**
   * Smooth scroll para links internos
   */
  function initSmoothScroll() {
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        const href = this.getAttribute('href');
        
        // Ignorar # vacío
        if (href === '#') return;
        
        const target = document.querySelector(href);
        if (!target) return;
        
        e.preventDefault();
        
        gsap.to(window, {
          duration: 1,
          scrollTo: {
            y: target,
            offsetY: 80 // Offset para navbar
          },
          ease: 'power2.inOut'
        });
      });
    });
  }

  /**
   * Inicializa tooltips de Bootstrap
   */
  function initTooltips() {
    if (typeof bootstrap === 'undefined') return;
    
    const tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    
    tooltipTriggerList.map(tooltipTriggerEl => {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
  }

  /**
   * Interacciones personalizadas
   */
  function initCustomInteractions() {
    // Desglose de palabras clave (si existe la función)
    if (typeof desglocesClave === 'function') {
      desglocesClave();
    }

    // Hover effects en cards
    const cards = document.querySelectorAll('.card, .card-hover');
    cards.forEach(card => {
      card.addEventListener('mouseenter', function() {
        gsap.to(this, {
          y: -5,
          duration: 0.3,
          ease: 'power2.out'
        });
      });

      card.addEventListener('mouseleave', function() {
        gsap.to(this, {
          y: 0,
          duration: 0.3,
          ease: 'power2.out'
        });
      });
    });
  }

  // ============================================================
  // DESGLOCES CLAVE (Helper para home)
  // ============================================================
  
  function desglocesClave() {
    if (!document.querySelector('.hero')) return;

    const keyWords = [
      {
        id: 'words1',
        info: 'Pensamos: inteligencia de mercado, estrategia fundamentada'
      },
      {
        id: 'words3',
        info: 'Maggiore significa mayor. Nuestra promesa es crecimiento para tu empresa'
      },
      {
        id: 'words7',
        info: 'Respiramos marketing y branding. Creamos marcas desde cero y hacemos que las marcas ya grandes sean aún mayores.'
      },
      {
        id: 'words12',
        info: 'Alto: Utilizamos la inteligencia de mercados y el marketing digital como motores de crecimiento para tu negocio.'
      }
    ];

    keyWords.forEach(({ id, info }) => {
      const element = document.getElementById(id);
      if (!element) return;

      element.style.cursor = 'pointer';

      element.addEventListener('click', function() {
        // Crear tooltip o mostrar info de alguna forma
        console.log(info);
        
        // Podrías usar un modal, tooltip, o alert
        // Ejemplo simple:
        const infoBox = document.createElement('div');
        infoBox.className = 'key-word-info';
        infoBox.textContent = info;
        infoBox.style.cssText = `
          position: fixed;
          bottom: 20px;
          right: 20px;
          background: rgba(0, 208, 255, 0.95);
          color: white;
          padding: 20px;
          border-radius: 8px;
          max-width: 300px;
          z-index: 9999;
          animation: slideIn 0.3s ease;
        `;

        document.body.appendChild(infoBox);

        setTimeout(() => {
          infoBox.remove();
        }, 5000);
      });
    });
  }

  // ============================================================
  // UTILITY FUNCTIONS
  // ============================================================
  
  /**
   * Debounce helper
   */
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  /**
   * Throttle helper
   */
  function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
      if (!inThrottle) {
        func.apply(this, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }

  // Exportar utilidades globalmente si es necesario
  window.maggioreUtils = {
    debounce,
    throttle
  };

})();
