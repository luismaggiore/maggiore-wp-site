/**
 * DESGLOCES - ESPAÑOL
 * Tooltips hover en palabras clave del hero
 * Solo se carga en páginas en español
 */

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