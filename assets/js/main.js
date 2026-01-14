document.addEventListener("DOMContentLoaded", (event) => {
  gsap.registerPlugin(
    ScrollTrigger,
    ScrollSmoother,
    ScrollToPlugin,
    SplitText,
    TextPlugin
  );
  // gsap code here!

  if (document.querySelector(".copiar-correo")) {
    document.querySelectorAll(".copiar-correo").forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();

        const correo = this.getAttribute("data-correo");

        navigator.clipboard
          .writeText(correo)
          .then(() => {
            // Feedback opcional
            const textoOriginal = this.innerHTML;
            this.innerHTML = '<i class="bi bi-check"></i> Copiado';

            setTimeout(() => {
              this.innerHTML = textoOriginal;
            }, 1500);
          })
          .catch((err) => {
            console.error("Error al copiar:", err);
          });
      });
    });
  }

  const smoother = ScrollSmoother.create({
    wrapper: "#smooth-wrapper",
    content: "#smooth-content",
    smooth: 1.2,
    effects: true,
  });

  if (
    document.querySelector(".blog-sidebar") &&
    document.querySelector(".blog-mainbar")
  ) {
    const mm = gsap.matchMedia();

    // Solo en dispositivos grandes
    mm.add("(min-width: 992px)", () => {
      const sidebar = document.querySelector(".blog-sidebar");
      const content = document.querySelector(".blog-mainbar");

      // Solo hacer pin si el contenido es más largo que el sidebar
      if (content.offsetHeight > sidebar.offsetHeight) {
        ScrollTrigger.create({
          trigger: content,
          pin: sidebar,
          start: "top top+=100", // Empieza a pinear 100px después del top
          end: "bottom bottom", // Se despinea cuando termina el contenido
          pinSpacing: false, // No agrega espacio extra
          // Si usas ScrollSmoother:
          scroller: document.querySelector("#smooth-wrapper")
            ? "#smooth-wrapper"
            : window,
        });
      }
    });
  }
  function makeHoverableCards({
    cardSelector,
    linkSelector,
    hoverableClass = "hoverable",
    hoverClass = "is-hover",
  }) {
    // Si no existe al menos una card o un link, no hacemos nada
    if (
      !document.querySelector(cardSelector) ||
      !document.querySelector(linkSelector)
    )
      return;

    const cards = Array.from(document.querySelectorAll(cardSelector)).filter(
      (card) => card.querySelector(linkSelector)
    );

    cards.forEach((card) => {
      card.classList.add(hoverableClass);

      const on = () => card.classList.add(hoverClass);
      const off = () => card.classList.remove(hoverClass);

      card.addEventListener("pointerenter", on);
      card.addEventListener("pointerleave", off);

      // accesibilidad (tab)
      card.addEventListener("focusin", on);
      card.addEventListener("focusout", off);
    });
  }

  // Ejemplo 1: lo mismo que tenías
  makeHoverableCards({
    cardSelector: ".card-mg",
    linkSelector: ".stretched-link",
    hoverableClass: "hoverable",
    hoverClass: "is-hover",
  });

  // Ejemplo 2: otra variante con clases distintas
  makeHoverableCards({
    cardSelector: ".blog-article",
    linkSelector: ".stretched-link",
    hoverableClass: "hoverable",
    hoverClass: "is-hover",
  });

  if (document.querySelector(".scroll-nav")) {
    const sectionsIDs = [];

    function getIDs() {
      sectionsIDs.push(document.querySelector(".hero").id);
      const secciones = document.querySelectorAll(".separador");
      secciones.forEach((s) => {
        sectionsIDs.push(s.id);
      });
    }
    getIDs();
    // crear dots
    sectionsIDs.forEach((s) => {
      let navLink = document.createElement("a");
      navLink.classList.add("nav-dot");
      navLink.href = "#" + s;
      document.querySelector(".div-dot").appendChild(navLink);
    });

    const dots = document.querySelectorAll(".nav-dot");
    const dotsArray = Array.from(dots);

    function setActiveDot(index) {
      dots.forEach((d) => d.classList.remove("active"));
      dots[index].classList.add("active");
    }

    function goToIndex(index) {
      const target = "#" + sectionsIDs[index];
      smoother.scrollTo(target, {
        duration: 1,
        ease: "power2.out",
      });
      setActiveDot(index);
    }

    // click dots
    sectionsIDs.forEach((s, index) => {
      dots[index].addEventListener("click", (e) => {
        e.preventDefault();
        goToIndex(index);
      });
    });

    // botones prev / next
    const prevBtn = document.querySelector(".scroll-nav .prev");
    const nextBtn = document.querySelector(".scroll-nav .next");

    prevBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const currentIndex = dotsArray.findIndex((d) =>
        d.classList.contains("active")
      );
      goToIndex(Math.max(0, currentIndex - 1));
    });

    nextBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const currentIndex = dotsArray.findIndex((d) =>
        d.classList.contains("active")
      );
      goToIndex(Math.min(dotsArray.length - 1, currentIndex + 1));
    });

    // ✅ activar dot al hacer scroll manual (ScrollTrigger)
    sectionsIDs.forEach((id, index) => {
      ScrollTrigger.create({
        trigger: "#" + id,
        start: "top center", // cuando el top de la sección llega al centro
        end: "bottom center",
        onEnter: () => setActiveDot(index),
        onEnterBack: () => setActiveDot(index),
        // importante si usas ScrollSmoother:
        scroller: smoother.wrapper(),
      });
    });
  }

  if (document.querySelectorAll(".bajada-reveal")) {
    gsap.from(".bajada-reveal", {
      x: 20,
      autoAlpha: 0,
      delay: 1.4,
      duration: 0.6,
      ease: "power1.out",
    });
  }

  if (document.querySelector(".text-appear")) {
    document.fonts.ready.then(() => {
      let splitText = SplitText.create(".text-appear", {
        type: "words,chars,lines",
      });

      let words = splitText.words; //an array of all the divs that wrap each character

      gsap.from(words, {
        scrollTrigger: {
          trigger: ".text-appear",
          toggleActions: "play reverse play reverse",
        },
        x: 60,
        autoAlpha: 0,
        ease: "power1.out",
        stagger: {
          amount: 0.4,
        },
      });
    });
  }
  //
  // Sticky Sidebar para single posts

  gsap.from(".brand-name", {
    autoAlpha: 0,
    y: 100,
    duration: 1,
    ease: "power1.out",
  });
  if (document.querySelector(".title-reveal")) {
    document.fonts.ready.then(() => {
      let mySplitText = SplitText.create(".title-reveal", {
        type: "words,lines",
        linesClass: "lines",
        wordsClass: "words++",
      });

      let chars = mySplitText.words;

      gsap.from(chars, {
        duration: 0.6,
        autoAlpha: 0,
        delay: 1,
        x: 20,
        ease: "power1.out",
        stagger: {
          amount: 0.6,
        },
        onComplete: () => {
          // ✅ aquí tu función
          desglocesClave();
        },
      });
    });
  }

  const mm = gsap.matchMedia();

  mm.add("(min-width: 768px)", () => {
    ScrollTrigger.create({
      trigger: "#sectionThree",
      pin: ".testimonial-title", // el elemento que se queda fijo
      start: "top 40% ", // cuando el top de la sección llega al centro de la pantalla
      end: "bottom 80%", // hasta que el bottom llegue al centro
      // markers: true,            // descomenta para depurar
      // pinSpacing: true          // por defecto true; deja espacio para que se "despinee" suave
    });

    gsap.to(".move", {
      scrollTrigger: {
        trigger: ".hero",
        start: "top 0%",
        end: "bottom -10%",
        scrub: true,
      },
      y: 150,
    });

    if (document.querySelector(".testimonial-slide")) {
      gsap.utils.toArray(".testimonial-slide").forEach((el) => {
        gsap.fromTo(
          el,
          { x: 200, opacity: 0 }, // Estado inicial (fuera, derecha)
          {
            x: 0,
            y: 0,
            opacity: 1,
            ease: "none",
            scrollTrigger: {
              trigger: el,
              start: "top 80%",
              end: "bottom 80%",
              scrub: true,
              toggleActions: "play reverse play reverse",
            },
          }
        );
      });
    }

    // opcional: retorna cleanup si se cambia el tamaño
    return () => {
      ScrollTrigger.getAll().forEach((st) => st.kill());
    };
  });

  gsap.from(".testimonial-title", {
    scrollTrigger: {
      trigger: ".testimonial-title",
      start: "top bottom",
      end: "bottom center",
      scrub: true,
    },

    autoAlpha: 0,
  });

  ScrollTrigger.create({
    trigger: "#robinHood", // el pin empieza cuando esta sección llegue a la posición indicada
    endTrigger: "#flexible", // el pin termina cuando ESTA sección llegue a la posición indicada
    pin: ".constelacion",
    start: "center center", // cuando el centro de #robinHood llega al centro del viewport
    end: "center center", // cuando el centro de #flexible llega al centro del viewport
    // markers: true,
    // pinSpacing: true
  });

  if (document.querySelectorAll(".hr-mg")) {
    gsap.utils.toArray(".hr-mg").forEach((el) => {
      gsap.from(el, {
        maxWidth: 0,
        duration: 0.5,
        ease: "power1.out",
        scrollTrigger: {
          trigger: el,
          toggleActions: "play reverse play reverse",
        },
      });
    });
  }

  if (document.getElementById("features")) {
    gsap.utils.toArray(".feature-name").forEach((el) => {
      gsap.from(el, {
        y: 40,
        autoAlpha: 0,
        ease: "expo.out",
        duration: 4,
        scrollTrigger: {
          trigger: el,
          toggleActions: "play reverse play reverse",
        },
      });
    });

    gsap.utils.toArray(".title-feature").forEach((el) => {
      gsap.from(el, {
        x: 100,
        autoAlpha: 0,
        ease: "expo.out",
        duration: 2,
        scrollTrigger: {
          trigger: el,
          toggleActions: "play reverse play reverse",
        },
      });

      let mySplitText = SplitText.create(el, {
        type: "words",
      });
      let chars = mySplitText.words; //an array of all the divs that wrap each character

      gsap.from(chars, {
        color: "#537379ff",
        ease: "expo.out",
        duration: 0.5,
        scrollTrigger: {
          trigger: el,
          toggleActions: "play reverse play reverse",
        },
        stagger: {
          amount: 1,
        },
      });
    });
  }

  if (document.querySelector(".blog-mg")) {
    gsap.from(".blog-mg", {
      scrollTrigger: {
        trigger: ".blog-mg",
        toggleActions: "play reverse play reverse",
      },
      x: 200,
      autoAlpha: 0,
      ease: "expo.out",
      duration: 2,
      stagger: {
        amount: 0.4,
      },
    });
  }

  if (document.querySelector(".constelacion")) {
    const dots = gsap.utils.toArray("#Layer_1 .dot");

    // formación A (cuando entras a #inteligencia)
    const positionsInteligencia = [
      { cx: 340, cy: 260 },
      { cx: 280, cy: 140 },
      { cx: 340, cy: 90 },
      { cx: 340, cy: 420 },
      { cx: 180, cy: 520 },
      { cx: 400, cy: 140 },
      { cx: 500, cy: 520 },
    ];

    // formación B (cuando entras a #flexible)
    const positionsFlexible = [
      { cx: 50, cy: 290 },
      { cx: 200, cy: 390 },
      { cx: 300, cy: 290 },
      { cx: 150, cy: 190 },
      { cx: 450, cy: 390 },
      { cx: 400, cy: 190 },
      { cx: 550, cy: 290 },
    ];

    function animateToPositions(tl, positions, stagger = 0.03) {
      dots.forEach((dot, i) => {
        const p = positions[i];
        if (!p) return;

        tl.to(
          dot,
          {
            attr: { cx: p.cx, cy: p.cy },
            duration: 1,
            ease: "none",
          },
          i * stagger
        );
      });
    }

    function arco(tl) {
      tl.to(".arco", {
        autoAlpha: 0,
      });

      tl.from(".ajedrez", {
        autoAlpha: 0,
      });
    }

    function infinito(tl) {
      tl.to(".ajedrez", {
        autoAlpha: 0,
      });

      tl.from(".infinito", {
        autoAlpha: 0,
      });
    }

    // --- 1) Trigger INTELIGENCIA ---
    const tlInteligencia = gsap.timeline({
      scrollTrigger: {
        trigger: "#inteligencia",
        start: "top center",
        end: "center center",
        scrub: true,
        // muy importante para evitar “saltos” al volver hacia arriba:
        invalidateOnRefresh: true,
      },
    });

    animateToPositions(tlInteligencia, positionsInteligencia, 0.03);
    arco(tlInteligencia);
    const tlFlexible = gsap.timeline({
      scrollTrigger: {
        trigger: "#flexible",
        start: "top center",
        end: "center center",
        scrub: true,

        // clave para que no intente aplicar estados antes de llegar:
        immediateRender: false,
        invalidateOnRefresh: true,
      },
    });

    animateToPositions(tlFlexible, positionsFlexible, 0.03);
    infinito(tlFlexible);
  }

  function desglocesClave() {
    if (document.querySelector(".hero")) {
      const keyWords = [
        {
          id: "words1",
          info: "Pensamos: inteligencia de mercado, estrategia fundamentada",
        },
        {
          id: "words3",
          info: "Maggiore significa mayor. Nuestra promesa es crecimiento para tu empresa",
        },
        {
          id: "words7",
          info: "Respiramos marketing y branding. Creamos marcas desde cero y hacemos que las marcas ya grandes sean aún mayores.",
        },
        {
          id: "words12",
          info: "Alto: Utilizamos la inteligencia de mercados y el marketing digital como motores de crecimiento para tu negocio.",
        },
      ];

      keyWords.forEach((word, index) => {
        let spanWord = document.querySelector(`.${word.id}`);
        let divInfo = document.createElement("div");
        divInfo.classList.add("desgloce");
        divInfo.innerHTML = "<p>" + word.info + "</p>";
        spanWord.appendChild(divInfo);
        spanWord.classList.add("transition");
        spanWord.addEventListener("mouseover", () => {
          spanWord.classList.add("colored-text");
          document.querySelectorAll(".desgloce")[index].style.display = "block";
        });
        spanWord.addEventListener("mouseleave", () => {
          spanWord.classList.remove("colored-text");
          document.querySelectorAll(".desgloce")[index].style.display = "none";
        });
      });
    }
  }

  if (document.querySelector(".globo-2")) {
    gsap.from(".globo-2", {
      y: 400,
      duration: 2,
      scrollTrigger: {
        trigger: ".sectionTwo",
        scrub: true,
        start: "top 80%",
        end: "center center",
      },
    });
  }

  if (document.querySelector(".globo")) {
    gsap.from(".globo", {
      y: 400,
      duration: 2,
      onComplete: () => {
        // ✅ aquí tu función
        globoParalax();
      },
    });

    function globoParalax() {
      const globo = document.querySelector(".globo");

      // Ajusta la intensidad del movimiento (px máximos aprox)
      const strength = 50;

      // Variables para suavizado (lerp)
      let targetX = 0,
        targetY = 0;
      let currentX = 0,
        currentY = 0;
      const ease = 0.08; // más bajo = más lento/suave

      window.addEventListener("mousemove", (e) => {
        const { innerWidth: w, innerHeight: h } = window;

        // Normaliza mouse a rango [-0.5, 0.5]
        const nx = e.clientX / w - 0.5;
        const ny = e.clientY / h - 0.5;

        targetX = nx * strength;
        targetY = ny * strength;
      });

      function animate() {
        // Interpolación suave hacia el target
        currentX += (targetX - currentX) * ease;
        currentY += (targetY - currentY) * ease;

        globo.style.transform = `translate(${currentX}px, ${currentY}px)`;

        requestAnimationFrame(animate);
      }
      animate();
    }
  }
});
