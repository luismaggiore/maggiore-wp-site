/**
 * DESGLOCES - ENGLISH
 * Hover tooltips on hero key words
 * Only loads on English pages
 *
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
        id: "words2",
        info: 'We think: market intelligence, grounded strategy',
      },
      {
        id: "words3",
        info: "Maggiore means greater. Our promise is growth for your company",
      },
      {
        id: "words6",
        info: "We breathe marketing and branding. We create brands from scratch and make already great brands even greater.",
      },
      {
        id: "words9",
        info: "High: We use market intelligence and digital marketing as growth engines for your business.",
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

