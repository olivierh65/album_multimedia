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
        var w = $("#freewall").wookmark({
          align: "center",
          autoResize: true,
          ignoreInactiveItems: true,
          itemWidth: 250,
          fillEmptySpace: false,
          flexibleWidth: "40%",
          offset: 2,
          resizeDelay: 50,
        });
        // $(".loader-wrapper").hide();
      }
    })
    .each(function () {
      if (this.complete) {
        // $(this).load();
        $(this).trigger("load");
      }
    });
})(jQuery, Drupal, drupalSettings);
