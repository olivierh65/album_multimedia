(function ($, Drupal, drupalSettings) {
  $(document).ready(function () {
    $(document).foundation();

    var images_loaded = 0;
    var total_images = $(".lightgallery-image-1").length;
    var percent_slice = 100 / total_images;
    var current_percent = 0;

    // move info in the same div than the div contening image
    jQuery(".freewall-brick").each(function () {
      jQuery(this)
        .find(".freewall-info")
        .detach()
        .appendTo(jQuery(this).find(".lightgallery-content-1"));
    });

    $(".lightgallery-image-1")
      .one("load", function () {
        images_loaded++;
        current_percent += percent_slice;

        if (images_loaded == total_images) {
          console.log("Load freewall");
          var wall;
          wall = new Freewall("#freewall");
          wall.reset({
            selector: ".lightgallery-content-1",
            animate: true,
            cellW: 300,
            cellH: "auto",
            cacheSize: false,
            keepOrder: true,
            onResize: function () {
              wall.fitWidth();
            },
          });
          wall.container.find(".freewall-brick img").on("load", function () {
            wall.fitWidth();
          });
        }
        // $(".loader-wrapper").hide();
      })
      .each(function () {
        if (this.complete) {
          // $(this).load();
          $(this).trigger("load");
        }
      });

    // $(document).ready(function (e, settings) {
  });
})(jQuery, Drupal, drupalSettings);
