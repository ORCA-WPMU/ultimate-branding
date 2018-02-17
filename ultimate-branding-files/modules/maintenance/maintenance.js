jQuery( document ).ready( function( $ ) {
    $('.ub-button.ub-add-site').on( 'click', function() {
        var select = $('select', $(this).closest('td'));
        var value = select.val();
        if ( -1 != value ) {
            var html = '<li id="site-'+value+'">';
            html += '<input type="hidden" name="'+$('#simple_options_sites_list').attr('name')+'" value="'+value+'">';
            html += $('option:selected', select ).text();
            html += ' <a href="#">'+ub_maintenance.remove+'</a>';
            html += '</li>';
            $('#ub_maintenance_selcted_sites').append(html);
            $('option:selected', select ).detach();
            ub_maintenance_bind();
        }
        return false;
    });
    function ub_maintenance_bind() {
        $('#ub_maintenance_selcted_sites a').on( 'click', function() {
            $(this).closest('li').detach();
            return false;
        });
    }
    ub_maintenance_bind();
});
