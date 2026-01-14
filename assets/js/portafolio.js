document.addEventListener("DOMContentLoaded", function () {
  // ========== CUSTOM VIDEO PLAYERS ==========
  const videoPlayers = document.querySelectorAll(".custom-video-player");

  videoPlayers.forEach((player) => {
    const video = player.querySelector(".video-element");
    const poster = player.querySelector(".video-poster");
    const posterImage = player.querySelector(".poster-image");
    const playBtnCenter = player.querySelector(".play-btn-center");
    const playPauseBtn = player.querySelector(".play-pause");
    const playIcon = playPauseBtn.querySelector(".play-icon");
    const pauseIcon = playPauseBtn.querySelector(".pause-icon");
    const progressBar = player.querySelector(".progress-bar");
    const progressFilled = player.querySelector(".progress-filled");
    const currentTimeEl = player.querySelector(".current-time");
    const durationEl = player.querySelector(".duration");
    const volumeBtn = player.querySelector(".volume-btn");
    const volumeSlider = player.querySelector(".volume-slider");
    const fullscreenBtn = player.querySelector(".fullscreen-btn");
    const canvas = player.querySelector(".thumbnail-canvas");

    let isLoaded = false;
    const needsThumb = player.dataset.needsThumb === "true";

    // Si necesita thumbnail, generarlo del primer frame del video
    if (needsThumb && canvas) {
      const tempVideo = document.createElement("video");
      tempVideo.src = player.dataset.src;
      tempVideo.currentTime = 1; // Segundo 1
      tempVideo.preload = "metadata";
      tempVideo.muted = true;

      tempVideo.addEventListener("loadeddata", function () {
        const ctx = canvas.getContext("2d");
        canvas.width = tempVideo.videoWidth;
        canvas.height = tempVideo.videoHeight;
        ctx.drawImage(tempVideo, 0, 0, canvas.width, canvas.height);

        // Convertir canvas a imagen
        const thumbnailUrl = canvas.toDataURL("image/jpeg", 0.8);
        posterImage.src = thumbnailUrl;

        // Limpiar video temporal
        tempVideo.src = "";
        tempVideo.remove();
      });
    }

    // Cargar video al hacer click en el poster
    playBtnCenter.addEventListener("click", function (e) {
      e.stopPropagation();

      if (!isLoaded) {
        // Cargar video
        const src = player.dataset.src;
        const mime = player.dataset.mime;

        const source = document.createElement("source");
        source.src = src;
        source.type = mime;
        video.appendChild(source);
        video.load();

        isLoaded = true;

        video.addEventListener("loadedmetadata", function () {
          durationEl.textContent = formatTime(video.duration);
        });

        video.addEventListener(
          "canplay",
          function () {
            video.play();
            player.classList.add("playing");
          },
          { once: true }
        );
      } else {
        video.play();
        player.classList.add("playing");
      }
    });

    // Play/Pause
    playPauseBtn.addEventListener("click", function () {
      if (video.paused) {
        video.play();
      } else {
        video.pause();
      }
    });

    video.addEventListener("play", function () {
      playIcon.style.display = "none";
      pauseIcon.style.display = "block";
      player.classList.add("playing");
    });

    video.addEventListener("pause", function () {
      playIcon.style.display = "block";
      pauseIcon.style.display = "none";
    });

    // Progress bar
    video.addEventListener("timeupdate", function () {
      const percent = (video.currentTime / video.duration) * 100;
      progressFilled.style.width = percent + "%";
      currentTimeEl.textContent = formatTime(video.currentTime);
    });

    progressBar.addEventListener("click", function (e) {
      const rect = progressBar.getBoundingClientRect();
      const pos = (e.clientX - rect.left) / rect.width;
      video.currentTime = pos * video.duration;
    });

    // Volumen
    volumeSlider.addEventListener("input", function () {
      video.volume = this.value / 100;
    });

    volumeBtn.addEventListener("click", function () {
      if (video.volume > 0) {
        video.volume = 0;
        volumeSlider.value = 0;
      } else {
        video.volume = 1;
        volumeSlider.value = 100;
      }
    });

    // Fullscreen
    fullscreenBtn.addEventListener("click", function () {
      if (player.requestFullscreen) {
        player.requestFullscreen();
      } else if (player.webkitRequestFullscreen) {
        player.webkitRequestFullscreen();
      } else if (player.mozRequestFullScreen) {
        player.mozRequestFullScreen();
      }
    });

    // Click en el video para play/pause
    video.addEventListener("click", function () {
      if (video.paused) {
        video.play();
      } else {
        video.pause();
      }
    });
  });

  // ========== VIDEOS EXTERNOS ==========
  const externalVideos = document.querySelectorAll(".external-video");

  externalVideos.forEach((container) => {
    const playBtn = container.querySelector(".play-btn-center");
    const embedUrl = container.dataset.embedUrl;

    playBtn.addEventListener("click", function () {
      const iframe = document.createElement("iframe");
      iframe.src = embedUrl + "?autoplay=1";
      iframe.frameBorder = "0";
      iframe.allow =
        "accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture";
      iframe.allowFullscreen = true;
      iframe.style.position = "absolute";
      iframe.style.top = "0";
      iframe.style.left = "0";
      iframe.style.width = "100%";
      iframe.style.height = "100%";

      container.innerHTML = "";
      container.appendChild(iframe);
    });
  });

  // ========== LIGHTBOX PARA IMÁGENES ==========
  if (typeof GLightbox !== "undefined") {
    const lightbox = GLightbox({
      selector: "[data-lightbox]",
      touchNavigation: true,
      loop: true,
    });
  }

  // Función auxiliar para formatear tiempo
  function formatTime(seconds) {
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return mins + ":" + (secs < 10 ? "0" : "") + secs;
  }
});
