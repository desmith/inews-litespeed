

    jQuery(document).ready( function(){

        jQuery('ul.wp-submenu li:contains("Upgrade to Pro") a').attr( 'target', '_blank' ); // makes pro link open in a new tab
        jQuery('tr[data-plugin="charitable/charitable.php"]').addClass('wpcharitable-plugin-row').find('.proupgrade a').attr( 'target', '_blank' );

    });

