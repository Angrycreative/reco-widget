function recoInit() {
    var defaultDisplayCount = 2;
    var defaultDisplayHeight = 0;
    var fullDisplayHeight = 0;
    jQuery('#reco_reviews_list li').each(function(i){
        if (i < defaultDisplayCount) {
            defaultDisplayHeight += jQuery(this).outerHeight();
        }
        fullDisplayHeight += jQuery(this).outerHeight();
        return true;
    });
    
    jQuery('#reco_reviews_list').height(defaultDisplayHeight);
    
    jQuery('#toggle_displayed_reviews').click(function() {
        if (jQuery('#reco_reviews_list').height() == defaultDisplayHeight) {
            jQuery('#reco_reviews_list').animate({height: fullDisplayHeight}, 'fast', 'swing', function() {
                jQuery('#toggle_displayed_reviews span').html('Visa färre');
            });
        } else {
            jQuery('#reco_reviews_list').animate({height: defaultDisplayHeight}, 'fast', 'swing', function() {
                jQuery('#toggle_displayed_reviews span').html('Visa fler');
            });
        }
    });   
}