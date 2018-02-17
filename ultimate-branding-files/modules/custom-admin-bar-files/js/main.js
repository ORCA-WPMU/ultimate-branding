jQuery(function ($) {
    // General stuff
    $(document).on("click", ".handlediv, h3.hndle", function(event){
        event.preventDefault();
        $(this).closest(".postbox").toggleClass("closed");
    });

    $(document).on("click", ".hndle input", function(e){
        e.preventPropagation();
    });

    // Parent entry title
    function titleUrlSwitch () {

        if ( $(this).is(":checked") ){
            $(this).closest("td").find(".title_link-this_url").attr("disabled", false);
        } else{
            $(this).closest("td").find(".title_link-this_url").attr("disabled", true);
        }
    }

    titleUrlSwitch();
    $(document).on("change", '.title_link-this_url-switch', titleUrlSwitch);

    // handling parent menu link type
    $(document).on("change", "input[type='radio'].title_link-url-type", function(){
        var $url = $(this).closest("td").find(".title_link-this_url");
        $url.val($(this).data("value"));

        if( $(this).hasClass("title_link-this_url-switch") ){
            $url.removeAttr("disabled").attr("disabled", false);
        }else{
            $url.attr("disabled", true);
        }
    });
    // Sub menu preview
    function updateUrlPreview () {

        if( typeof $(this).attr("value") === "undefined"){
            return false;
        }

        var $parent = $(this).closest(".postbox"),
            type = $parent.find(".wdcab_last_wizard_step_url_type").val(),
            url = $parent.find(".wdcab_last_wizard_step_url").val();

        type = $parent.find("option[value='" + type +  "']").data("value");

        var preview = type + url;

        $parent.find(".wdcab_url_preview code").text(preview);
        return true;
    }
    updateUrlPreview();

    $(document).on("change", ".wdcab_last_wizard_step_url_type", updateUrlPreview);
    $(document).on("keyup", ".wdcab_last_wizard_step_url", updateUrlPreview);


    // Delete button
    $(document).on( "click", ".wdcab_step_delete", function (event) {
        event.preventDefault();
        var $parent = $(this).closest('.postbox');

        $parent.find(".inside").remove();
        $parent.toggle("highlight", function(){
          $(this).remove();
        });
    });

    // Adding a new admin bar
    $(document).on("click", "#ub_add_new_admin_bar", function(event){
        event.preventDefault();
       //var $main = $("#ub_main_admin_bar"),
       var $main = $("#ub_admin_bar_template");
           $new_bar = $($main.html());
           //console.log($main.html());return;
        $new_bar.find(".postbox").removeClass("closed");
       // remove content
       $new_bar.find(".hndle span").not(".ub_add_new_link_box").text(ub_admin_bar.new_bar);
       //$new_bar.find(".ub_add_new_link_box .hndle span").text(ub_admin_bar.save_before_adding);
       $new_bar.find(".ub_add_new_link_box .hndle span").text(ub_admin_bar.new_bar_sub_menu);

        /**
         * change name of inputs
         */
        var new_bars_count = parseInt( $(".parent_admin_bar_new").length, 10 );
        $new_bar.find("input, select").each(function(){
           var name = $(this).attr("name"),
               new_name = name.replace( "ub_ab_tmp[]", "ub_ab_new[" + new_bars_count + "]"),
               new_name = new_name.replace( "ub_ab_new[" + ( new_bars_count - 1) + "]", "ub_ab_new[" + new_bars_count + "]");
           $(this).attr("name", new_name);
        });

       $("#ub_admin_bar_menus").append($new_bar);
    });

    // Add deleted row to delete hidden input
    $(document).on("click", ".ub_delete_row", function(){
        var id = $(this).data("id"),
            $input = $("input[name='ub_ab_delete_links']"),
            ids = $input.val() !== "" ?  $input.val().split(",") : [];
        if( typeof id !== "undefined" && id !== ""  ){
            ids.push(id);
            $input.val( ids.join(", ") );
        }
    });


    // Sortable items
    $(".submenu-box-sortables").not(".not-sortable")
        .sortable({
            handle: ".hndle",
            axis: "y",
            update : function () {
                $(this).find(".postbox").each(function (index) {
                    $(this).find('.hndle .wdcab_step_count').html( index+1 );
                });
            }
        });

    // Make parent menus sortable
//    $("#ub_admin_bar_menus").sortable({
//        //appendTo: '#ub_admin_bar_menus',
//        //containment: "#ub_admin_bar_menus",
//        //forcePlaceholderSize: true,
//        axis: "y",
//        handle: ".hndle",
//        update : function () {
//            $(".parent_admin_bar").each(function (index) {
//                $(this).find('.ub_ad_main_order').html( index+1 );
//            });
//        }
//    });
    $(document).on("click", ".click_disabled, #wpadminbar a[href='#']", function(event){
            event.preventDefault();
    });

    var UB_Ordering = {
        children : function(hide){
            hide = typeof hide === "undefined" ? true : false;
            if( hide ){
                $("#ub_admin_bar_wrap ul#wp-admin-bar-root-default > li").css({
                    cursor : "move"
                }).find(".ab-sub-wrapper").css({
                    visibility : "hidden"
                });
            }else{
                $("#ub_admin_bar_wrap ul#wp-admin-bar-root-default > li").css({
                    cursor : "default"
                }).find(".ab-sub-wrapper").css({
                    visibility : "visible"
                });
            }

        },
        sortable : function( make ) {
            make = typeof make === "undefined" ? true : false;
            if( make ){


                $("#ub_admin_bar_wrap ul#wp-admin-bar-root-default a").addClass("click_disabled");
                $("#ub_admin_bar_wrap ul#wp-admin-bar-root-default").sortable({
                    axis: "x",
                    forceHelperSize: true,
                    forcePlaceholderSize: true,
                    distance : 2,
                    handle: "a",
                    tolerance: "intersect",
                    cursor: "move"
                }).sortable( "enable" );
            }else{
                $("#ub_admin_bar_wrap ul#wp-admin-bar-root-default a").removeClass("click_disabled");

                $("#ub_admin_bar_wrap ul#wp-admin-bar-root-default").sortable( "disable" );
            }

        },
        wiggle : function(wiggle) {
            wiggle = typeof wiggle === "undefined" ? true : false;
            var $el = $("#ub_admin_bar_wrap ul#wp-admin-bar-root-default > li");
            if( wiggle ){
                $el.ClassyWiggle("start", {
                    degrees: ['2', '4', '2', '0', '-2', '-4', '-2', '0'],
                    delay : 90
                });
            }else{
                $el.ClassyWiggle("stop");
            }
        },
        add_save_button : function(){
            $("#ub_admin_bar_save_ordering").remove();
            $("#wp-admin-bar-root-default").after("<button id='ub_admin_bar_save_ordering'></button>");
        },
        start : function(){
            this.children();
            this.sortable();
            this.wiggle();
            this.add_save_button();
        },
        stop : function(){
            this.children( false );
            this.sortable( false );
            this.wiggle( false );
            $("#ub_admin_bar_save_ordering").slideUp(100, function(){
                $(this).remove();
            });
        },
        save : function(){
            var self = this,
                $button = $( "#ub_admin_bar_save_ordering" );

            $button.attr("disabled", true).addClass("ub_loading");

            order = [];
            $("#ub_admin_bar_wrap #wp-admin-bar-root-default > li").each(function(){
                if( typeof this.id === "string" &&  this.is !== "" ){
                    order.push( this.id.replace( "wp-admin-bar-", "" ) );
                }
            });

            $.ajax({
               url      : ajaxurl,
               type     : "post",
               data     : {
                   action   : "ub_save_menu_ordering",
                   order    : order
               },
               success  : function( data ){
                   if( data.status ){
                       self.stop();
                       $button.remove();
                   }else{
                       $button.attr("disabled", false).removeClass("ub_loading");
                   }
               }
            });
        }
    };
    $("#ub_admin_bar_start_ordering").on("click", function( e ){
        e.preventDefault();
        UB_Ordering.start();
    });

    $(document).on("click", "#ub_admin_bar_save_ordering", function( e ){
        e.preventDefault();
        UB_Ordering.save();
    });

    $(document).on("click", ".ub_adminbar_use_icon", function(){
        var $this = $(this),
            $tr = $this.closest("table").find(".ub_adminbar_icon_tr");

        if( $this.is(":checked") ){
            $tr.slideDown();
        }else{
            $tr.slideUp();
        }
    })
});