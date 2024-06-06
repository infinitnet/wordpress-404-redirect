<?php
/**
 * Plugin Name: WordPress 404 Redirect
 * Description: WordPress plugin to redirect 404 URLs to a specified URL.
 * Author: Infinitnet
 * Author URI: https://infinitnet.io/
 * Plugin URI: https://github.com/infinitnet/wordpress-404-redirect
 * Update URI: https://github.com/infinitnet/wordpress-404-redirect
 * Version: 2.0.1
 * License: GPLv3
 * Text Domain: wordpress-404-redirect
 */

function wp_404_redirect_menu() {
    add_options_page('404 Redirect Settings', '404 Redirect', 'manage_options', 'wp-404-redirect', 'wp_404_redirect_page');
}

function wp_404_redirect_page() {
    if (isset($_POST['wp_404_redirect_nonce']) && wp_verify_nonce($_POST['wp_404_redirect_nonce'], 'wp_404_redirect_nonce_action')) {
        update_option('wp_404_redirect_enabled', isset($_POST['wp_404_redirect_enabled']) ? '1' : '0');
        update_option('wp_404_redirect_target', esc_url_raw($_POST['wp_404_redirect_target']));
        update_option('wp_404_redirect_type', sanitize_text_field($_POST['wp_404_redirect_type']));
    }
    $redirect_types = array(
        301 => '301 (Moved Permanently)',
        308 => '308 (Permanent Redirect)',
        302 => '302 (Found)',
        303 => '303 (See Other)',
        307 => '307 (Temporary Redirect)'
    );
    ?>
    <div class="wrap">
        <h2>404 Redirect Settings</h2>
        <form method="post">
            <?php wp_nonce_field('wp_404_redirect_nonce_action', 'wp_404_redirect_nonce'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Redirect Target URL (Custom 404 Page)</th>
                    <td>
                        <input type="url" name="wp_404_redirect_target" value="<?php echo esc_url(get_option('wp_404_redirect_target')); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Redirect Type</th>
                    <td>
                        <select name="wp_404_redirect_type">
                            <?php
                            $selected_type = get_option('wp_404_redirect_type', 301);
                            foreach ($redirect_types as $type => $label) {
                                $selected = ($type == $selected_type) ? 'selected' : '';
                                echo '<option value="' . $type . '" ' . $selected . '>' . $label . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Enable Redirect</th>
                    <td>
                        <input type="checkbox" name="wp_404_redirect_enabled" value="1" <?php checked(get_option('wp_404_redirect_enabled'), '1'); ?> />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function wp_404_redirect_redirect() {
    if (get_option('wp_404_redirect_enabled') == '1' && is_404()) {
        $redirect_url = get_option('wp_404_redirect_target');
        if (!empty($redirect_url)) {
            $redirect_type = get_option('wp_404_redirect_type', 301);
            wp_safe_redirect($redirect_url, $redirect_type);
            exit();
        }
    }
}

add_action('admin_menu', 'wp_404_redirect_menu');
add_action('template_redirect', 'wp_404_redirect_redirect', 1);
