function desglocesClave() {
  if (document.querySelector(".hero")) {
    [
      {
        id: "words2",
        info: "We think: market intelligence, grounded strategy",
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
    ].forEach((e, t) => {
      let o = document.querySelector(`.${e.id}`),
        r = document.createElement("div");
      (r.classList.add("desgloce"),
        (r.innerHTML = "<p>" + e.info + "</p>"),
        o.appendChild(r),
        o.classList.add("transition"),
        o.addEventListener("mouseover", () => {
          (o.classList.add("colored-text"),
            (document.querySelectorAll(".desgloce")[t].style.display =
              "block"));
        }),
        o.addEventListener("mouseleave", () => {
          (o.classList.remove("colored-text"),
            (document.querySelectorAll(".desgloce")[t].style.display = "none"));
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
      t = document.querySelector(".globo") ? 1 : 0;
    gsap.from(e, {
      duration: 0.6,
      autoAlpha: 0,
      delay: t,
      x: 20,
      ease: "power1.out",
      stagger: { amount: 0.6 },
      onComplete: () => {
        desglocesClave();
      },
    });
  });
