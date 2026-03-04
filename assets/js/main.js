document.addEventListener("DOMContentLoaded", (event) => {
gsap.registerPlugin(
ScrollTrigger,
ScrollSmoother,
ScrollToPlugin,
SplitText,
TextPlugin,
);
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
mm.add("(min-width: 992px)", () => {
const sidebar = document.querySelector(".blog-sidebar");
const content = document.querySelector(".blog-mainbar");
if (content.offsetHeight > sidebar.offsetHeight) {
ScrollTrigger.create({
trigger: content,
pin: sidebar,
start: "top top+=100",
end: "bottom bottom",
pinSpacing: false,
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
card.addEventListener("focusin", on);
card.addEventListener("focusout", off);
});
}
makeHoverableCardsGSAP({
cardSelector: ".card-mg",
linkSelector: ".stretched-link",
});
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
sectionsIDs.forEach((s, index) => {
dots[index].addEventListener("click", (e) => {
e.preventDefault();
goToIndex(index);
});
});
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
sectionsIDs.forEach((id, index) => {
ScrollTrigger.create({
trigger: "#" + id,
start: "top center",
end: "bottom center",
onEnter: () => setActiveDot(index),
onEnterBack: () => setActiveDot(index),
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
const directions = [
{ x: -distance, y: 0 },
{ x: distance, y: 0 },
{ x: 0, y: -distance },
{ x: 0, y: distance },
];
// Baraja direcciones para que se sienta menos "patrón"
const shuffled = directions
.map((d) => ({ d, r: Math.random() }))
.sort((a, b) => a.r - b.r)
.map((o) => o.d);
cards.forEach((card, i) => {
const dir = shuffled[i % shuffled.length];
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
},
});
});
})();
if (document.querySelector(".text-appear")) {
document.fonts.ready.then(() => {
let splitText = SplitText.create(".text-appear", {
type: "words,chars,lines",
});
let words = splitText.words;
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
gsap.from(".brand-name", {
autoAlpha: 0,
y: 100,
duration: 1,
ease: "power1.out",
});
const mm = gsap.matchMedia();
mm.add("(min-width: 768px)", () => {
ScrollTrigger.create({
trigger: "#sectionThree",
pin: ".testimonial-title",
start: "top 40% ",
end: "bottom 80%",
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
trigger: "#robinHood",
endTrigger: "#flexible",
pin: ".constelacion",
start: "center center",
end: "center center",
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
let chars = mySplitText.words;
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
let chars = mySplitText.words;
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
const positionsInteligencia = [
{ cx: 340, cy: 260 },
{ cx: 280, cy: 140 },
{ cx: 340, cy: 90 },
{ cx: 340, cy: 420 },
{ cx: 180, cy: 520 },
{ cx: 400, cy: 140 },
{ cx: 500, cy: 520 },
];
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
const tlInteligencia = gsap.timeline({
scrollTrigger: {
trigger: "#inteligencia",
start: "top center",
end: "center center",
scrub: true,
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
immediateRender: false,
invalidateOnRefresh: true,
},
});
animateToPositions(tlFlexible, positionsFlexible, 0.03);
infinito(tlFlexible);
}
if (document.querySelector(".title-reveal2")) {
document.fonts.ready.then(() => {
let mySplitText = SplitText.create(".title-reveal2", {
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
});
});
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
globoParalax();
},
});
function globoParalax() {
const globo = document.querySelector(".globo");
const strength = 50;
let targetX = 0,
targetY = 0;
let currentX = 0,
currentY = 0;
const ease = 0.08;
window.addEventListener("mousemove", (e) => {
const { innerWidth: w, innerHeight: h } = window;
const nx = e.clientX / w - 0.5;
const ny = e.clientY / h - 0.5;
targetX = nx * strength;
targetY = ny * strength;
});
function animate() {
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
if (switcher.querySelector(".ls-dropdown")) return;
const items = Array.from(switcher.querySelectorAll(":scope > li.lang-item"));
if (!items.length) return;
const current =
items.find((li) => li.classList.contains("current-lang")) || items[0];
const currentLink = current.querySelector("a");
const currentImg = currentLink?.querySelector("img");
if (!currentLink || !currentImg) return;
const dropdown = document.createElement("div");
dropdown.className = "ls-dropdown";
const btn = document.createElement("button");
btn.type = "button";
btn.className = "ls-btn";
btn.setAttribute("aria-haspopup", "menu");
btn.setAttribute("aria-expanded", "false");
const btnImg = currentImg.cloneNode(true);
btnImg.removeAttribute("style");
btn.appendChild(btnImg);
const caret = document.createElement("span");
caret.className = "ls-caret";
caret.textContent = "▾";
btn.appendChild(caret);
const menu = document.createElement("div");
menu.className = "ls-menu";
menu.setAttribute("role", "menu");
items.forEach((li) => {
if (li === current) return;
const a = li.querySelector("a");
const img = a?.querySelector("img");
if (!a || !img) return;
const aClone = a.cloneNode(true);
const imgClone = img.cloneNode(true);
imgClone.removeAttribute("style");
aClone.innerHTML = "";
aClone.appendChild(imgClone);
aClone.setAttribute("role", "menuitem");
menu.appendChild(aClone);
});
dropdown.appendChild(btn);
dropdown.appendChild(menu);
switcher.appendChild(dropdown);
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
document.addEventListener("click", (e) => {
if (!dropdown.contains(e.target)) close();
});
document.addEventListener("keydown", (e) => {
if (e.key === "Escape") close();
});
});