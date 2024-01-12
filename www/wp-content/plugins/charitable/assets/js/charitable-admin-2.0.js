/* global charitable_builder, jconfirm, charitable_panel_switch, Choices, Charitable, CharitableCampaignEmbedWizard, wpCookies, tinyMCE, CharitableUtils, List */ // eslint-disable-line no-unused-vars

var CharitableAdminUI = window.CharitableAdminUI || ( function( document, window, $ ) {

	var s = {};

	var elements = {}; 

    var app = {

		settings: {
			clickWatch: false
		},

		init: function() {

			// charitable_panel_switch = true;
			s = this.settings;

			// Document ready.
			$( app.ready );

		},

		ready: function() {

			elements.$addNewCampaignButton = $( 'body.post-type-campaign .page-title-action' );

			// Bind all actions.
			app.bindUIActions();

            var urlParams = new URLSearchParams(window.location.search);

            if ( urlParams.has('create') && 'campaign' === urlParams.get('create') ) {
                app.newCampaignPopup();
                urlParams.delete('create');
                window.history.pushState({}, '', '/wp-admin/edit.php?' + urlParams.toString() );
            }

        },


        bindUIActions: function() {

            $('body.post-type-campaign').on( 'click', '.page-title-action', function( e ) {
                e.preventDefault();
                app.newCampaignPopup();
            } );
            $('body.post-type-campaign').on( 'click', '.charitable-campaign-list-banner a.button-link', function( e ) {
                e.preventDefault();
                app.campaignListBannerPopup();
            } );

            $('body.post-type-campaign').on( 'click', '.jconfirm-closeIcon', function( e ) { // eslint-disable-line no-unused-vars
                s.clickWatch = false;
            } );
            if ( s.clickWatch === false ) {
                $('body.post-type-campaign').on( 'click', 'input.campaign_name', function( e ) {
                    e.preventDefault();
                    $(this).select();
                    s.clickWatch = true;
                } );
            }

        },

        newCampaignPopup: function() {

            var admin_url = typeof charitable_admin.admin_url !== "undefined" ? charitable_admin.admin_url : '/wp-admin', // eslint-disable-line no-undef
                box_width = $(window).width() * .50;

            if ( box_width > 770 ) {
                box_width = 770;
            }

            $.confirm( {
                title: 'Create Campaign',
                content: '' +
                '<form id="create-campaign-form" method="POST" action="' + admin_url + 'admin.php?page=charitable-campaign-builder&view=template" class="formName">' +
                '<div class="form-group">' +
                '<label>Name:</label>' +
                '<input type="text" placeholder="Campaign Name" value="My New Campaign" name="campaign_name" class="name campaign_name form-control" required />' +
                '</div>' +
                '</form>',
                closeIcon: true,
                boxWidth: box_width + 'px',
                useBootstrap: false,
                type: 'create-campaign',
                animation: 'none',
                buttons: {
                    formSubmit: {
                        text: 'Create Campaign',
                        btnClass: 'btn-green',
                        action: function () {
                            var campaign_name = this.$content.find('.campaign_name').val().trim();
                            if ( ! campaign_name ){
                                $.alert('Please provide a valid campaign name.');
                                return false;
                            } else {
                                $('.jconfirm-buttons button.btn').html('Creating...');
                                $('#create-campaign-form').submit();
                                return false;
                            }
                        }
                    },
                },
                onContentReady: function () {

                }
            } );

        },

        campaignListBannerPopup: function() {

            var plugin_asset_dir = typeof charitable_admin.plugin_asset_dir !== "undefined" ? charitable_admin.plugin_asset_dir : '/wp-content/plugins/charitable/assets'; // eslint-disable-line no-undef

            $.confirm( {
                title: false,
                content: '' +
                '<div class="charitable-lite-pro-popup">' +
                    '<div class="charitable-lite-pro-popup-left" >' +
                        '<h1>The Ambassadors Extension is only available for Charitable Pro users.</h1>' +
                        '<h2>Harness the power of supporter networks and friends to reach more people and raise more money for your cause.</h2>' +
                        '<ul>' +
						'<li><p>Create a crowdfunding platform (similar to GoFundMe)</p></li>' +
                        '<li><p>Simplified fundraiser creation and management</p></li>' +
                        '<li><p>Let supporters fundraise together through our Teams feature</p></li>' +
                        '<li><p>Integrate with email marketing to follow up with campaign creators</p></li>' +
                        '<li><p>Give people a place to fundraise for their own cause</p></li>' +
                        '</ul>' +
                        '<a href="https://wpcharitable.com/lite-vs-pro/?utm_source=WordPress&utm_medium=Ambassadors+Campaign+Modal+Unlock&utm_campaign=WP+Charitable" target="_blank" class="charitable-lite-pro-popup-button">Unlock Peer-to-Peer Fundraising</a>' +
                        '<a href="https://wpcharitable.com/lite-vs-pro/?utm_source=WordPress&utm_medium=Ambassadors+Campaign+Modal+More&utm_campaign=WP+Charitable" target="_blank" class="charitable-lite-pro-popup-link">Or learn more about the Ambassadors extension &rarr;</a>' +
                    '</div>' +
                    '<div class="charitable-lite-pro-popup-right" >' +
                    '<img src="' + plugin_asset_dir + 'images/lite-to-pro/ambassador.png" alt="Charitable Ambassador Extension" >' +
                    '</img>' +
                '</div>',
                closeIcon: true,
                alignMiddle: true,
                boxWidth: '986px',
                useBootstrap: false,
                animation: 'none',
                buttons: false,
                type: 'lite-pro-ad',
                onContentReady: function () {

                }
            } );

        },

    };

    // Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) ); // eslint-disable-line no-undef

CharitableAdminUI.init();
