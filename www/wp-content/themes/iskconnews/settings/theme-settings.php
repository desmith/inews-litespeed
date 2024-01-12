<?php
//Add theme settings page
add_action('admin_menu', 'isk_theme_settings');

function isk_theme_settings() {
  add_menu_page( "ISKCON News Theme Settings", "ISKCON News Theme Settings", "manage_options", "theme-panel", "theme_settings_page", null, 99);
}

//Display the form
function theme_settings_page()
{
  include(dirname(__FILE__) . DIRECTORY_SEPARATOR .'settings.php');
}

//All Fields
function display_theme_panel_fields()
{
  register_setting("isk_theme_section", "facebook_link");

  register_setting("isk_theme_section", "instra_link");

  register_setting("isk_theme_section", "twitter_link");

  register_setting("isk_theme_section", "footer_content");
	
  register_setting("isk_theme_section", "email_header");
  register_setting("isk_theme_section", "email_footer");
	
 
  add_settings_section("isk_theme_section", "FACT Theme Settings", null, "theme-options");

  add_settings_field("facebook_link", "Facebook Link", "facebook_link", "theme-options", "isk_theme_section");
  add_settings_field("instra_link", "Instragram Link", "instra_link", "theme-options", "isk_theme_section");
  add_settings_field("twitter_link", "Twitter Link", "twitter_link", "theme-options", "isk_theme_section");

  add_settings_field("footer_content", "Footer Content", "footer_content", "theme-options", "isk_theme_section");
	
  add_settings_field("email_header", "Email Template Header", "email_header", "theme-options", "isk_theme_section");  
  add_settings_field("email_footer", "Email Template Footer", "email_footer", "theme-options", "isk_theme_section");
}

add_action("admin_init", "display_theme_panel_fields");


/*
* Handling Fields
*/

//Social links
function facebook_link()
{
?>
  <div class="social-media">
      <input type="text" name="facebook_link" id="facebook_link" value="<?php echo get_option('facebook_link'); ?>" />
  </div>    
  <?php
}
function instra_link()
{
?>
  <div class="social-media">
      <input type="text" name="instra_link" id="instra_link" value="<?php echo get_option('instra_link'); ?>" />
  </div>    
  <?php
}

function twitter_link()
{
?>
  <div class="social-media">
      <input type="text" name="twitter_link" id="twitter_link" value="<?php echo get_option('twitter_link'); ?>" />
  </div>    
  <?php
}

//Footer Section
function footer_content()
  {
  ?>
    <div class="email-includes-section editorField">
        <?php 
          $footer_content = get_option('footer_content');
          $settings = array(
            'teeny' => true,
            'textarea_rows' => 8,
            'tabindex' => 1
        );
        wp_editor($footer_content, 'footer_content', $settings);
        ?>
    </div>    
    <?php
}



//Email Template Header Section
function email_header()
  {
  ?>
    <div class="email-includes-section editorField">
        <?php 
          $email_header = get_option('email_header');
          $settings = array(
            'teeny' => true,
            'textarea_rows' => 15,
            'tabindex' => 1
        );
        wp_editor($email_header, 'email_header', $settings);
        ?>
    </div>    
    <?php
}

//Email Template Footer Section
function email_footer()
  {
  ?>
    <div class="email-includes-section editorField">
        <?php 
          $email_footer = get_option('email_footer');
          $settings = array(
            'teeny'         => true,
            'textarea_rows' => 15,
            'tabindex'      => 1
          );
          wp_editor($email_footer, 'email_footer', $settings);
        ?>
    </div>    
    <?php
  }
