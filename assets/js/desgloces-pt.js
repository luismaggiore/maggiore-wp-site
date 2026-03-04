function desglocesClave() {
  if (document.querySelector(".hero")) {
    [
      {
        id: "words1",
        info: "Pensamos: inteligência de mercado, estratégia fundamentada",
      },
      {
        id: "words2",
        info: "Maggiore significa maior. Nossa promessa é crescimento para sua empresa",
      },
      {
        id: "words6",
        info: "Respiramos marketing e branding. Criamos marcas do zero e fazemos marcas já grandes ficarem ainda maiores.",
      },
      {
        id: "words9",
        info: "Alto: Utilizamos a inteligência de mercados e o marketing digital como motores de crescimento para seu negócio.",
      },
    ].forEach((e, o) => {
      let a = document.querySelector(`.${e.id}`),
        s = document.createElement("div");
      (s.classList.add("desgloce"),
        (s.innerHTML = "<p>" + e.info + "</p>"),
        a.appendChild(s),
        a.classList.add("transition"),
        a.addEventListener("mouseover", () => {
          (a.classList.add("colored-text"),
            (document.querySelectorAll(".desgloce")[o].style.display =
              "block"));
        }),
        a.addEventListener("mouseleave", () => {
          (a.classList.remove("colored-text"),
            (document.querySelectorAll(".desgloce")[o].style.display = "none"));
        }));
    });
  }
}
document.querySelector(".title-reveal") &&
  document.fonts.ready.then(() => {
    let e = SplitText.create(".title-reveal", {
        type: "words,lines",
        linesClass: "lines",
        wordsClass: "words++",
      }).words,
      o = document.querySelector(".globo") ? 1 : 0;
    gsap.from(e, {
      duration: 0.6,
      autoAlpha: 0,
      delay: o,
      x: 20,
      ease: "power1.out",
      stagger: { amount: 0.6 },
      onComplete: () => {
        desglocesClave();
      },
    });
  });
