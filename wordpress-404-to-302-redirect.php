<?php
/**
 * Plugin Name: WordPress 404 to 302 Redirect
 * Description: WordPress plugin to redirect 404 URLs to a specified URL.
 * Author: Infinitnet
 * Author URI: https://infinitnet.io/
 * Plugin URI: https://github.com/infinitnet/wordpress-404-to-302-redirect
 * Update URI: https://github.com/infinitnet/wordpress-404-to-302-redirect
 * Version: 1.0.1
 * License: GPLv3
 * Text Domain: wordpress-404-to-302-redirect
 */

function wp_404_to_302_menu() {
     add_options_page('404 to 302 Redirect Settings', '404 to 302 Redirect', 'manage_options', 'wp-404-to-302-redirect', 'wp_404_to_302_page');
 }
 
function wp_404_to_302_page() {
     if (isset($_POST['wp_404_to_302_nonce']) && wp_verify_nonce($_POST['wp_404_to_302_nonce'], 'wp_404_to_302_nonce_action')) {
         update_option('wp_404_to_302_enabled', isset($_POST['wp_404_to_302_enabled']) ? '1' : '0');
         update_option('wp_404_to_302_target', esc_url_raw($_POST['wp_404_to_302_target']));
     }
 
     ?>
     <div class="wrap">
       <h2>404 to 302 Redirect Settings</h2>
       <form method="post">
         <?php 
         wp_nonce_field( 'wp_404_to_302_nonce_action', 'wp_404_to_302_nonce' );
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

function wp_404_to_302_redirect() {
     if (get_option('wp_404_to_302_enabled') == '1') {
         if (is_404()) {
             $redirect_url = get_option('wp_404_to_302_target');
             if (!empty($redirect_url)) {
                 wp_safe_redirect($redirect_url, 302);
                 exit();
             }
         }
     }
}
 
add_action('admin_menu', 'wp_404_to_302_menu');
add_action('template_redirect', 'wp_404_to_302_redirect');