jQuery(document).ready(function (e) {
  function o(e) {
    const o = {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#039;",
    };
    return e.replace(/[&<>"']/g, (e) => o[e]);
  }
  function t(e, o, t = 2e3) {
    const i = e.html(),
      n = e.css("background-color");
    (e.html(o),
      e.css("background-color", "#46b450"),
      setTimeout(function () {
        (e.html(i), e.css("background-color", n));
      }, t));
  }
  function i() {
    const o = [];
    (e(".mg-video-externo-item").each(function () {
      o.push(e(this).data("url"));
    }),
      e("#mg_portafolio_videos_externos_hidden").val(o.join("\n")),
      console.log("Hidden input actualizado:", o.length + " videos externos"));
  }
  (e("#mg_select_galeria").on("click", function (o) {
    (o.preventDefault(),
      console.log("Admin-bundle: Abriendo selector de imágenes..."));
    const i = wp.media({
      title: "Agregar imágenes a la galería",
      multiple: !0,
      library: { type: "image" },
      button: { text: "Agregar selección" },
    });
    (i.on("select", function () {
      console.log("Admin-bundle: Imágenes seleccionadas");
      const o = i.state().get("selection");
      let n = e("#mg_portafolio_galeria_input").val();
      ((n = n
        ? n
            .split(",")
            .map((e) => parseInt(e))
            .filter(Boolean)
        : []),
        console.log("IDs actuales de imágenes:", n));
      let l = 0;
      (o.each(function (o) {
        const t = o.get("id");
        if (!n.includes(t)) {
          n.push(t);
          let i = "";
          const s = o.get("sizes");
          s && s.thumbnail && s.thumbnail.url
            ? (i = s.thumbnail.url)
            : o.get("url")
              ? (i = o.get("url"))
              : o.get("icon")
                ? (i = o.get("icon"))
                : ((i =
                    'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="80" height="80"%3E%3Crect fill="%23ddd" width="80" height="80"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="%23999"%3ELoading...%3C/text%3E%3C/svg%3E'),
                  console.log("Haciendo fetch de attachment:", t),
                  o.fetch().done(function () {
                    const i = o.get("sizes"),
                      n =
                        i && i.thumbnail && i.thumbnail.url
                          ? i.thumbnail.url
                          : o.get("url");
                    n &&
                      e('.mg-thumb-wrapper[data-id="' + t + '"] img').attr(
                        "src",
                        n,
                      );
                  }));
          const a = `<div class="mg-thumb-wrapper" data-id="${t}" style="position: relative;">\n            <img src="${i}" style="width: 80px; height: auto; display: block; border:1px solid #ccc; padding:3px; border-radius:3px;" />\n            <button type="button" class="mg-remove-image" data-id="${t}" \n                    style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; line-height: 1;">×</button>\n          </div>`;
          (e("#mg_galeria_preview").append(a), l++);
        }
      }),
        e("#mg_portafolio_galeria_input").val(n.join(",")),
        console.log("✅ Imágenes agregadas:", l),
        console.log("✅ Total en galería:", n.length),
        l > 0 &&
          t(
            e("#mg_select_galeria"),
            '<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡' +
              l +
              " agregada(s)!",
          ));
    }),
      i.open());
  }),
    e("#mg_clear_galeria").on("click", function (o) {
      (o.preventDefault(),
        confirm(
          "¿Estás seguro de que quieres eliminar todas las imágenes de la galería?",
        ) &&
          (e("#mg_galeria_preview").html(""),
          e("#mg_portafolio_galeria_input").val(""),
          console.log("Galería de imágenes limpiada")));
    }),
    e(document).on("click", ".mg-remove-image", function (o) {
      o.preventDefault();
      const t = e(this).data("id");
      e(this).parent(".mg-thumb-wrapper").remove();
      let i = e("#mg_portafolio_galeria_input")
        .val()
        .split(",")
        .filter(Boolean);
      ((i = i.filter((e) => e != t)),
        e("#mg_portafolio_galeria_input").val(i.join(",")),
        console.log("Imagen removida:", t));
    }),
    e("#mg_select_videos").on("click", function (o) {
      (o.preventDefault(),
        console.log("Admin-bundle: Abriendo selector de videos..."));
      const i = 100,
        n = wp.media({
          title: "Agregar videos (máx. 100MB cada uno)",
          multiple: !0,
          library: { type: "video" },
          button: { text: "Agregar videos seleccionados" },
        });
      (n.on("select", function () {
        console.log("Admin-bundle: Videos seleccionados");
        const o = n.state().get("selection");
        let l = e("#mg_portafolio_videos_input").val();
        ((l = l
          ? l
              .split(",")
              .map((e) => parseInt(e))
              .filter(Boolean)
          : []),
          console.log("IDs de videos actuales:", l));
        let s = 0,
          a = 0;
        (o.each(function (o) {
          if (l.length >= 10)
            return (
              console.warn("⚠️ Límite de 10 videos alcanzado"),
              alert(
                "Límite de 10 videos alcanzado. No se agregarán más videos.",
              ),
              !1
            );
          const t = o.get("id"),
            n = o.get("filesizeInBytes") || 0,
            r = n ? (n / 1048576).toFixed(2) : 0;
          if (r > i)
            return (
              console.warn("⚠️ Video muy grande:", r + "MB (máx: " + i + "MB)"),
              void a++
            );
          if (!l.includes(t)) {
            l.push(t);
            const i = o.get("filename") || "Video";
            let n = "";
            const a = o.get("image");
            ((n = a && a.src ? a.src : o.get("icon") || ""),
              n ||
                (n =
                  'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="120" height="120" viewBox="0 0 24 24"%3E%3Crect fill="%23333" width="24" height="24"/%3E%3Cpath fill="%23fff" d="M8 5v14l11-7z"/%3E%3C/svg%3E'));
            const d = `<div class="mg-video-wrapper" data-id="${t}" style="position: relative; width: 120px;">\n            <div style="position: relative; width: 120px; height: 120px; border: 1px solid #ccc; border-radius: 3px; overflow: hidden; background: #000;">\n              <img src="${n}" style="width: 100%; height: 100%; object-fit: cover;" />\n              <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.7); border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">\n                <span class="dashicons dashicons-controls-play" style="color: white; font-size: 24px;"></span>\n              </div>\n            </div>\n            <div style="font-size: 10px; margin-top: 3px; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${i}">\n              ${i}\n            </div>\n            <div style="font-size: 9px; text-align: center; color: #666;">\n              ${r} MB\n            </div>\n            <button type="button" class="mg-remove-video" data-id="${t}" \n                    style="position: absolute; top: -5px; right: -5px; background: red; color: white; border: none; border-radius: 50%; width: 20px; height: 20px; cursor: pointer; font-size: 12px; line-height: 1;">×</button>\n          </div>`;
            (e("#mg_videos_preview").append(d), s++);
          }
        }),
          e("#mg_portafolio_videos_input").val(l.join(",")),
          console.log("✅ Videos agregados:", s),
          a > 0 &&
            (console.log("⚠️ Videos saltados (muy grandes):", a),
            alert(a + " video(s) fueron omitidos por exceder " + i + "MB")),
          console.log("✅ Total de videos:", l.length),
          s > 0 &&
            t(
              e("#mg_select_videos"),
              '<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡' +
                s +
                " video(s) agregado(s)!",
            ));
      }),
        n.open());
    }),
    e("#mg_clear_videos").on("click", function (o) {
      (o.preventDefault(),
        confirm(
          "¿Estás seguro de que quieres eliminar todos los videos de la galería?",
        ) &&
          (e("#mg_videos_preview").html(""),
          e("#mg_portafolio_videos_input").val(""),
          console.log("Galería de videos limpiada")));
    }),
    e(document).on("click", ".mg-remove-video", function (o) {
      o.preventDefault();
      const t = e(this).data("id");
      e(this).closest(".mg-video-wrapper").remove();
      let i = e("#mg_portafolio_videos_input").val().split(",").filter(Boolean);
      ((i = i.filter((e) => e != t)),
        e("#mg_portafolio_videos_input").val(i.join(",")),
        console.log("Video removido:", t));
    }),
    e("#mg_agregar_video_externo").on("click", function () {
      const n = e("#mg_video_externo_input").val().trim();
      if ("" === n) return void alert("Por favor ingresa una URL");
      const l = n.includes("youtube.com") || n.includes("youtu.be"),
        s = n.includes("vimeo.com");
      if (!l && !s)
        return void alert(
          "⚠️ Por favor ingresa una URL válida de YouTube o Vimeo",
        );
      let a = !1;
      if (
        (e(".mg-video-externo-item").each(function () {
          if (e(this).data("url") === n) return ((a = !0), !1);
        }),
        a)
      )
        return void alert("⚠️ Este video ya está en la lista");
      const r = (function (e) {
        const o = e.match(
          /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/,
        );
        if (o && o[1]) return o[1];
        const t = e.match(/vimeo\.com\/(\d+)/);
        return t && t[1] ? t[1] : null;
      })(n);
      let d = "",
        c = "",
        g = "";
      l
        ? ((c = "YouTube"),
          (g = "#FF0000"),
          (d = r
            ? "https://img.youtube.com/vi/" + r + "/mqdefault.jpg"
            : 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="320" height="180" viewBox="0 0 320 180"%3E%3Crect fill="%23FF0000" width="320" height="180"/%3E%3Cpath fill="%23fff" d="M140 90 L180 70 L180 110 Z"/%3E%3C/svg%3E'))
        : s &&
          ((c = "Vimeo"),
          (g = "#1AB7EA"),
          (d =
            'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="320" height="180" viewBox="0 0 320 180"%3E%3Crect fill="%231ab7ea" width="320" height="180"/%3E%3Ctext x="50%25" y="50%25" text-anchor="middle" dy=".3em" fill="white" font-size="30" font-family="Arial"%3EVimeo%3C/text%3E%3C/svg%3E'));
      const m =
          '<div class="mg-video-externo-item" data-url="' +
          o(n) +
          '" style="display: flex; gap: 10px; align-items: center; padding: 10px; background: #f9f9f9; border-radius: 4px; margin-bottom: 10px;">\x3c!-- Thumbnail --\x3e<div style="flex-shrink: 0; width: 120px; height: 68px; border-radius: 3px; overflow: hidden; background: #000;"><img src="' +
          d +
          '" style="width: 100%; height: 100%; object-fit: cover;" alt="' +
          c +
          ' thumbnail"></div>\x3c!-- Info --\x3e<div style="flex: 1; min-width: 0;"><div style="display: flex; align-items: center; gap: 5px; margin-bottom: 3px;"><span class="dashicons dashicons-video-alt3" style="color: ' +
          g +
          '; font-size: 16px;"></span><strong style="color: ' +
          g +
          ';">' +
          c +
          '</strong></div><div style="font-size: 12px; color: #666; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="' +
          o(n) +
          '">' +
          o(n) +
          '</div></div>\x3c!-- Botón eliminar --\x3e<button type="button" class="button mg-eliminar-video-externo" data-url="' +
          o(n) +
          '" style="flex-shrink: 0;"><span class="dashicons dashicons-trash" style="margin-top: 3px;"></span> Eliminar</button></div>',
        p = e("#mg_videos_externos_lista");
      (p.find("p").remove(),
        p.append(m),
        e("#mg_video_externo_input").val(""),
        i(),
        t(
          e("#mg_agregar_video_externo"),
          '<span class="dashicons dashicons-yes" style="margin-top: 3px;"></span> ¡Agregado!',
          1500,
        ),
        console.log("Video externo agregado:", n));
    }),
    e(document).on("click", ".mg-eliminar-video-externo", function () {
      const o = e(this).data("url");
      confirm("¿Estás seguro de eliminar este video?") &&
        (e(this)
          .closest(".mg-video-externo-item")
          .fadeOut(300, function () {
            (e(this).remove(),
              0 === e(".mg-video-externo-item").length &&
                e("#mg_videos_externos_lista").html(
                  '<p style="color: #666; font-style: italic;">No hay videos externos agregados. Usa el campo de arriba para agregar uno.</p>',
                ),
              i());
          }),
        console.log("Video externo eliminado:", o));
    }),
    e("#mg_video_externo_input").on("keypress", function (o) {
      13 === o.which &&
        (o.preventDefault(), e("#mg_agregar_video_externo").click());
    }),
    console.log("✅ Admin Media Bundle loaded successfully"),
    console.log(
      "📦 Includes: Image Gallery + Video Gallery + External Videos",
    ));
});
