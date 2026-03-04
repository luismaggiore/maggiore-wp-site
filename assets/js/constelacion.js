document.addEventListener("DOMContentLoaded", () => {
if (!document.querySelector(".constelacion")) {
console.log('⚠️ Constellation: .constelacion element not found');
return;
}
const svg = document.querySelector(".constelacion svg");
if (!svg) {
console.error('❌ Constellation: SVG not found inside .constelacion');
return;
}
const controller = window.animationController;
const config = window.VISUAL_CONFIG?.constellation;
if (!controller) {
console.error('❌ Constellation: AnimationController not found');
return;
}
if (!config || !config.enabled) {
console.log('⚠️ Constellation disabled in config');
return;
}
const MAX_DISTANCE = config.maxDistance;
const NEIGHBORS = config.maxNeighbors;
const MOVEMENT_THRESHOLD = config.movementThreshold || 1;
const LINE_STYLE = config.lineStyle;
function distance(a, b) {
const dx = a.cx - b.cx;
const dy = a.cy - b.cy;
return Math.sqrt(dx * dx + dy * dy);
}
function getLineGroup() {
let lineGroup = svg.querySelector("g[data-constellation-lines]");
if (!lineGroup) {
lineGroup = document.createElementNS("http://www.w3.org/2000/svg", "g");
lineGroup.setAttribute("data-constellation-lines", "true");
svg.insertBefore(lineGroup, svg.firstChild);
}
return lineGroup;
}
const lineGroup = getLineGroup();
let previousPositions = null;
let lastUpdateTime = 0;
function checkPositionChanges() {
const circles = Array.from(svg.querySelectorAll("circle"));
const currentPositions = circles.map(c => ({
cx: parseFloat(c.getAttribute("cx")),
cy: parseFloat(c.getAttribute("cy"))
}));
if (!previousPositions) {
previousPositions = currentPositions;
return true;
}
const changed = currentPositions.some((pos, i) => {
const prev = previousPositions[i];
if (!prev) return true;
return Math.abs(pos.cx - prev.cx) > MOVEMENT_THRESHOLD ||
Math.abs(pos.cy - prev.cy) > MOVEMENT_THRESHOLD;
});
if (changed) {
previousPositions = currentPositions;
}
return changed;
}
function redrawLines() {
const circles = Array.from(svg.querySelectorAll("circle"));
const points = circles.map((c, index) => {
const cx = parseFloat(c.getAttribute("cx"));
const cy = parseFloat(c.getAttribute("cy"));
return { index, cx, cy };
});
lineGroup.innerHTML = "";
const drawnPairs = new Set();
points.forEach((p) => {
const nearest = points
.filter((o) => o.index !== p.index)
.map((o) => ({ o, d: distance(p, o) }))
.filter((x) => x.d <= MAX_DISTANCE)
.sort((a, b) => a.d - b.d)
.slice(0, NEIGHBORS);
nearest.forEach(({ o }) => {
const a = Math.min(p.index, o.index);
const b = Math.max(p.index, o.index);
const key = `${a}:${b}`;
if (drawnPairs.has(key)) return;
drawnPairs.add(key);
const line = document.createElementNS(
"http://www.w3.org/2000/svg",
"line"
);
line.setAttribute("x1", p.cx);
line.setAttribute("y1", p.cy);
line.setAttribute("x2", o.cx);
line.setAttribute("y2", o.cy);
line.setAttribute("stroke", LINE_STYLE.stroke);
line.setAttribute("stroke-width", LINE_STYLE.strokeWidth);
line.setAttribute("opacity", LINE_STYLE.opacity);
lineGroup.appendChild(line);
});
});
}
const constellationEffect = {
name: 'constellation',
dirty: true,
enabled: true,
updateCount: 0,
shouldUpdate(currentTime) {
if (this.dirty) {
return true;
}
if (currentTime - lastUpdateTime < 50) {
return false;
}
lastUpdateTime = currentTime;
return checkPositionChanges();
},
update(deltaTime, currentTime) {
redrawLines();
this.dirty = false;
this.updateCount++;
},
markDirty() {
this.dirty = true;
},
forceUpdate() {
this.dirty = true;
redrawLines();
},
getStats() {
const circles = svg.querySelectorAll("circle").length;
const lines = lineGroup.querySelectorAll("line").length;
return {
points: circles,
lines: lines,
updateCount: this.updateCount
};
}
};
controller.register('constellation', constellationEffect, 2);
constellationEffect.forceUpdate();
console.log('⭐ Constellation effect initialized');
if (config.debug?.logEffects) {
console.log('Constellation stats:', constellationEffect.getStats());
}
window.constellationEffect = constellationEffect;
window.constellationStats = () => {
const stats = constellationEffect.getStats();
console.table(stats);
return stats;
};
});