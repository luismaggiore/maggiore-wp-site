jQuery(document).ready(function ($) {
  let mediaUploader;

  $(".mg-upload-image").on("click", function (e) {
    e.preventDefault();

    if (mediaUploader) {
      mediaUploader.open();
      return;
    }

    mediaUploader = wp.media({
      title: "Seleccionar imagen",
      button: { text: "Usar imagen" },
      multiple: false,
    });

    mediaUploader.on("select", function () {
      const attachment = mediaUploader
        .state()
        .get("selection")
        .first()
        .toJSON();
      $("#mg_caso_contratador_img").val(attachment.id);
      $("#mg_contratador_preview")
        .attr("src", attachment.sizes.thumbnail.url)
        .show();
    });

    mediaUploader.open();
  });

  $(".mg-remove-image").on("click", function () {
    $("#mg_caso_contratador_img").val("");
    $("#mg_contratador_preview").hide().attr("src", "");
  });
});
