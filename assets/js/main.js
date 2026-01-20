document.addEventListener("DOMContentLoaded", (event) => {
  gsap.registerPlugin(
    ScrollTrigger,
    ScrollSmoother,
    ScrollToPlugin,
    SplitText,
    TextPlugin,
  );
  // gsap code here!
  const smoother = ScrollSmoother.create({
    wrapper: "#smooth-wrapper",
    content: "#smooth-content",
    smooth: 1.2,
    effects: true,
  });

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

  if (document.querySelector(".mg_values_scope")) {
    const scope = document.querySelector(".mg_values_scope");
    if (!scope) return;

    const letters = Array.from(scope.querySelectorAll(".mg_values_letter"));
    const cards = Array.from(scope.querySelectorAll(".mg_values_card"));

    if (letters.length === 0 || cards.length === 0) return;

    const clearActive = () => {
      letters.forEach((letter) => letter.classList.remove("is_active"));
    };

    cards.forEach((card, idx) => {
      card.addEventListener("mouseenter", () => {
        clearActive();
        if (letters[idx]) letters[idx].classList.add("is_active");
      });

      card.addEventListener("mouseleave", () => {
        clearActive();
      });
    });
  }

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
  function makeHoverableCardsGSAP({
    cardSelector,
    linkSelector,
    scale = 0.99,
    duration = 0.18,
    ease = "power2.out",
    backgroundHover = "var(--background-hover)",
    backgroundNormal = "", // "" = vuelve al estilo CSS original
  }) {
    const reduceMotion =
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    const cardExists = document.querySelector(cardSelector);
    const linkExists = document.querySelector(linkSelector);
    if (!cardExists || !linkExists) return;

    const cards = Array.from(document.querySelectorAll(cardSelector)).filter(
      (card) => card.querySelector(linkSelector),
    );

    cards.forEach((card) => {
      // Estado inicial “normal”

      // Creamos una animación reutilizable por card
      const hoverTween = gsap.to(card, {
        scale,
        backgroundColor: backgroundHover,
        duration: reduceMotion ? 0 : duration,
        ease,
        paused: true,
        overwrite: "auto",
      });

      const on = () => hoverTween.play();
      const off = () => hoverTween.reverse();

      card.addEventListener("pointerenter", on);
      card.addEventListener("pointerleave", off);

      // accesibilidad (tab)
      card.addEventListener("focusin", on);
      card.addEventListener("focusout", off);
    });
  }

  // Ejemplo 1: .card-mg
  makeHoverableCardsGSAP({
    cardSelector: ".card-mg",
    linkSelector: ".stretched-link",
  });

  // Ejemplo 2: .blog-article
  makeHoverableCardsGSAP({
    cardSelector: ".blog-article",
    linkSelector: ".stretched-link",
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
        d.classList.contains("active"),
      );
      goToIndex(Math.max(0, currentIndex - 1));
    });

    nextBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const currentIndex = dotsArray.findIndex((d) =>
        d.classList.contains("active"),
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
    let delayValue = document.querySelector(".globo") ? 1.4 : 0.4;

    gsap.from(".bajada-reveal", {
      x: 20,
      autoAlpha: 0,
      delay: delayValue,
      duration: 0.6,
      ease: "power1.out",
    });
  }

  (() => {
    if (typeof gsap === "undefined") return;

    // Respeta "reduced motion"
    const reduceMotion = window.matchMedia(
      "(prefers-reduced-motion: reduce)",
    ).matches;

    const cards = gsap.utils.toArray(".card-mg");
    if (!cards.length) return;

    gsap.registerPlugin(ScrollTrigger);

    const distance = 20;
    // 4 direcciones base (offsets)
    const directions = [
      { x: -distance, y: 0 }, // desde izquierda
      { x: distance, y: 0 }, // desde derecha
      { x: 0, y: -distance }, // desde arriba
      { x: 0, y: distance }, // desde abajo
    ];

    // Baraja direcciones para que se sienta menos "patrón"
    const shuffled = directions
      .map((d) => ({ d, r: Math.random() }))
      .sort((a, b) => a.r - b.r)
      .map((o) => o.d);

    cards.forEach((card, i) => {
      const dir = shuffled[i % shuffled.length];

      // Si reduced motion, que aparezca sin desplazamiento
      const fromVars = reduceMotion
        ? { autoAlpha: 0 }
        : {
            autoAlpha: 0,
            x: dir.x,
            y: dir.y,
          };

      gsap.fromTo(card, fromVars, {
        autoAlpha: 1,
        x: 0,
        y: 0,
        duration: reduceMotion ? 0.01 : 2,
        ease: "expo.out", // "cool" (puedes probar "power4.out" también)
        clearProps: "transform,opacity",
        scrollTrigger: {
          trigger: card,
          start: "top 85%",
          end: "top 60%",
          toggleActions: "play none none reverse",
          // markers: true, // descomenta para debug
        },
      });
    });
  })();

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
      let delayValue = document.querySelector(".globo") ? 1 : 0;

      gsap.from(chars, {
        duration: 0.6,
        autoAlpha: 0,
        delay: delayValue,
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

  if (document.querySelector(".reveal-up")) {
    gsap.utils.toArray(".reveal-up").forEach((el) => {
      gsap.from(el, {
        y: 100,
        autoAlpha: 0,
        ease: "expo.out",
        scrollTrigger: {
          trigger: el,
          toggleActions: "play none none reverse",
        },
      });
    });
  }

  if (document.querySelector(".mision-vision")) {
    gsap.utils.toArray(".mision-vision").forEach((el) => {
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
          i * stagger,
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

document.addEventListener("DOMContentLoaded", () => {
  const switcher = document.querySelector(".language-switcher");
  if (!switcher) return;

  // Evita duplicar si el script corre 2 veces
  if (switcher.querySelector(".ls-dropdown")) return;

  const items = Array.from(switcher.querySelectorAll(":scope > li.lang-item"));
  if (!items.length) return;

  const current =
    items.find((li) => li.classList.contains("current-lang")) || items[0];
  const currentLink = current.querySelector("a");
  const currentImg = currentLink?.querySelector("img");

  if (!currentLink || !currentImg) return;

  // Crear estructura nueva
  const dropdown = document.createElement("div");
  dropdown.className = "ls-dropdown";

  const btn = document.createElement("button");
  btn.type = "button";
  btn.className = "ls-btn";
  btn.setAttribute("aria-haspopup", "menu");
  btn.setAttribute("aria-expanded", "false");

  const btnImg = currentImg.cloneNode(true);
  btnImg.removeAttribute("style"); // por si viene con inline style
  btn.appendChild(btnImg);

  const caret = document.createElement("span");
  caret.className = "ls-caret";
  caret.textContent = "▾";
  btn.appendChild(caret);

  const menu = document.createElement("div");
  menu.className = "ls-menu";
  menu.setAttribute("role", "menu");

  // Poner en el menú SOLO los idiomas que no son current
  items.forEach((li) => {
    if (li === current) return;
    const a = li.querySelector("a");
    const img = a?.querySelector("img");
    if (!a || !img) return;

    const aClone = a.cloneNode(true);
    const imgClone = img.cloneNode(true);
    imgClone.removeAttribute("style");

    // dejamos solo la imagen dentro del link
    aClone.innerHTML = "";
    aClone.appendChild(imgClone);

    aClone.setAttribute("role", "menuitem");
    menu.appendChild(aClone);
  });

  dropdown.appendChild(btn);
  dropdown.appendChild(menu);
  switcher.appendChild(dropdown);

  // Toggle con click
  const open = () => {
    dropdown.classList.add("is-open");
    btn.setAttribute("aria-expanded", "true");
  };
  const close = () => {
    dropdown.classList.remove("is-open");
    btn.setAttribute("aria-expanded", "false");
  };
  const toggle = () => {
    dropdown.classList.contains("is-open") ? close() : open();
  };

  btn.addEventListener("click", (e) => {
    e.stopPropagation();
    toggle();
  });

  // Cerrar al click afuera
  document.addEventListener("click", (e) => {
    if (!dropdown.contains(e.target)) close();
  });

  // ESC para cerrar
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") close();
  });
});
