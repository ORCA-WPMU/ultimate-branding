jQuery(document).ready(function($){
    $(".ahc-remove_item").click(function () {
        if($('.ahc_existing_help_item').length <= 1) {
            alert('You must have at least one help item.');
        } else {
            $(this).parent().hide().remove();
            $(this).parent().find('.ahc_tab_title').val('');
        }

        return false;
    });

    /**
     * Scroll window to top when a help menu opens
     */
    $(document).on('screen:options:open', function(){
        $('html, body').animate({scrollTop : 0}, 'fast');
    });

});



