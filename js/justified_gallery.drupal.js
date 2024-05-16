(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.justified = {
        attach: function (context, settings) {
                $('#freewall').justifiedGallery({
                    selector: ".freewall-brick",
                    rowHeight: 100,
                    imgSelector: '.lightgallery-image-1',
                });

        }
    }

})(jQuery, Drupal, drupalSettings);
