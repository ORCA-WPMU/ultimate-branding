jQuery(document).ready(function () {
    jQuery('.admin-panel-tips .apt-action a').click(function () {
        var parent1 = jQuery(this).parent();
        var parent2 = parent1.parent();
        var args = {
            action:  'ub_admin_panel_tips',
            id:      parent2.data('id'),
            nonce:   parent1.data('nonce'),
            user_id: parent2.data('user-id'),
            what:    parent1.data('what')
        };
        jQuery(this).parent().html( ub_admin_panel_tips.saving );
        jQuery.post(ajaxurl, args, function(response) {
            parent2.hide();
        });
        return false;
    });
});
