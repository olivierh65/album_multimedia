(function ($, Drupal, drupalSettings) {
  $(document).ready(function () {
    $(document).foundation();

    var images_loaded = 0;
    var total_images = $(".lightgallery-image-1").length;
    var percent_slice = 100 / total_images;
    var current_percent = 0;

    var wall = null;

    var option = {
      selector: ".lightgallery-content-1",
      animate: true,
      cellW: 300,
      cellH: "auto",
      cacheSize: false,
      keepOrder: true,
      onResize: function () {
        wall.fitWidth();
      },
    };

    /*
    $("#freewall").ready(function () {
      if (wall == null) {
        wall = new Freewall("#freewall");
        wall.reset(option);
      }
    });
*/
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
          if (wall == null) {
            wall = new Freewall("#freewall");
            wall.reset(option);
          }
          wall.fitWidth();
        }
        if (wall != null) {
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
  });
})(jQuery, Drupal, drupalSettings);
