function sw_clickAddNew() {
    jQuery('#holdingtext').css('display', 'none');
    dt = new Date().getTime();
    jQuery('#blanktable').clone()
        .appendTo('#entryholder').css('display', 'block').attr('id', 'swtc-' + dt)
        .find('input.deletecheck').val(dt).end()
        .find('input.find').attr('name', 'swtble[' + dt + '][find]').end()
        .find('input.domain').attr('name','swtble[' + dt + '][domain]').end()
        .find('input.case').attr('name','swtble[' + dt + '][ignorecase]').end()
        .find('input.replace').attr('name','swtble[' + dt + '][replace]').end()
        .find('select.admin_front').attr('name','swtble[' + dt + '][admin_front]');

    jQuery('div.handlediv').unbind('click').click(sw_toggleRule);
    jQuery("#entryholder").sortable('refresh');

    sw_headings();
    /**
     * animate to new element
     */
    el = jQuery( '#swtc-'+dt );
    jQuery('html, body').animate({
        scrollTop: el.offset().top
    }, 500, function() {
        jQuery( 'input.long.find', el ).focus()
    })
    return false;

}

function sw_toggleRule() {
    jQuery(this).parent().find('div.inside').slideToggle('slow');
}

function sw_sortables() {
    jQuery("#entryholder").sortable({	items: "div.postbox",
        revert: true,
        scroll:true,
        smooth:true,
        revert:true,
        containment:'#entryholder',
        opacity: 0.75,
        cursor:'move',
        tolerance: 'pointer'
    });
}

function sw_niceHeading() {
    tval = jQuery(this).val();
    jQuery(this).parents('div.postbox').find('h3.hndle span').html('Text Change : ' + tval);
}

function sw_headings() {
    jQuery('input.find').unbind('change').change(sw_niceHeading);
}

function sw_adminReady() {
    jQuery('#addnewtextchange').click(sw_clickAddNew);
    jQuery('div.handlediv').click(sw_toggleRule);
    sw_sortables();
    sw_headings();
}

jQuery(document).ready(sw_adminReady);
