( function( $ ){

    /**
     * Binds navigation buttons/links in the "Five Star Rating" admin notice.
     */
    function fiveStarRatingNotice() {
        const steps = document.querySelectorAll(
            '.charitable-admin-notice-five-star-rating'
        );

        steps.forEach( ( stepEl ) => {
            const navigationEls = stepEl.querySelectorAll( '[data-navigate]' );

            if ( ! navigationEls ) {
                return;
            }

            navigationEls.forEach( ( navigationEl ) => {
                navigationEl.addEventListener( 'click', ( { target } ) => {
                    const step = target.dataset.navigate;
                    const stepToShow = document.querySelector(
                        `.charitable-admin-notice-five-star-rating[data-step="${ step }"]`
                    );
                    const stepsToHide = document.querySelectorAll(
                        `.charitable-admin-notice-five-star-rating:not([data-step="${ step }"])`
                    );

                    if ( stepToShow ) {
                        stepToShow.style.display = 'block';
                    }

                    if ( stepsToHide.length > 0 ) {
                        stepsToHide.forEach( ( stepToHide ) => {
                            stepToHide.style.display = 'none';
                        } );
                    }
                } );
            } );
        } );
    }

    $( document ).ready( function() {

        fiveStarRatingNotice();

        $( '.charitable-notice.is-dismissible' ).each( function(){
            var $el = $( this ), $button = $el.find( '.notice-dismiss' );

            $button.on( 'click', function( event ) {
                event.preventDefault();

                $.ajax({
                    type: "POST",
                    data: {
                        action : 'charitable_dismiss_notice',
                        notice : $el.data( 'notice' )
                    },
                    dataType: "json",
                    url: ajaxurl,
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function ( response ) {
                        if ( window.console && window.console.log ) {
                            console.log( response );
                        }
                    },
                    error: function( error ) {
                        if ( window.console && window.console.log ) {
                            console.log( error );
                        }
                    }
                }).fail(function ( response ) {
                    if ( window.console && window.console.log ) {
                        console.log( response );
                    }
                });
            });

            $el.css( 'position', 'relative' );
        });

        $( '.charitable-banner' ).each( function () {
            const banner = jQuery( this );
            const bannerId = banner.data( 'id' );
            const nonce = banner.data( 'nonce' );
            const lifespan = banner.data( 'lifespan' );

            banner.on( 'click', '.banner-dismiss, .charitable-banner-dismiss', () => {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'charitable_dismiss_admin_banner',
                        banner_id: bannerId,
                        nonce,
                        lifespan,
                    },
                    dataType: "json",
                    success() {
                        banner.slideUp( 'fast' );

                        // Remove previously set "seen" local storage.
                        const uid = 0;
                        const seenKey = `charitable-banner-${ bannerId }-seen-${ uid }`;
                        window.localStorage.removeItem( seenKey );
                    },
                } );
            } );
        } );

        $( '.charitable-campaign-list-banner' ).each( function () {
            const banner = jQuery( this );
            const containerRow = banner.closest('tr.child');
            const bannerId = banner.data( 'id' );
            const nonce = banner.data( 'nonce' );
            const lifespan = banner.data( 'lifespan' );

            banner.on( 'click', '.banner-dismiss, .charitable-close', () => {
                event.preventDefault();
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'charitable_dismiss_admin_list_banner',
                        banner_id: bannerId,
                        nonce,
                        lifespan,
                    },
                    dataType: "json",
                    success() {
                        containerRow.fadeOut( 'fast' );

                        // Remove previously set "seen" local storage.
                        const uid = 0;
                        const seenKey = `charitable-banner-${ bannerId }-seen-${ uid }`;
                        window.localStorage.removeItem( seenKey );
                    },
                } );
            } );
        } );

        // Move "Top of Page" promos to the top of content (before Help/Screen Options).
        const topOfPageNotice = jQuery( '.charitable-admin-banner-top-of-page' );

        if ( topOfPageNotice.length > 0 ) {
            const topOfPageNoticeEl = topOfPageNotice.detach();

            if ( jQuery( '#charitable-admin-header' ).length > 0 ) {
                jQuery( '#charitable-admin-header' ).before( topOfPageNoticeEl );
            } else {
                jQuery( '#wpbody-content' ).prepend( topOfPageNoticeEl );
            }

            const uid = 0;
            const noticeId = topOfPageNoticeEl.data( 'id' );
            const seenKey = `charitable-banner-${ noticeId }-seen-${ uid }`;

            if ( window.localStorage.getItem( seenKey ) ) {
                topOfPageNoticeEl.show();
            } else {
                setTimeout( () => {
                    window.localStorage.setItem( seenKey, true );
                    topOfPageNotice.slideDown();
                }, 1500 );
            }
	    }

        $( '.charitable-admin-notice-five-star-rating' ).each( function () {
            const notice = jQuery( this );
            const bannerId = notice.data( 'id' );

            notice.on( 'click', '.charitable-notice-dismiss', () => {
                $.ajax({
                    type: "POST",
                    url: ajaxurl,
                    data: {
                        action: 'charitable_dismiss_admin_five_star_rating',
                        banner_id: bannerId,
                    },
                    success() {
                        $('.notice-five-star-review').remove();

                    },
                } );
            } );
        } );

        // Move "Top of Page" promos to the top of content (before Help/Screen Options).
        const fiveStarReviewNotice = jQuery( '.notice-five-star-review' );

        if ( fiveStarReviewNotice.length > 0 ) {
            const fiveStarReviewNoticeEl = fiveStarReviewNotice.detach(),
                  adminHeader = jQuery( '#charitable-admin-header' );

            if ( jQuery('#screen-meta-links').length > 0 ) {
                jQuery( '#screen-meta-links' ).after( fiveStarReviewNoticeEl );
                fiveStarReviewNoticeEl.addClass('screen-meta-space').show();
            } else if ( adminHeader.length > 0 ) {
                adminHeader.after( fiveStarReviewNoticeEl );
                fiveStarReviewNoticeEl.show();
            } else if ( topOfPageNotice.length > 0 ) {
                topOfPageNotice.after( fiveStarReviewNoticeEl );
                fiveStarReviewNoticeEl.show();
            } else {
                jQuery( '#wpbody-content' ).prepend( fiveStarReviewNoticeEl );
                fiveStarReviewNoticeEl.show();
            }

        }

    });

} )( jQuery );