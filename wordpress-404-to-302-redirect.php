<?php
/**
 * Plugin Name: WordPress 404 to 302 Redirect
 * Description: WordPress plugin to redirect 404 URLs to a specified URL.
 * Author: Infinitnet
 * Author URI: https://infinitnet.io/
 * Plugin URI: https://github.com/infinitnet/wordpress-404-to-302-redirect
 * Update URI: https://github.com/infinitnet/wordpress-404-to-302-redirect
 * Version: 1.0
 * License: GPLv3
 * Text Domain: wordpress-404-to-302-redirect
 */

function wp_404_to_302_menu() {
    add_options_page('404 to 302 Redirect Settings', '404 to 302 Redirect', 'manage_options', 'wp-404-to-302-redirect', 'wp_404_to_302_page');
}

function wp_404_to_302_page() {

    ?>
    <div class="wrap">
      <h2>404 to 302 Redirect Settings</h2>
      <form method="post" action="options.php">
        <?php 
        wp_nonce_field( 'wp_404_to_302_nonce_action', 'wp_404_to_302_nonce' );
        settings_fields('wp-404-to-302-redirect-group');
            do_settings_sections('wp-404-to-302-redirect-group');
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Redirect Target URL (Custom 404 Page)</th>
                    <td>
                        <input type="url" name="wp_404_to_302_target" value="<?php echo esc_url(get_option('wp_404_to_302_target')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                <th scope="row">Enable Redirect</th>
             <td>
                 <input type="checkbox" name="wp_404_to_302_enabled" value="1" <?php checked( get_option('wp_404_to_302_enabled'), 1 ); ?> />
             </td>
             </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
  
  }
  
function wp_404_to_302_settings() {

    check_admin_referer( 'wp_404_to_302_nonce_action', 'wp_404_to_302_nonce' ); 

    register_setting(
      'wp-404-to-302-redirect-group', 
      'wp_404_to_302_enabled',
      'wp_404_to_302_target',
      'wp_404_to_302_sanitize_target_url'
    );
  
  }
  
  function wp_404_to_302_sanitize_target_url($input) {

    if ( empty($input) ) {
        add_settings_error(
          'wp_404_to_302_target', 
          'empty_url',
          'Please enter a redirect URL.'
        );
        return get_option('wp_404_to_302_target');
    }
  
    if ( ! esc_url_raw($input) ) {
      add_settings_error(
        'wp_404_to_302_target',
        'invalid_url',
        'Please enter a valid redirect URL.'
      );
      return get_option('wp_404_to_302_target'); 
    }
  
    return esc_url_raw($input);
  
  }  

function wp_404_to_302_redirect() {
    if ( ! get_option('wp_404_to_302_enabled') ) {
        return;
      }

    if (is_404()) {
        $redirect_url = get_option('wp_404_to_302_target');
        if (!empty($redirect_url)) {
            wp_safe_redirect($redirect_url, 302);
            exit();
        }
    }
}

add_action('admin_menu', 'wp_404_to_302_menu');
add_action('admin_init', 'wp_404_to_302_settings');
add_action('template_redirect', 'wp_404_to_302_redirect');
