(function ($, Drupal, drupalSettings) {
  // $(document).ready(function (e, settings) {
    var wall;
    Drupal.behaviors.flexwall = {
      attach: function (context, settings) {
    console.log("Load freewall");
    wall = new Freewall("#freewall");
    wall.reset({
      selector: ".lightgallery-content-1",
      animate: true,
      cellW: 300,
      cellH: 'auto',
      cacheSize: false,
      keepOrder: true,
      onResize: function () {
        wall.fitWidth();
      },
    });
  
    wall.container.find('.freewall-brick img').on('load',function() {
      // move info in the same div than the div contening image
      jQuery('.freewall-brick').each(function() {
        jQuery(this).find('.freewall-info').detach().appendTo(jQuery(this).find('.lightgallery-content-1'))
      });
      wall.fitWidth();
    });
    

  }};

})(jQuery, Drupal, drupalSettings);
