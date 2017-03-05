<?php

/**
 * @since      0.0.1
 * @package    Materialpool
 * @author     Frank Staude <frank@staude.net>
 */
class Materialpool_Settings
{

    public static function settings_init() {
        add_settings_section(
            'materialpool_email',
            __( 'Emailvorlagen', Materialpool::$textdomain ),
            array( 'Materialpool_Settings', 'email_section' ),
            'mpsettings'
        );
        register_setting( 'materialpool_email', 'materialpool_autor_email_vorlage' );
        register_setting( 'materialpool_email', 'materialpool_organisation_email_vorlage' );

    }


    public static function options_page() {
        add_submenu_page(
            'materialpool',
            _x('Materialpool Settings', Materialpool::$textdomain, 'Page Title' ),
            _x('Settings', Materialpool::$textdomain, 'Menu Title' ),
            'manage_options',
            'mpsettings',
            array( 'Materialpool_Settings', 'options_page_html' )
        );
    }

    public static function options_page_html() {
        global $settings;

        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        if ( isset( $_GET['settings-updated'] ) ) {
            add_settings_error( 'materialpool', 'materialpool', __( 'Settings Saved', Materialpool::$textdomain ), 'updated' );
        }
        settings_errors( 'materialpool' );
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'materialpool_email' );
                do_settings_sections( 'materialpool_email' );
                submit_button( 'Save Settings' );
                ?>
            </form>
        </div>
        <?php
    }

    function email_section( $args ) {
        ?>
        <p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'wporg' ); ?></p>
        <?php
    }

}