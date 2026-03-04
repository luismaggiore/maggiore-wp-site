document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".custom-video-player").forEach((t) => {
    const n = t.querySelector(".video-element"),
      o = (t.querySelector(".video-poster"), t.querySelector(".poster-image")),
      r = t.querySelector(".play-btn-center"),
      l = t.querySelector(".play-pause"),
      a = l.querySelector(".play-icon"),
      c = l.querySelector(".pause-icon"),
      i = t.querySelector(".progress-bar"),
      d = t.querySelector(".progress-filled"),
      s = t.querySelector(".current-time"),
      u = t.querySelector(".duration"),
      y = t.querySelector(".volume-btn"),
      p = t.querySelector(".volume-slider"),
      m = t.querySelector(".fullscreen-btn"),
      v = t.querySelector(".thumbnail-canvas");
    let f = !1;
    if ("true" === t.dataset.needsThumb && v) {
      const e = document.createElement("video");
      ((e.src = t.dataset.src),
        (e.currentTime = 1),
        (e.preload = "metadata"),
        (e.muted = !0),
        e.addEventListener("loadeddata", function () {
          const t = v.getContext("2d");
          ((v.width = e.videoWidth),
            (v.height = e.videoHeight),
            t.drawImage(e, 0, 0, v.width, v.height));
          const n = v.toDataURL("image/jpeg", 0.8);
          ((o.src = n), (e.src = ""), e.remove());
        }));
    }
    (r.addEventListener("click", function (o) {
      if ((o.stopPropagation(), f)) (n.play(), t.classList.add("playing"));
      else {
        const o = t.dataset.src,
          r = t.dataset.mime,
          l = document.createElement("source");
        ((l.src = o),
          (l.type = r),
          n.appendChild(l),
          n.load(),
          (f = !0),
          n.addEventListener("loadedmetadata", function () {
            u.textContent = e(n.duration);
          }),
          n.addEventListener(
            "canplay",
            function () {
              (n.play(), t.classList.add("playing"));
            },
            { once: !0 },
          ));
      }
    }),
      l.addEventListener("click", function () {
        n.paused ? n.play() : n.pause();
      }),
      n.addEventListener("play", function () {
        ((a.style.display = "none"),
          (c.style.display = "block"),
          t.classList.add("playing"));
      }),
      n.addEventListener("pause", function () {
        ((a.style.display = "block"), (c.style.display = "none"));
      }),
      n.addEventListener("timeupdate", function () {
        const t = (n.currentTime / n.duration) * 100;
        ((d.style.width = t + "%"), (s.textContent = e(n.currentTime)));
      }),
      i.addEventListener("click", function (e) {
        const t = i.getBoundingClientRect(),
          o = (e.clientX - t.left) / t.width;
        n.currentTime = o * n.duration;
      }),
      p.addEventListener("input", function () {
        n.volume = this.value / 100;
      }),
      y.addEventListener("click", function () {
        n.volume > 0
          ? ((n.volume = 0), (p.value = 0))
          : ((n.volume = 1), (p.value = 100));
      }),
      m.addEventListener("click", function () {
        t.requestFullscreen
          ? t.requestFullscreen()
          : t.webkitRequestFullscreen
            ? t.webkitRequestFullscreen()
            : t.mozRequestFullScreen && t.mozRequestFullScreen();
      }),
      n.addEventListener("click", function () {
        n.paused ? n.play() : n.pause();
      }));
  });
  if (
    (document.querySelectorAll(".external-video").forEach((e) => {
      const t = e.querySelector(".play-btn-center"),
        n = e.dataset.embedUrl;
      t.addEventListener("click", function () {
        const t = document.createElement("iframe");
        ((t.src = n + "?autoplay=1"),
          (t.frameBorder = "0"),
          (t.allow =
            "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"),
          (t.allowFullscreen = !0),
          (t.style.position = "absolute"),
          (t.style.top = "0"),
          (t.style.left = "0"),
          (t.style.width = "100%"),
          (t.style.height = "100%"),
          (e.innerHTML = ""),
          e.appendChild(t));
      });
    }),
    "undefined" != typeof GLightbox)
  ) {
    GLightbox({ selector: "[data-lightbox]", touchNavigation: !0, loop: !0 });
  }
  function e(e) {
    const t = Math.floor(e / 60),
      n = Math.floor(e % 60);
    return t + ":" + (n < 10 ? "0" : "") + n;
  }
});
