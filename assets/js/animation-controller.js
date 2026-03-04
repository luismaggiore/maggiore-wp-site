class AnimationController {
  constructor() {
    ((this.effects = new Map()),
      (this.isRunning = !1),
      (this.frameId = null),
      (this.lastTime = 0),
      (this.fps = 0),
      (this.frameCount = 0),
      (this.fpsUpdateTime = 0),
      this.setupVisibilityObserver(),
      this.setupReducedMotionObserver(),
      console.log("🎬 Animation Controller initialized"));
  }
  register(e, t, i = 0) {
    return (
      this.effects.has(e) &&
        console.warn(`⚠️ Effect "${e}" already registered, replacing...`),
      this.effects.set(e, {
        effect: t,
        priority: i,
        enabled: !0,
        updateCount: 0,
        lastUpdateTime: 0,
      }),
      console.log(`✅ Registered effect: ${e} (priority: ${i})`),
      1 !== this.effects.size || this.isRunning || this.start(),
      this
    );
  }
  unregister(e) {
    return (
      this.effects.delete(e) && console.log(`❌ Unregistered effect: ${e}`),
      0 === this.effects.size && this.stop(),
      this
    );
  }
  toggle(e, t) {
    const i = this.effects.get(e);
    return (
      i
        ? ((i.enabled = t),
          console.log(`🔄 Effect "${e}" ${t ? "enabled" : "disabled"}`))
        : console.warn(`⚠️ Effect "${e}" not found`),
      this
    );
  }
  getEffect(e) {
    const t = this.effects.get(e);
    return t ? t.effect : null;
  }
  start() {
    if (!this.isRunning)
      return (
        (this.isRunning = !0),
        (this.lastTime = performance.now()),
        (this.fpsUpdateTime = this.lastTime),
        (this.frameCount = 0),
        this.tick(this.lastTime),
        console.log("▶️ Animation Controller started"),
        this
      );
    console.warn("⚠️ Animation Controller already running");
  }
  stop() {
    if (this.isRunning)
      return (
        (this.isRunning = !1),
        this.frameId &&
          (cancelAnimationFrame(this.frameId), (this.frameId = null)),
        console.log("⏸️ Animation Controller stopped"),
        this
      );
  }
  tick(e) {
    if (!this.isRunning) return;
    const t = (e - this.lastTime) / 1e3;
    ((this.lastTime = e),
      this.frameCount++,
      e - this.fpsUpdateTime >= 1e3 &&
        ((this.fps = this.frameCount),
        (this.frameCount = 0),
        (this.fpsUpdateTime = e)));
    (Array.from(this.effects.entries())
      .filter(([e, t]) => t.enabled)
      .sort((e, t) => t[1].priority - e[1].priority)
      .forEach(([i, s]) => {
        try {
          (!s.effect.shouldUpdate || s.effect.shouldUpdate(e, t)) &&
            (s.effect.update(t, e), s.updateCount++, (s.lastUpdateTime = e));
        } catch (e) {
          (console.error(`❌ Error updating effect "${i}":`, e),
            (s.enabled = !1));
        }
      }),
      (this.frameId = requestAnimationFrame((e) => this.tick(e))));
  }
  setupVisibilityObserver() {
    document.addEventListener("visibilitychange", () => {
      document.hidden
        ? (this.stop(), console.log("👁️ Page hidden - animations paused"))
        : (this.start(), console.log("👁️ Page visible - animations resumed"));
    });
  }
  setupReducedMotionObserver() {
    const e = window.matchMedia("(prefers-reduced-motion: reduce)"),
      t = (e) => {
        e.matches
          ? (this.stop(),
            console.log("♿ Reduced motion detected - animations disabled"),
            document.body.classList.add("visual-engine-reduced-motion"))
          : (this.start(),
            document.body.classList.remove("visual-engine-reduced-motion"));
      };
    (e.addEventListener("change", t), t(e));
  }
  getStats() {
    const e = {};
    return (
      this.effects.forEach((t, i) => {
        e[i] = {
          enabled: t.enabled,
          priority: t.priority,
          updateCount: t.updateCount,
          lastUpdate: t.lastUpdateTime,
        };
      }),
      {
        fps: Math.round(this.fps),
        effectCount: this.effects.size,
        activeEffects: Array.from(this.effects.entries()).filter(
          ([e, t]) => t.enabled,
        ).length,
        isRunning: this.isRunning,
        effects: e,
      }
    );
  }
  logStats() {
    const e = this.getStats();
    return (console.log("📊 Animation Controller Stats:", e), this);
  }
  destroy() {
    (this.stop(),
      this.effects.clear(),
      console.log("💥 Animation Controller destroyed"));
  }
}
("undefined" != typeof window &&
  ((window.AnimationController = AnimationController),
  (window.animationController = new AnimationController()),
  void 0 !== window.VISUAL_CONFIG &&
    window.VISUAL_CONFIG.debug?.showFPS &&
    setInterval(() => {
      const e = window.animationController.getStats();
      console.log(
        `FPS: ${e.fps} | Effects: ${e.activeEffects}/${e.effectCount}`,
      );
    }, 2e3)),
  "undefined" != typeof module &&
    module.exports &&
    (module.exports = AnimationController));
