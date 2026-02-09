/**
 * DESGLOCES - PORTUGUÊS
 * Tooltips hover em palavras-chave do hero
 * Só carrega em páginas em português
 */

/**
 * DESGLOCES - ESPAÑOL
 * Tooltips hover en palabras clave del hero
 * Solo se carga en páginas en español
 */

function desglocesClave() {
  if (document.querySelector(".hero")) {
    const keyWords = [
      {
        id: 'words1',
        info: 'Pensamos: inteligência de mercado, estratégia fundamentada'
      },
      {
        id: 'words2',
        info: 'Maggiore significa maior. Nossa promessa é crescimento para sua empresa'
      },
      {
        id: 'words6',
        info: 'Respiramos marketing e branding. Criamos marcas do zero e fazemos marcas já grandes ficarem ainda maiores.'
      },
      {
        id: 'words9',
        info: 'Alto: Utilizamos a inteligência de mercados e o marketing digital como motores de crescimento para seu negócio.'
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
