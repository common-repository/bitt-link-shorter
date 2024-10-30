( function ( $ )
{

    'use strict';
    /**
     * All of the code for your admin-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).load(function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.
     */
    $( function ()
    {

        $("input[name='wpbls_bitt_link_btn']").click( function () {

            var wpbls_current_link    = $("input[name='wpbls_current_link']").val();
            var wpbls_current_id      = $("input[name='wpbls_current_id']").val();
            var wpbls_bitt_individual = $("input[name='wpbls_bitt_individual']").val();
            var wpbls_genrate_link    = $("input[name='wpbls_genrate_bitt_link_shorter']").val();

            if( '' != wpbls_genrate_link ){

                $("input[name='wpbls_genrate_bitt_link_shorter']").show();
                $("input[name='wpbls_bitt_copy_link_btn']").show();
                $("input[name='wpbls_bitt_copy_link_p_btn']").show();
                $("input[name='wpbls_bitt_link_btn']").hide();

                return false;

            }

            $.ajax( {

                type: 'POST',
                async: false,
                url: ajaxurl,

                data: {
                    wpbls_current_link:    wpbls_current_link,
                    wpbls_current_id:      wpbls_current_id,
                    wpbls_bitt_individual: wpbls_bitt_individual,
                    action:                "wpbls_single_ajax"

                },

                success: function( responce ) {

                    $("input[name='wpbls_genrate_bitt_link_shorter']").val( responce );
                    $("input[name='wpbls_genrate_bitt_link_shorter']").show();
                    $("input[name='wpbls_bitt_copy_link_btn']").show();
                    $("input[name='wpbls_bitt_copy_link_p_btn']").show();
                    $("input[name='wpbls_bitt_link_btn']").hide();
                }

            } );
        } );

    } );

    $( function ()
    {

        $("input[name='wpbls_bluk_bitt_link_btn']").click( function () {

            var wpbls_bluk_bitt_link = $("textarea[name='wpbls_bluk_bitt_link']").val();

            if( '' == wpbls_bluk_bitt_link ){
                $(".bitt-link-shorter-dashboar .wpbls-error-notice").show();
                return false;
            }

            $.ajax( {

                type: 'POST',
                async: false,
                url: ajaxurl,

                data: {

                    wpbls_bluk_bitt_link: wpbls_bluk_bitt_link,
                    action: "wpbls_bluk_link_ajax"

                },

                success: function( responce ) {

                    $(".bitt-link-shorter-dashboar .wpbls-error-notice").hide();

                    $("textarea[name='wpbls_bluk_bitt_link']").val(' ');

                    location.reload();

                }

            } );

        } );

    } );


    $( function ()
    {

        $('#wpbls_bluk_datatable').DataTable();

    } );

    $( function ()
    {
        /* copy to clipboard function for single page e.g posts, pages, post type */
        $("input[name=wpbls_bitt_copy_link_p_btn]").click( function () {

            var copy_id = $(this).data("id");

            var copyText = document.getElementById("wpbls_bitt_copy_link_"+copy_id);

            copyText.select();

            document.execCommand("copy");

        } );

    } );

} )( jQuery );

/* copy to clipboard function for bitt link shorter setting page  */
function wpbls_bitt_copy_link_btn ( id )
{

    var copyText = document.getElementById("wpbls_bitt_copy_link_"+id);

    copyText.select();

    document.execCommand("copy");

}