(function ($, Drupal, drupalSettings) {
    $(document).ready(function(){
        $(document).foundation();
    
        var images_loaded = 0;
        var total_images = $(".lightgallery-image-1").length;
        var percent_slice = 100 / total_images;
        var current_percent = 0;
    
        $(".lightgallery-image-1").one("load", function() {
            images_loaded++;
            current_percent += percent_slice;
             // move info in the same div than the div contening image
      jQuery('.freewall-brick').each(function() {
        jQuery(this).find('.freewall-info').detach().appendTo(jQuery(this).find('.lightgallery-content-1'))
      });
            if(images_loaded == total_images){
                $(".free-wall").pycsLayout({
                gutter: 4,
                idealHeight: 200,
                pictureContainer: ".lightgallery-image-1",
                });
                $(".loader-wrapper").hide();
            }
        }).each(function() {
            if(this.complete){
                // $(this).load();
                $(this).trigger('load');
            }
        });
    });

})(jQuery, Drupal, drupalSettings);
