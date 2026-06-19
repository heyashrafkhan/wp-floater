<?php
/*
Plugin Name: WP Floater
Description: A WordPress plugin that adds a floating element to the dashboard menu with an option to add a phone number.
Version: 1.0
Author: Ashraf Khan
Text Domain: wp-floater
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function wp_floater_enqueue_styles() {
    if ( is_admin() ) {
        return;
    }

    wp_register_style( 'wp-floater-style', false );
    wp_enqueue_style( 'wp-floater-style' );

    $css = '.phone-floater{font-size:30px;color:#FFF;border-radius:50px;text-align:center;position:fixed;width:50px;height:50px;bottom:80px;right:20px;background-color:#fff;z-index:100000;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(0,0,0,.15);}.whatsapp-floater{font-size:30px;color:#FFF;border-radius:50px;text-align:center;position:fixed;width:50px;height:50px;bottom:20px;right:20px;background-color:#25d366;z-index:100000;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 10px rgba(0,0,0,.15);transition:background-color .2s ease-in-out;text-decoration:none;}.whatsapp-floater:hover,.whatsapp-floater:focus{background-color:#128C7E;outline:none;}.wp-floater a img{max-width:100%;height:auto;display:block;}';

    wp_add_inline_style( 'wp-floater-style', $css );
}
add_action( 'wp_enqueue_scripts', 'wp_floater_enqueue_styles' );

function wp_floater_add_menu_page() {
    add_menu_page(
        __( 'WP Floater Settings', 'wp-floater' ),
        __( 'WP Floater', 'wp-floater' ),
        'manage_options',
        'wp-floater-settings',
        'wp_floater_settings_page',
        'dashicons-format-chat',
        99
    );
}
add_action( 'admin_menu', 'wp_floater_add_menu_page' );

function wp_floater_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    $updated = false;

    if ( isset( $_POST['wp_floater_submit'] ) ) {
        check_admin_referer( 'wp_floater_save_settings', 'wp_floater_nonce' );

        $phone_number    = isset( $_POST['phone_number'] ) ? wp_strip_all_tags( wp_unslash( $_POST['phone_number'] ) ) : '';
        $whatsapp_number = isset( $_POST['whatsapp_number'] ) ? wp_strip_all_tags( wp_unslash( $_POST['whatsapp_number'] ) ) : '';

        $phone_number    = preg_replace( '/[^0-9+]/', '', $phone_number );
        $phone_number    = preg_replace( '/(?!^\+)\+/', '', $phone_number );
        $whatsapp_number = preg_replace( '/\D+/', '', $whatsapp_number );

        update_option( 'wp_floater_phone_number', $phone_number );
        update_option( 'wp_floater_whatsapp_number', $whatsapp_number );

        $updated = true;
    }

    if ( $updated ) {
        echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved successfully.', 'wp-floater' ) . '</p></div>';
    }

    $phone_number    = get_option( 'wp_floater_phone_number', '' );
    $whatsapp_number = get_option( 'wp_floater_whatsapp_number', '' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'WP Floater Settings', 'wp-floater' ); ?></h1>
        <p><?php esc_html_e( 'Note: For WhatsApp number, use the full phone number in international format. Omit any zeroes, brackets, or dashes.', 'wp-floater' ); ?></p>
        <p><?php esc_html_e( 'Example: Use 9XXXXXXXXXX. Do not use +971-(XXX)XXXXXXX.', 'wp-floater' ); ?></p>
        <form method="post" action="<?php echo esc_url( menu_page_url( 'wp-floater-settings', false ) ); ?>">
            <?php wp_nonce_field( 'wp_floater_save_settings', 'wp_floater_nonce' ); ?>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row"><label for="phone_number"><?php esc_html_e( 'Phone Number', 'wp-floater' ); ?></label></th>
                        <td><input type="text" id="phone_number" name="phone_number" value="<?php echo esc_attr( $phone_number ); ?>" class="regular-text" /></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="whatsapp_number"><?php esc_html_e( 'WhatsApp Phone Number', 'wp-floater' ); ?></label></th>
                        <td><input type="text" id="whatsapp_number" name="whatsapp_number" value="<?php echo esc_attr( $whatsapp_number ); ?>" class="regular-text" /></td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><button type="submit" name="wp_floater_submit" class="button button-primary"><?php esc_html_e( 'Save Changes', 'wp-floater' ); ?></button></p>
        </form>
    </div>
    <?php
}

function wp_floater_add_footer() {
    $phone_number    = get_option( 'wp_floater_phone_number', '' );
    $whatsapp_number = get_option( 'wp_floater_whatsapp_number', '' );

    if ( empty( $phone_number ) && empty( $whatsapp_number ) ) {
        return;
    }

    echo '<div class="wp-floater">';

    if ( ! empty( $phone_number ) ) {
        echo '<a class="phone-floater" href="' . esc_url( 'tel:' . $phone_number ) . '" aria-label="' . esc_attr__( 'Call us', 'wp-floater' ) . '"><img src="' . esc_url( plugin_dir_url( __FILE__ ) . 'phone.svg' ) . '" alt="' . esc_attr__( 'Call', 'wp-floater' ) . '"></a>';
    }

    if ( ! empty( $whatsapp_number ) ) {
        echo '<a class="whatsapp-floater" aria-label="' . esc_attr__( 'Chat on WhatsApp', 'wp-floater' ) . '" href="' . esc_url( 'https://wa.me/' . $whatsapp_number ) . '" target="_blank" rel="noopener noreferrer"><img src="' . esc_url( plugin_dir_url( __FILE__ ) . 'WA.svg' ) . '" alt="' . esc_attr__( 'Chat on WhatsApp', 'wp-floater' ) . '"></a>';
    }

    echo '</div>';
}
add_action( 'wp_footer', 'wp_floater_add_footer' );
