(function ($, Drupal, drupalSettings) {
  $(document).foundation();

  var images_loaded = 0;
  var total_images = $(".lightgallery-image-1").length;
  var percent_slice = 100 / total_images;
  var current_percent = 0;

  $(".lightgallery-image-1")
    .one("load", function () {
      images_loaded++;
      current_percent += percent_slice;
      // move info in the same div than the div contening image
      jQuery(".freewall-brick").each(function () {
        jQuery(this)
          .find(".freewall-info")
          .detach()
          .appendTo(jQuery(this).find(".lightgallery-content-1"));
      });
      if (images_loaded == total_images) {
        var wall;
        Drupal.behaviors.flexwall = {
          attach: function (context, settings) {
            console.log("Load freewall");
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
          },
        };
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

  wall.container.find(".freewall-brick img").on("load", function () {
    // move info in the same div than the div contening image
    jQuery(".freewall-brick").each(function () {
      jQuery(this)
        .find(".freewall-info")
        .detach()
        .appendTo(jQuery(this).find(".lightgallery-content-1"));
    });
    wall.fitWidth();
  });
})(jQuery, Drupal, drupalSettings);
